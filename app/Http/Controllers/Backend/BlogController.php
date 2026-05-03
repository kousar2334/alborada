<?php

namespace App\Http\Controllers\Backend;

use App\Models\Blog;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repository\BlogRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class BlogController extends Controller
{

    public function __construct(public BlogRepository $blogRepository) {}
    /**
     * Will return blog categories list
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function categoriesList(Request $request): View
    {
        return view('backend.modules.blogs.categories', ['categories' => $this->blogRepository->blogCategoriesList($request)]);
    }

    /**
     *Will return blog comment list 
     **/
    public function blogComments(Request $request): View
    {
        return view('backend.modules.blogs.comments', ['comments' => $this->blogRepository->blogCommentList($request)]);
    }

    /**
     * Will delete a blog comment
     */
    public function blogCommentDelete(Request $request): RedirectResponse
    {
        $res = $this->blogRepository->deleteBlogComment($request['id']);
        if (!$res) {
            toastNotification('error', 'Action failed. Please try again', 'Error');
            return redirect()->back();
        }

        toastNotification('success', 'Comment Deleted successfully', 'Success');
        return to_route('admin.blogs.comment.list');
    }
    /**
     * Will return blog tags dropdown options
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function tagsDropdownOptions(Request $request): JsonResponse
    {
        return response()->json($this->blogRepository->tagsDropdownOptions($request));
    }
    /**
     * Will return blog categories dropdown options
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function categoryDropdownOptions(Request $request): JsonResponse
    {
        return response()->json($this->blogRepository->categoryDropdownOptions($request));
    }

    /**
     * Will store new category
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function storeNewCategory(Request $request): JsonResponse
    {
        $request->validate([
            'title'            => 'required|max:250|unique:blog_categories,title',
            'parent'           => 'nullable|exists:blog_categories,id',
            'meta_title'       => 'nullable|max:250',
            'meta_description' => 'nullable|max:250',
        ]);

        $res = $this->blogRepository->storeNewCategory($request);
        if (!$res) {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }

        return response()->json(
            [
                'success' => true,
            ]
        );
    }
    /**
     * Will load category edit form
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function editCategory($id, Request $request): View
    {
        $category = $this->blogRepository->categoryDetails($request['id']);
        return view('backend.modules.blogs.edit_category', ['category' => $category]);
    }
    /**
     * Will update category
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function updateCategory(Request $request)
    {
        $request->validate([
            'title'            => 'required|max:250|unique:blog_categories,title,' . $request['id'],
            'parent'           => 'nullable|exists:blog_categories,id',
            'meta_title'       => 'nullable|max:250',
            'meta_description' => 'nullable|max:250',
        ]);

        $res = $this->blogRepository->updateCategory($request);

        if ($res) {
            toastNotification('success', 'Category updated successfully', 'Success');
        } else {
            toastNotification('error', 'Category update failed', 'Error');
        }
        return to_route('admin.blogs.categories.edit', ['id' => $request['id'], 'lang' => $request['lang']]);
    }
    /**
     * Will delete a blog category
     * 
     * @param \Illuminate\Http\Request $request 
     */
    public function deleteCategory(Request $request): RedirectResponse
    {
        $res = $this->blogRepository->deleteCategory($request['id']);
        if (!$res) {
            toastNotification('error', 'Action failed. Please try again', 'Error');
            return redirect()->back();
        }

        toastNotification('success', 'Category Deleted successfully', 'Success');
        return to_route('admin.blogs.categories.list');
    }
    /**
     * Will redirect blogs list
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function blogList(Request $request): View
    {
        return view('backend.modules.blogs.list', ['blogs' => $this->blogRepository->BlogList($request)]);
    }

    /**
     * Will redirect new blog page
     * 
     */
    public function createNewBlog(): View
    {
        return view('backend.modules.blogs.new_blog');
    }
    /**
     * Will store new blog
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function storeNewBlog(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:250',
            'short_description' => 'required',
            'permalink' => 'required|unique:blogs,permalink',
        ]);

        $res = $this->blogRepository->storeNewBlog($request, auth()->user()->id);
        if (!$res) {
            toastNotification('error', 'Blog Create Failed', 'Error');
            return redirect()->back();
        }

        toastNotification('success', 'Blog Create Successfully');
        return to_route('admin.blogs.list');
    }
    /**
     * Will redirect blog edit page
     * 
     * @param Blog $blog
     */
    public function editBlog(Blog $blog, Request $request): View
    {
        return view('backend.modules.blogs.edit_blog', ['blog' => $blog, 'lang' => $request->lang]);
    }
    /**
     * Will update blog
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function updateBlog(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:250',
            'permalink' => 'required|unique:blogs,permalink,' . $request['id'],
        ]);

        $res = $this->blogRepository->updateBlog($request);
        if (!$res) {
            toastNotification('error', 'Blog Update Failed', 'Error');
        }

        toastNotification('success', 'Blog Update Successfully', 'Success');
        return to_route('admin.blogs.edit', ['blog' => $request['id'], 'lang' => $request->lang]);
    }
    /**
     * Will delete blog
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function deleteBlog(Request $request)
    {
        $res = $this->blogRepository->deleteBlog($request['id']);
        if (!$res) {
            toastNotification('error', 'Action failed. Please try again', 'Error');
            return redirect()->back();
        }

        toastNotification('success', 'Blog Deleted successfully', 'Success');
        return to_route('admin.blogs.list');
    }
}
