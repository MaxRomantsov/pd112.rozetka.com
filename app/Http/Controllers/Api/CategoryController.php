<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getList()
    {
        $data = [
            ['id' => 1, 'name' => 'Коти'],
            ['id' => 2, 'name' => 'Собаки'],
            ['id' => 3, 'name' => 'Мишки']
        ];
        return response()->json($data)
        ->header('Content-Type','application/json; charset-utf-8');
    }
}
