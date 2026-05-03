<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\BlogRepository;

class BlogController extends Controller
{
    public function __construct(public BlogRepository $blog_repository) {}

    /**
     * Display the public blog listing page
     */
    public function blogList(Request $request)
    {
        $blogs = $this->blog_repository->activeBlogList($request);

        return view('frontend.pages.blog-list', compact('blogs'));
    }

    /**
     * Display a single blog post by permalink
     */
    public function blogDetails(string $permalink)
    {
        $blog = $this->blog_repository->getBlogByPermalink($permalink);

        abort_if($blog === null, 404);

        $recentBlogs = $this->blog_repository->recentBlogs($blog->id);

        return view('frontend.pages.blog-details', compact('blog', 'recentBlogs'));
    }

    /**
     * Store a comment on a blog post
     */
    public function storeComment(Request $request, string $permalink)
    {
        $request->validate([
            'comment'     => 'required|string|max:1000',
            'guest_name'  => 'required_unless:user,' . (auth()->id() ?? 'null') . '|nullable|string|max:100',
            'guest_email' => 'nullable|email|max:100',
        ]);

        $blog = $this->blog_repository->getBlogByPermalink($permalink);
        abort_if($blog === null, 404);

        $data = [
            'blog_id'     => $blog->id,
            'user_id'     => auth()->id(),
            'guest_name'  => auth()->check() ? null : $request->guest_name,
            'guest_email' => auth()->check() ? null : $request->guest_email,
            'comment'     => $request->comment,
        ];

        $this->blog_repository->storeBlogComment($data);

        return back()->with('comment_success', __tr('Your comment has been submitted successfully.'));
    }
}
