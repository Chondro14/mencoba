<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    //
    public function all(Request $request){
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('id');
        $show_product = $request->input('show_product');

        if($id){
            $product = ProductCategory::with(['products'])->find($id);
            if($product){
                return ResponseFormatter::success($product,'Data produk kategori berhasil');

            }
            else{
                return ResponseFormatter::error(null,'Data kategori tidak ada',404);
            }
        }

        $category = ProductCategory::query();
        if($category){
            $category->where('name','like','%'.$name.'%');
        }

        if($show_product){
            $category->with('products');
        }
        return ResponseFormatter::success($category->paginate($limit),'Data kategori berhasil diambil');
    }
}
