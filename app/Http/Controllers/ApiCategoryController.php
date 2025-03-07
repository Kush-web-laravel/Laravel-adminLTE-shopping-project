<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;

class ApiCategoryController extends Controller
{
    //
    public function create(Request $request)
    {

        
        $data = json_decode($request->input('data'), true);
        $rules = [
            'category_name'                     =>      'required|string|max:20',
            'subcategories'                     =>      'required|array',
            'subcategories.*.name'              =>      'required|string|max:25',
            'subcategories.*.price'             =>      'required|numeric|min:1'
        ];

        $validator = Validator::make($data, $rules);

        if($validator->passes()){
            $category = Category::create([
                'name' =>  $data['category_name'],
               ]);
        
               foreach($data['subcategories'] as $subcategory){
                $category->subcategories()->create([
                    'name' => $subcategory['name'],
                    'price' =>  $subcategory['price'],
                ]);
        
                $responseData = [
                    'category' =>  $category,
                    'subcategories' =>  $category->subcategories,
                ];
        
                return response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Category created successfully',
                    'data' =>  $responseData,
                ]);
            }
        }   
      
    }

    public function fetchAll()
    {
        $categories =  Category::with('subcategories')->get();

        $responseData = [
            'category' =>  $categories,
        ];
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Categories fetched successfully',
            'data' => $responseData
        ]);
    }

    public function show(Request $request)
    {
        $id = $request->query('q');

        $category = Category::with('subcategories')->find($id);
        
        $responseData = [
            'category' =>  $category,
        ];
        if($category){
            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Category retreived successfully',
                'data' =>  $responseData,
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Category not found',
                'data' => null
            ]);
        }
    }

    public function update(Request  $request)
    {

        $id = $request->query('q');

        $data = json_decode($request->input('data'), true);

        $rules = [
            'category_name'                    =>      'required|string|max:20',
            'subcategories'                     =>      'required|array',
            'subcategories.*.id'                =>      'nullable|integer|exists:subcategories,id',
            'subcategories.*.name'              =>      'required|string|max:25',
            'subcategories.*.price'             =>      'required|numeric|min:1'
        ];

        $validator = Validator::make($data, $rules);

        
        
            $category =  Category::find($id);
            if($category){
                if($validator->passes()){
                    $category->update([
                        'name' => $data['category_name'],
                    ]);
        
                    foreach($data['subcategories'] as $subcategory){
                        if(isset($subcategory['id'])){
                            $category->subcategories()->where('id', $subcategory['id'])->update([
                                'name' => $subcategory['name'],
                                'price' => $subcategory['price']
                            ]);
                        }else{
                            $category->subcategories()->create([
                                'name' => $subcategory['name'],
                                'price' => $subcategory['price']
                            ]);
                        }
                    }
        
                    $responseData = [
                        'category' =>  $category,
                        'subcategories' =>  $category->subcategories,
                    ];
        
                    return response()->json([
                        'status' => 'success',
                        'status_code' => 200,
                        'message' => 'Category updated successfully',
                        'data' =>  $responseData,
                    ]);
                }else{
                    return response()->json([
                        'status' => 'error',
                        'status_code' => 404,
                        'message' =>  'Category not found',
                        'data' => null
                    ]);
                }
            }
        
    }

    public function destroy(Request $request)
    {
        $id = $request->query('q');

        $category = Category::find($id);

        if($category){

            $category->delete();
            $category->subcategories()->delete();

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Category deleted successfully',
                'data' => null
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' =>  'Category not found',
                'data' => null
            ]);
        }
    }
}
