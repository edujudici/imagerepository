<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Api\Company\CompanyInterface;
use App\Http\Requests\CompanyFormRequest;

class HomeController extends Controller
{

    protected $companyI;
    
    public function __construct(CompanyInterface $companyI) {
        //$this->middleware('auth');
        $this->companyI = $companyI;
    }

    public function index() {
        $model = $this->companyI->getAll();               
        return view('home', compact('model'));
    }

    public function save(CompanyFormRequest $req) {
        return $this->companyI->save($req);
    }

    public function delete(Request $req) {
        return $this->companyI->delete($req->id);
    }
}
