<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Helpers\CrudHelper;

class CategoryController extends Controller
{
    // Fetch all categories and subcategories
    public function index()
    {
        return view('category');
    }

    public function store(Request $request) {
        $request->validate([
            'category_name' => 'required',
            'sub_category_name.*' => 'required',
            'sub_category_price.*' => 'required|numeric',
        ]);

        // Create Category
        $category = Category::create(['name' => $request->category_name]);

        // Create Subcategories
        foreach ($request->sub_category_name as $key => $subCategoryName) {
            Subcategory::create([
                'parent_id' => $category->id,
                'name' => $subCategoryName,
                'price' => $request->sub_category_price[$key]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Category and subcategories created successfully',
        ]);
    }

    // Fetch single category for editing
    public function fetchCategories(Request $request)
    {
        $perpage = $request->input('per_page', 7);
        $category = Category::with('subcategories')->paginate($perpage);
        return response()->json([
            'status' => 'success',
            'categories' => $category->items(),
            'current_page' => $category->currentPage(),
            'last_page' => $category->lastPage(),
            'total' => $category->total()
        ]);
    }

    public function edit($id)
    {
        $category = Category::with('subcategories')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'category' => $category
        ]);
    }

    public function update(Request $request, $id) {

        $category =  Category::findOrFail($id);
        
        $request->validate([
            'category_name' => 'required',
            'existing_sub_category_name.*' => 'required',
            'existing_sub_category_price.*' => 'required|numeric',
            'new_sub_category_name.*' => 'sometimes|required',
            'new_sub_category_price.*' => 'sometimes|required|numeric',
        ]);
    
        // Update Category
        $category->update(['name' => $request->category_name]);
    
        
        if ($request->has('existing_subcategory_ids')) {
            foreach ($request->existing_subcategory_ids as $key => $id) {
                $subcategory = Subcategory::findOrFail($id);
                $subcategory->update([
                    'name' => $request->existing_sub_category_name[$key],
                    'price' => $request->existing_sub_category_price[$key]
                ]);
            }
            
        }
   
        if ($request->has('sub_category_name')) {
            foreach ($request->sub_category_name as $key => $subCategoryName) {
                
                    Subcategory::create([
                        'name' => $subCategoryName,
                        'price' => $request->sub_category_price[$key],
                        'parent_id' => $category->id,
                    ]);
                
            }
        }
        // Delete subcategories that were marked for removal
        if ($request->has('delete_subcategory_ids')) {
            Subcategory::whereIn('id', $request->delete_subcategory_ids)->delete();
        }
    
        return response()->json(['status' => 'success','message' => 'Category updated successfully!']);
    }
    // Delete category and subcategories
    public function delete($id)
    {
        $category = Category::findOrFail($id);

        if($category){
            $category->delete();
            $category->subcategories()->delete();
            return response()->json(['status' => 'success', 'message' => 'Category deleted successfully']);
        }
            return response()->json(['status' => 'error', 'message' => 'Failed to delete category']);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        $categories = Category::with(['subcategories' => function($q) use ($query, $minPrice, $maxPrice) {
            if ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            }
            if ($minPrice !== null) {
                $q->where('price', '>=', $minPrice);
            }
            if ($maxPrice !== null) {
                $q->where('price', '<=', $maxPrice);
            }
        }])
        ->where('name', 'LIKE', "%{$query}%")
        ->orWhereHas('subcategories', function($q) use ($query, $minPrice, $maxPrice) {
            if ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            }
            if ($minPrice !== null) {
                $q->where('price', '>=', $minPrice);
            }
            if ($maxPrice !== null) {
                $q->where('price', '<=', $maxPrice);
            }
        })
        ->get();
    
        return response()->json([
            'status' => 'success',
            'categories' => $categories
        ]);
                         
    }

    public function download()
    {
        $categories = Category::with('subcategories')->get();

        return response()->json([
            'status' => 'success',
            'categories' => $categories
        ]);
    }
}