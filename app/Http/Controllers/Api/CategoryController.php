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
            $imageRead = $manager->read($image);
            $imageRead->scale(width: $size);
            $path=public_path('upload/'.$fileSave);
            $imageRead->toWebp()->save($path);
        }
        $inputs["image"]= $imageName;
        $category = Categories::create($inputs);
        return response()->json($category,201,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
    /**
     * @OA\Get(
     *     tags={"Category"},
     *     path="/api/categories/{id}",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ідентифікатор категорії",
     *         required=true,
     *         @OA\Schema(
     *             type="number",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(response="200", description="List Categories."),
     * @OA\Response(
     *    response=404,
     *    description="Wrong id",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong Category Id has been sent. Pls try another one.")
     *        )
     *     )
     * )
     */
    public function getById($id) {
        $category = Categories::findOrFail($id);
        return response()->json($category,200, ['Charset' => 'utf-8']);
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ідентифікатор категорії",
     *         required=true,
     *         @OA\Schema(
     *             type="number",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успішне видалення категорії"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Категорії не знайдено"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Не авторизований"
     *     )
     * )
     */
    public function delete($id) {
        $category = Categories::findOrFail($id);
        $sizes = [50,150,300,600,1200];
        foreach ($sizes as $size) {
            $fileSave = $size."_".$category->image;
            $path=public_path('upload/'.$fileSave);
            if(file_exists($path))
                unlink($path);
        }
        $category->delete();
        return response()->json("",200, ['Charset' => 'utf-8']);
    }
    public function edit($id, Request $request) {
        $category = Categories::findOrFail($id);
        $imageName=$category->image;
        $inputs = $request->all();
        if($request->hasFile("image")) {
            $image = $request->file("image");
            $imageName = uniqid() . ".webp";
            $sizes = [50, 150, 300, 600, 1200];
            // create image manager with desired driver
            $manager = new ImageManager(new Driver());
            foreach ($sizes as $size) {
                $fileSave = $size . "_" . $imageName;
                $imageRead = $manager->read($image);
                $imageRead->scale(width: $size);
                $path = public_path('upload/' . $fileSave);
                $imageRead->toWebp()->save($path);
                $removeImage = public_path('upload/'.$size."_". $category->image);
                if(file_exists($removeImage))
                    unlink($removeImage);
            }
        }
        $inputs["image"]= $imageName;
        $category->update($inputs);
        return response()->json($category,200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
