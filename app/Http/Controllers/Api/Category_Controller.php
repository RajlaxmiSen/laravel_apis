<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;

class Category_Controller extends Controller
{
    public $response;
    public function __construct()
    {
        $this->response = [
            'response' => 1,
            'success' => 0,
            'message' => 'Invalid Request',
        ];

        //$this->middleware('auth:api', ['except' => ['login']]);
    }

    public function getCategories(){
        $category_array = [];
        $categories = Category::all()->pluck('name','id');
        foreach($categories as $key => $category){
            array_push($category_array,array("id" => $key, "value" => $category));
        }
        if(isset($categories)){
            $this->response['response'] = 1;
            $this->response['success'] = 1;
            $this->response['message'] = "Categories fetched!!";
            $this->response['data'] =  $category_array;
        }
        return response()->json($this->response);
    }
}
