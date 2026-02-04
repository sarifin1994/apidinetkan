<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request){
        $perPage = $request->get('per_page', 10);
        $query = BlogPost::where('status','publish');
        $posts = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json($posts);
    }

    public function show($slug){
        $post = BlogPost::where('slug',$slug)->firstOrFail();
        return response()->json($post);
    }

}
