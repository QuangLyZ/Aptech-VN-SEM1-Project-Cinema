<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;
use App\Support\CloudinaryUploader;

class PostController extends Controller
{
    protected $cloudinary;

    public function __construct(CloudinaryUploader $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }
public function uploadImage(Request $request)
{
    if ($request->hasFile('upload')) {

        $file = $request->file('upload');

        $request->validate([
            'upload' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // lưu file
        $path = $file->storeAs('uploads', $filename, 'public');

        return response()->json([
            "uploaded" => 1,
            "fileName" => $filename,
            "url" => asset('storage/' . $path)
        ]);
    }

    return response()->json([
        "uploaded" => 0,
        "error" => [
            "message" => "Upload failed"
        ]
    ]);
}
public function list()
{
    $posts = Post::where('status', 'visible')
        ->orderBy('publish_at', 'desc')
        ->paginate(9);

return view('admin.posts.index', compact('posts'));
}
    public function index()
    {
        $posts = Post::latest()->get();

        return view('admin.home', [
            'activeTab' => 'posts',
            'pageTitle' => 'Bài viết',
            'posts' => $posts
        ]);
    }

public function store(Request $request)
{
    $request->validate([
        'title' => 'required',
        'content' => 'required',
        'publish_at' => 'nullable'
    ]);

    Post::create([
        'title' => $request->title,
        'keywords' => $request->keywords,
        'content' => $request->content,
        'thumbnail' => $request->thumbnail,
        'publish_at' => $request->publish_at,

        // 👉 QUAN TRỌNG: dùng visible
        'status' => 'visible'
    ]);

    return redirect()->back()->with('success', 'Đăng bài thành công!');
}
public function edit($id)
{
    $post = Post::findOrFail($id);
    $posts = Post::paginate(10); // thêm dòng này

    return view('admin.posts.edit', compact('post', 'posts'));
}

public function update(Request $request, $id)
{
    $post = Post::findOrFail($id);

    $post->update([
        'title' => $request->title,
        'keywords' => $request->keywords,
        'content' => $request->content,
        'thumbnail' => $request->thumbnail,
        'publish_at' => $request->publish_at,
    ]);

    return redirect()->route('admin.posts.index')
        ->with('success', 'Cập nhật thành công');
}
public function destroy($id)
{
    $post = Post::findOrFail($id);

    if ($post->thumbnail) {
        try {
            // Trich xuat Public ID tu URL Cloudinary
            // Vi du: https://res.cloudinary.com/.../image/upload/v1234/cinebook/posts/abc.jpg
            // Public ID se la: cinebook/posts/abc
            $parts = explode('/', parse_url($post->thumbnail, PHP_URL_PATH));
            $uploadIndex = array_search('upload', $parts);
            if ($uploadIndex !== false) {
                $publicIdWithExt = implode('/', array_slice($parts, $uploadIndex + 2)); // Bo qua 'upload' va 'version'
                $publicId = pathinfo($publicIdWithExt, PATHINFO_FILENAME);
                $this->cloudinary->deleteImage($publicId);
            }
        } catch (\Exception $e) {
            \Log::error('Loi xoa anh Cloudinary: ' . $e->getMessage());
        }
    }

    $post->delete();

    return back()->with('success', 'Xóa bài viết thành công!');
}
public function show($id)
{
    $post = \App\Models\Post::findOrFail($id);
    return view('admin.posts.show', compact('post'));
}
public function toggle($id)
{
    $post = Post::findOrFail($id);

    if ($post->status == 'hidden') {
        $post->status = 'visible';
    } else {
        $post->status = 'hidden';
    }

    $post->save();

    return back()->with('success', 'Cập nhật thành công');
}
}