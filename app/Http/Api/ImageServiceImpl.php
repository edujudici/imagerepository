<?php

namespace App\Http\Api\Image;

use Intervention\Image\ImageManagerStatic as ImageIntervention;
use App\Http\Api\Image\ImageInterface;
use App\Http\Api\Company\CompanyInterface;
use App\Image;

class ImageServiceImpl implements ImageInterface {

    const RANDOM_NAME = 40;
    const IMAGE_SIZE = 900;

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

    public function findById($imageId, $token) {

        $this->validToken($token);

        $image = $this->image->find($imageId);
        return $image;
    }

    public function save($request) {

        $companyI = app(CompanyInterface::class);
        $company = $companyI->findById($request->companyId);        

    	$files = $request->file('images');
        foreach ($files as $key => $value) {
            
            $imageRealPath = $value->getRealPath();
            $name = str_random(self::RANDOM_NAME).'.'.$value->getClientOriginalExtension();
            
            $img = ImageIntervention::make($imageRealPath);

            if ($img->width() > $img->height()) {

                $img->resize(self::IMAGE_SIZE, null, function($r) {
                    $r->aspectRatio();
                });

            } else {

                $img->resize(null, self::IMAGE_SIZE, function($r) {
                    $r->aspectRatio();
                });
            }

            $path = 'images/'.$company->com_token.DIRECTORY_SEPARATOR.$name;
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

    private function validToken($token) {
        $companyI = app(CompanyInterface::class);
        $company = $companyI->findByToken($token);
        if (!$company)
            abort(403, 'Token inválido');
    }
}