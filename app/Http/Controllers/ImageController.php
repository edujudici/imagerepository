<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Api\Image\ImageInterface;
use App\Http\Requests\ImageFormRequest;

class ImageController extends Controller
{

	protected $imageI;
    
    public function __construct(ImageInterface $imageI) {
        $this->imageI = $imageI;
    }

    public function index($id) {
        $model = $this->imageI->getAll($id);
        return view('images', compact('model'));
    }
    
    public function save(ImageFormRequest $req) {   
        return $this->imageI->save($req);
    }

    public function delete(Request $req) {
        return $this->imageI->delete($req->id);
    }

    public function link($id) {
        $image = $this->imageI->findById($id);
        if ($image)
            return url($image->img_path);
    }
}
