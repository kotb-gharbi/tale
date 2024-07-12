<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArticlesController extends Controller
{
    public function AddArticle(Request $request){

        $data = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'image' => ['required','mimes:png,jpeg,jpg','max:2048'],
            'price' => ['required' , 'numeric']
        ]);

        $path = public_path('uploads');

        //move image to uploads folder
        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($path, $filename);
            $data['image'] = $filename;

            //create a new article in the database
            $article = Article::create($data);

            if ($article) {
                return response()->json(["message" => "Article added successfully"]);
            } else {
                return response()->json(["message" => "Failed to add Article"]);
            }
        }

        return response()->json(["message" => "Image upload failed"]);
    }

    public function AllArticles(){

        $articles = Article::all();
        return response()->json($articles);
    }

    public function DeleteArticle(int $id){

        $article = Article::find($id);

        if(!$article){
            return response()->json(["message" => "Article not found"]);
        }

        //delete image from uploads folder
        $path = public_path('uploads');
        $image = $article->image;

        if(file_exists($path . '/' . $image)){
            unlink($path . '/' . $image);
        }
        
        //delete article from database
        $article->delete();

        return response()->json(["message" => "Article deleted successfully"]);
    }

    public function EditArticle($id){

        $article = Article::find($id);

        if(!$article){
            return response()->json(["message" => "Article not found"]);
        }

        return response()->json($article);

    }

    public function UpdateArticle(Request $request, int $id) {
        Log::info('UpdateArticle request data: ', $request->all());
    
        $data = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'image' => ['required', 'mimes:png,jpeg,jpg', 'max:2048'],
            'price' => ['required', 'numeric']
        ]);
    
        // Find the article
        $article = Article::find($id);
    
        if (!$article) {
            return response()->json(["message" => "Article not found"]);
        }
    
        //Handle image upload if present
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = public_path('uploads');
            $file->move($path, $filename);
    
            // Delete the old image
            if (file_exists($path . '/' . $article->image)) {
                unlink($path . '/' . $article->image);
            }

            //Update data with new image filename
            $data['image'] = $filename;

            $article->update($data);
    
            return response()->json(["message" => "Article updated successfully"]);

        }
        
        $data['image'] = $article->image;

        return response()->json(["message" => "Problem updating the article"]);
    }
    
}
    

