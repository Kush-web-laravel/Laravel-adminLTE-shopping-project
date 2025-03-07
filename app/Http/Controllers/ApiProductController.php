<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;

class ApiProductController extends Controller
{
    //

    public function addProduct(Request $request)
    {
        $request->validate([
            'productName' => 'required',
            'productPrice' => 'required|numeric|gt:0',
            'productImage' => 'required|mimes:png,jpg,jpeg,gif',
            'productDetails' => 'required',
        ]);

        if($request->hasFile('productImage')){
            $file = $request->file('productImage');
            $fileName = time().''.$file->getClientOriginalName();
            $filePath = $file->storeAs('api/uploads/product_images', $fileName , 'public');
        }

        $product = new Product([
            'productName' => $request->productName,
            'productPrice' => $request->productPrice,
            'productImage' => $filePath,
            'productDetails' => $request->productDetails,
            'user_id'   => auth()->id()
        ]);

       
        if($product->save()){
            return response()->json([
                'status' => 'success',
                'status_code' =>  200,
                'message' => 'Product added successfully',
                'product' => $product
            ]);
        }else{
            return  response()->json([
                'status' => 'error',
                'status_code' =>  404,
                'message' => 'Product cannot be added.',
                'product' => $product
            ]);
        }
           
    }

    public function listProducts()
    {
        
        $products = Product::where('user_id',auth()->id())->get();

        if(!$products){
            return response()->json([
                'status' => 'error',
                'status_code' =>  404,
                'message' => 'No products found',
                'products' => $products
            ]);
        }else{
            return response()->json([
                'status' => 'success',
                'status_code' =>  200,
                'message' => 'Products found',
                'products' => $products,
            ]);
        }
        
    }

    public function showProduct(Request $request)
    {
        $id = $request->input('id');
        $product = Product::where('id',$id)->first();

        if($product->user_id !== auth()->id()){
            return response()->json([
                'status' => 'error',
                'status_code' =>  401,
                'message' => 'You are not authorized to view this product',
                'product' => $product
            ]);
        }else{
            if(!$product){
                return response()->json([
                    'status' => 'error',
                    'status_code' =>  404,
                    'message' => 'Product not found',
                    'product' => $product
                ]);
            }else{
                return response()->json([
                    'status' => 'success',
                    'status_code' =>  200,
                    'message' => 'Product found',
                    'product' => $product,

                ]);
            }
        }
    }

    public function updateProduct(Request $request)
    {
        $id = $request->input('id');
        $product = Product::find($id);

        if($product->user_id !== auth()->id()){
            return response()->json([
                'status' => 'error',
                'status_code' =>  401,
                'message' => 'You are not the owner of this product'
            ]);
        }else{
            if(!$product){
                return response()->json([
                    'status' => 'error',
                    'status_code' =>  404,
                    'message' => 'Product not found'
                ]);
            }else{
                $request->validate([
                    'productName' => 'required',
                    'productPrice' => 'required',
                    'productImage' => 'mimes:jpg,jpeg,png,gif',
                    'productDetails' => 'required',
                ]);

                $filePath = $product->productImage;
            
                if($request->hasFile('productImage')){
                    $file = $request->file('productImage');
                    $fileName = time().''.$file->getClientOriginalName();
                    $filePath = $file->storeAs('api/uploads/product_images', $fileName , 'public');
                }
        
                $product->update([
                    'productName' => $request->productName,
                    'productPrice' => $request->productPrice,
                    'productDetails' =>  $request->productDetails,
                    'productImage' => $filePath,
                ]);
        
            
                return response()->json([
                    'status' => 'success', 
                    'status_code' =>  200,
                    'message' => 'Product details updated successfully',
                    'product' => $product
                ]);
            }   
        }
      
    }

    public function deleteProduct(Request $request)
    {
        $id = $request->input('id');
        $product = Product::find($id);
        if(!$product){
            
            return response()->json([
                'status' => 'error',
                'status_code' =>  404,
                'message' => 'Product not found',
                'product' => $product
            ]);
          
        }else{
            if($product->user_id !== auth()->id()){
                return response()->json([
                    'status' => 'error',
                    'status_code' =>  401,
                    'message' => 'You are not authorized to delete this product',
                    'product' => $product
                ]);
            }else{
                $product->delete();
                return response()->json([
                    'status' => 'success',   
                    'status_code' =>  200,
                    'message' => 'Product deleted successfully',
                    'product' => $product
                ]);
            }
           
        }
    }

}
