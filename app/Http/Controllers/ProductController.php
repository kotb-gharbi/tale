<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function AddProduct(Request $request){

        //input validation
        $data = $request->validate([
            'description' => 'required',
            'price' => 'required',
            'image' => ['required','mimes:png,jpeg,jpg','max:2048']
        ]);

        //images path
        $file_path = public_path('uploads');

        //image inserting
        if($request->hasFile('image')){
            $file = $request->file('image');
            $file_name = time() . '_' . $file->getClientOriginalName();
            $file->move($file_path,$file_name);
            $data = $request->except('image');
            $data['image'] = $file_name;

            //create a new product in the database
            $product = Product::create($data);

            if ($product) {
                return response()->json(["message" => "Product added successfully"]);
            } else {
                return response()->json(["message" => "Failed to add product"]);
            }
        }

        return response()->json(["message" => "Image upload failed"]);
    }

    public function UpdateProduct(Request $request,int $id){

        $data = $request->validate([
            'description' => 'required',
            'price' => ['required','numeric'],
            'image' => ['mimes:png,jpeg,jpg','max:2048']
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json(["message" => "Product not found"]);
        }

        $file_path = public_path('uploads');

        //store updated image
        if($request->hasFile('image')){
            $file = $request->file('image');
            $file_name = time() . '_' . $file->getClientOriginalName();
            $file->move($file_path,$file_name);
            $data = $request->except('image');
            $data['image'] = $file_name;

            //delete old image from uploads directory
            if(file_exists($file_path . '/' . $product->image)){
                unlink($file_path . '/' . $product->image);
            }

            //update product data
            $product->update($data);

            return response()->json(["message" => "product updated successfully"]);
        }
    }

    public function DeleteProduct(int $id){

        $product = Product::find($id);

        if(!$product){
            return response()->json(["message" => "Product not found"]);   
        }

        $file_path = public_path('uploads');

        //deletes image from uploads directory
        if(file_exists($file_path . '/' . $product->image)){
            unlink($file_path . '/' . $product->image);
        }

        //deletes product from database
        $product->delete();

        return response()->json(["message" => "Product deleted successfully"]);
    }

    public function AllProducts(){
        
        $products = Product::all();
        return response()->json($products);
    }

    public function EditProduct(int $id){

        $product = Product::find($id);

        if (!$product) {
            return redirect()->route('products.index')->with('error', 'Product not found');
        }

        return view('edit_product', compact('product'));
    }


}
