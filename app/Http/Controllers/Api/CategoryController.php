<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CategoryController extends Controller
{
    public function getList()
    {
        $data = Categories::all();
//        $data = [
//            ['id' => 1, 'name' => 'Коти'],
//            ['id' => 2, 'name' => 'Собачка'],
//            ['id' => 3, 'name' => 'Мишка'],
//        ];
        return response()->json($data)
        ->header('Content-Type','application/json; charset-utf-8');
    }
    public function create(Request $request) {
        $inputs = $request->all();
        $image = $request->file("image");
        $imageName = uniqid().".webp";
        $sizes = [50,150,300,600,1200];
        // create image manager with desired driver
        $manager = new ImageManager(new Driver());

        foreach ($sizes as $size) {
            $fileSave = $size."_".$imageName;
            // read image from file system
            $imageRead = $manager->read($image);
            // resize image proportionally to 300px width
            $imageRead->scale(width: $size);
            // save modified image in new format
            $path=public_path('upload/'.$fileSave);
            $imageRead->toWebp()->save($path);
        }
        $inputs["image"]= $imageName;
        $category = Categories::create($inputs);
        return response()->json($category,201,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
