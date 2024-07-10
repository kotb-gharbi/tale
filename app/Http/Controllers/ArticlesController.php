<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

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

    public function UpdateArticle(Request $request , int $id){

        $data = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'image' => ['required','mimes:png,jpeg,jpg','max:2048'],
            'price' => ['required' , 'numeric']
        ]);


        $article = Article::find($id);

        if(!$article){
            return response()->json(["message" => "Article not found"]);
        }

        
        $path = public_path('uploads');

        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($path, $filename);
            $data = $request->except('image');
            $data['image'] = $filename;

            //delete old image from uploads directory
            if(file_exists($path . '/' . $article->image)){
                unlink($path . '/' . $article->image);
            }

            //update article data
            $article->update($data);

            return response()->json(["message" => "article updated successfully"]);
        }
    }
}
    

