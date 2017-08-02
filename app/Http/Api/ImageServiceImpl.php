<?php

namespace App\Http\Api\Image;

use Intervention\Image\ImageManagerStatic as ImageIntervention;
use App\Http\Api\Image\ImageInterface;
use App\Http\Api\Company\CompanyInterface;
use App\Image;

class ImageServiceImpl implements ImageInterface {

    protected $image; 

    function __construct(Image $image) {
        $this->image = $image;
    }

    public function getAll($companyId) {
        $companyI = app(CompanyInterface::class);
    	$data['company'] = $companyI->findById($companyId);
        $data['images'] = $this->image->where('com_id', $companyId)->get();
        return $data;
    }

    public function getAllCount($companyId) {
        return $this->image->where('com_id', $companyId)->count();        
    }

    public function save($request) {

        $companyI = app(CompanyInterface::class);
        $company = $companyI->findById($request->companyId);        

    	$files = $request->file('images');
        foreach ($files as $key => $value) {
            
            $imageRealPath = $value->getRealPath();
            $name = str_random(40).'.'.$value->getClientOriginalExtension(); //$value->getClientOriginalName();
            
            //image intervention
            $img = ImageIntervention::make($imageRealPath);
            //$img->resize(600, 600);
            
            $path = 'images/'.$company->com_token.'/'.$name;
            $img->save(public_path($path));
            
            $image = new Image;            
            $image->img_path = $path;
            $image->com_id = $company->com_id;
            $image->save();
        }
        
        $images = $this->getAll($image->com_id);
        return response()->api($images);
    }

    public function delete($imageId) {

        $image = $this->image->find($imageId);
        if (!$image) {
            return response()->api($image, false, 'Imagem não encontrada.');
        }

        array_map('unlink', glob(public_path($image->img_path)));
        $image->delete();

        return response()->api($image);
    }
}