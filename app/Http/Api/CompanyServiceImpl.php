<?php

namespace App\Http\Api\Company;

use App\Http\Api\Company\CompanyInterface;
use App\Http\Api\Image\ImageInterface;
use App\Company;
use App\Image;

class CompanyServiceImpl implements CompanyInterface {

    protected $company;    

    function __construct(Company $company) {
        $this->company = $company;
    }

    public function getAll() {

    	$companies = $this->company->all();
        $imageI = app(ImageInterface::class);

    	foreach ($companies as $key => $value) {
    		$value->photos = $imageI->getAllCount($value->com_id);	
    	}

        return $companies;
    }

    public function save($request) {

    	$company = $this->company->find($request->id);

        if (!$company) {
            $company = $this->company;
            $company->com_token = substr( md5(rand()), 0, 16);

            mkdir(public_path('images/'.$company->com_token), 0777, true);
        }

        $company->com_description = $request->description;
    	$company->save();

        return response()->api($company);
    }

    public function delete($companyId) {

        $company = $this->company->find($companyId);
        if (!$company) {
            return response()->api($company, false, 'Empresa não encontrada.');            
        }

        Image::where('com_id',$companyId)->delete();
        
        $company->delete();

        $dirname = public_path('images/'.$company->com_token);
        array_map('unlink', glob("$dirname/*.*"));
        rmdir($dirname);

        return response()->api($company);    	
    }

    public function findById($companyId) {
        return $this->company->find($companyId);
    }
}