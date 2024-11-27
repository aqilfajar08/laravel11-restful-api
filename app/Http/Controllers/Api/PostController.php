<?php

namespace App\Http\Controllers\Api;

// import model post
use App\Models\Post;

use App\Http\Controllers\Controller;

// import resource PostRecource
use App\Http\Resources\PostResource;

// import HTTP request
use Illuminate\Http\Request;

// import facade Validator
use Illuminate\Support\Facades\Validator;

//import facade Storage
use Illuminate\Support\Facades\Storage;
use Symfony\Contracts\Service\Attribute\Required;

class PostController extends Controller
{
    public function index()
    {
        // get all post
        // membuat fungsi field `$posts` yang dimana akan mengirimkan 5 postingan baru di setiap halaman 
        $posts = Post::latest()->paginate(5);

        // return collection of posts as a resource

        /* 
        berfungsi untuk mengirimkan data yang diambil dari format khusus (PostResource)
        yang dimana di dalam nya berfungsi juga untuk memberikan informasi serta postingan terbaru kepada user
        `true` untuk menyatakan postingan berhasil   
        List data posts untuk memberikan informasi dari postingan
        `$posts` adalah data postingan itu sendiri 
        */
        return new PostResource(true, 'List data posts', $posts);
    }

    // MELAKUKAN METHOD POST PADA IMAGE, TITLE, CONTENT

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        /* 
        berfungsi yang untuk menyimpan file image dengan direktori yang telah diarahkan dan dibantu oleh `hashName()` 
        berfungsi jika nama file image yang sama dan menghindari konflik antar sesama file 
        */
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    public function show($id)
    {
        // find post by id
        /*
        
        */
        $post = Post::find($id);

        return new PostResource(true, 'Detail Data Post', $post);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = Post::find($id);

        //check if image is not empty
        if (request()->hasFile('image')) {

            //upload gambar baru
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //hapus gambar lama
            Storage::delete('public/posts' . basename($post->image));

            //update post dengan gambar baru
            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content,
            ]);

        } else {

            //update post tanpa gambar
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }
        return new PostResource(true, 'Update telah berhasil!', $post);
    }

    public function destroy($id) 
    {
        $post = Post::find($id);

        // delete image
        Storage::delete('public/posts' . basename($post->image));

        // delete post
        $post->delete();

        return new PostResource(true, 'Data post berhasil dihapus!', $post);
    }
}
