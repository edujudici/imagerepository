<?php

namespace App\Http\Api\Image;

interface ImageInterface {
    public function getAll($companyId);
    public function getAllCount($companyId);
    public function findById($imageId, $token);
    public function save($request);
    public function delete($imageId);    
}