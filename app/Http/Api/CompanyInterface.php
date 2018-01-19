<?php

namespace App\Http\Api\Company;

interface CompanyInterface {
    public function getAll();
    public function save($request);
    public function delete($companyId);        
    public function findById($companyId);        
    public function findByToken($token);        
}