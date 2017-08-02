<?php

namespace App\Http\Api\Image;

interface ImageInterface {
    public function getAll($companyId);
    public function getAllCount($companyId);
    public function save($request);
    public function delete($imageId);    
}