<?php

namespace App\Repository;

use App\Models\Blog;
use App\Models\BlogTag;
use App\Models\BlogHasTag;
use App\Models\BlogComment;
use Illuminate\Support\Str;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\BlogTranslation;
use App\Models\BlogHasCategories;
use Illuminate\Support\Facades\DB;

class BlogRepository
{

    /**
     * Will return blogs lits
     * 
     * @param Array $request
     * @return Collections
     */
    public function blogCategoriesList($request)
    {
        return BlogCategory::with('parentCat')->orderBy('id', 'DESC')->paginate(10)->withQueryString();
    }

    /**
     * Will store new blog category
     * 
     * @param Array $request
     * 
     * @return bool
     */
    public function storeNewCategory($request)
    {
        try {
            $cat = new BlogCategory();
            $cat->title = $request['title'];
            $cat->parent = $request['parent'];
            $cat->slug = Str::slug($request['title']);
            $cat->meta_title = $request['meta_title'];
            $cat->meta_description = $request['meta_description'];
            $cat->meta_image = $request['meta_image'];
            $cat->save();
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Error $e) {
            return false;
        }
    }
    /**
     * Will return category details
     * 
     * @param Int $id
     * @return Collection
     */
    public function categoryDetails($id)
    {
        return BlogCategory::find($id);
    }
    /**
     * Will update blog category
     * 
     * @param Array $request
     * 
     * @return bool
     */
    public function updateCategory($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != defaultLangCode()) {
                $category_translation = BlogCategoryTranslation::firstOrNew(['category_id' => $request['id'], 'lang' => $request['lang']]);
                $category_translation->title = x_clean($request['title']);
                $category_translation->save();
            } else {
                $cat = BlogCategory::find($request['id']);
                $cat->title = $request['title'];
                $cat->parent = $request['parent'];
                $cat->slug = Str::slug($request['title']);
                $cat->meta_title = $request['meta_title'];
                $cat->meta_description = $request['meta_description'];
                $cat->meta_image = $request['edit_meta_image'];
                $cat->status = $request['status'];
                $cat->is_featured = $request['is_featured'];
                $cat->save();
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will delete a category
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteCategory($id)
    {
        try {
            DB::beginTransaction();
            $cat = BlogCategory::find($id);
            $cat->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will return blog categories dropdown options
     * 
     * @param Object $request
     */
    public function categoryDropdownOptions($request)
    {
        $query = BlogCategory::with('child');

        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('title', 'LIKE',  '%' . $term . '%');
        }

        $categories = $query->orderBy('id', 'asc')->paginate(5);

        $output = [];

        foreach ($categories->items() as $category) {
            $item['id'] = $category->id;
            $item['text'] = $category->title;
            array_push($output, $item);

            if ($category->child != null) {
                foreach ($category->child as $child) {
                    $sub_item['id'] = $child->id;
                    $sub_item['text'] = '-- ' . $child->title;
                    array_push($output, $sub_item);

                    if ($child->child != null) {
                        foreach ($child->child as $pro_child) {
                            $sub_sub_item['id'] = $pro_child->id;
                            $sub_sub_item['text'] = '--- ' . $pro_child->title;
                            array_push($output, $sub_sub_item);
                        }
                    }
                }
            }
        }

        $morePages = true;

        if (empty($categories->nextPageUrl())) {
            $morePages = false;
        }
        $results = [
            "results" => $output,
            "pagination" => ["more" => $morePages]
        ];

        return $results;
    }
    /**
     * Will return blog tags dropdown options
     * 
     * @param Object $request
     */
    public function tagsDropdownOptions($request)
    {
        $query = BlogTag::query();

        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('title', 'LIKE',  '%' . $term . '%');
        }

        $categories = $query->orderBy('id', 'asc')->paginate(5);

        $output = [];

        foreach ($categories->items() as $category) {
            $item['id'] = $category->id;
            $item['text'] = $category->title;
            array_push($output, $item);

            if ($category->child != null) {
                foreach ($category->child as $child) {
                    $sub_item['id'] = $child->id;
                    $sub_item['text'] = '-- ' . $child->title;
                    array_push($output, $sub_item);

                    if ($child->child != null) {
                        foreach ($child->child as $pro_child) {
                            $sub_sub_item['id'] = $pro_child->id;
                            $sub_sub_item['text'] = '--- ' . $pro_child->title;
                            array_push($output, $sub_sub_item);
                        }
                    }
                }
            }
        }

        $morePages = true;

        if (empty($categories->nextPageUrl())) {
            $morePages = false;
        }
        $results = [
            "results" => $output,
            "pagination" => ["more" => $morePages]
        ];

        return $results;
    }
    /**
     * Will store new blog
     * 
     * @param Object $request
     * @return bool
     */
    public function storeNewBlog($request, $author)
    {
        try {
            DB::beginTransaction();
            $blog = new Blog();
            $blog->title = x_clean($request['title']);
            $blog->content = x_clean($request['content']);
            $blog->short_description = x_clean($request['short_description']);
            $blog->permalink = x_clean($request['permalink']);
            $blog->meta_title = x_clean($request['meta_title']);
            $blog->meta_description = x_clean($request['meta_description']);
            $blog->meta_image = x_clean($request['meta_image']);
            $blog->thumbnail = x_clean($request['thumbnail']);
            $blog->featured_image = x_clean($request['featured_image']);
            $blog->status = x_clean($request['status']);
            $blog->video = x_clean($request['video_link']);
            $blog->author = $author;
            $blog->is_featured = $request->has('is_featured') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $blog->save();

            //Store Categories
            if ($request->has('categories') && $request['categories'] != null) {
                foreach ($request['categories'] as $category) {
                    $blog_cat = new BlogHasCategories();
                    $blog_cat->blog_id = $blog->id;
                    $blog_cat->category_id = $category;
                    $blog_cat->save();
                }
            }

            //Store tags
            if ($request->has('tags') && $request['tags'] != null) {
                foreach ($request['tags'] as $tag) {
                    $tag_id = "";
                    $tagInfo = BlogTag::where('id', $tag)->first();
                    if ($tagInfo != null) {
                        $tag_id = $tagInfo->id;
                    } else {
                        $new_tag = new BlogTag();
                        $new_tag->title = x_clean($tag);
                        $new_tag->save();
                        $tag_id = $new_tag->id;
                    }
                    $blog_tag = new BlogHasTag();
                    $blog_tag->blog_id = $blog->id;
                    $blog_tag->tag_id = $tag_id;
                    $blog_tag->save();
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will store new blog
     * 
     * @param Object $request
     * @return bool
     */
    public function updateBlog($request)
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != defaultLangCode()) {
                $blog_translation = BlogTranslation::firstOrNew(['blog_id' => $request['id'], 'lang' => $request['lang']]);
                $blog_translation->title = x_clean($request['title']);
                $blog_translation->short_description = x_clean($request['short_description']);
                $blog_translation->content = x_clean($request['content']);
                $blog_translation->save();
            } else {
                $blog = Blog::findOrFail($request['id']);
                $blog->title = x_clean($request['title']);
                $blog->content = x_clean($request['content']);
                $blog->short_description = x_clean($request['short_description']);
                $blog->permalink = x_clean($request['permalink']);
                $blog->meta_title = x_clean($request['meta_title']);
                $blog->meta_description = x_clean($request['meta_description']);
                $blog->meta_image = x_clean($request['meta_image']);
                $blog->thumbnail = x_clean($request['thumbnail']);
                $blog->featured_image = x_clean($request['featured_image']);
                $blog->video = x_clean($request['video_link']);
                $blog->status = x_clean($request['status']);
                $blog->is_featured = $request->has('is_featured') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $blog->save();


                //update Categories
                BlogHasCategories::where('blog_id', $blog->id)->delete();

                if ($request->has('categories') && $request['categories'] != null) {
                    foreach ($request['categories'] as $category) {
                        $blog_cat = new BlogHasCategories();
                        $blog_cat->blog_id = $blog->id;
                        $blog_cat->category_id = $category;
                        $blog_cat->save();
                    }
                }

                //Update  tags
                BlogHasTag::where('blog_id', $blog->id)->delete();

                if ($request->has('tags') && $request['tags'] != null) {
                    foreach ($request['tags'] as $tag) {
                        $tag_id = "";
                        $tagInfo = BlogTag::where('id', $tag)->first();
                        if ($tagInfo != null) {
                            $tag_id = $tagInfo->id;
                        } else {
                            $new_tag = new BlogTag();
                            $new_tag->title = $tag;
                            $new_tag->save();
                            $tag_id = $new_tag->id;
                        }
                        $blog_tag = new BlogHasTag();
                        $blog_tag->blog_id = $blog->id;
                        $blog_tag->tag_id = $tag_id;
                        $blog_tag->save();
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will return blog list
     * 
     * @param Array $request
     * @return Collections
     */
    public function BlogList($request)
    {
        return Blog::with(['blog_translations', 'authorInfo' => function ($q) {
            $q->select('name', 'id');
        }])->orderBy('id', 'DESC')->paginate(10)->withQueryString();
    }

    /**
     * Will return blog comment list
     * 
     * @param Array $request
     * @return Collections
     */
    public function blogCommentList($request)
    {
        return BlogComment::with(['commentAuthor' => function ($q) {
            $q->select('name', 'id', 'image');
        }, 'blog' => function ($q) {
            $q->select('title', 'id', 'permalink');
        }])->orderBy('id', 'DESC')->paginate(10)->withQueryString();
    }

    /**
     * Return paginated active blogs for frontend listing
     */
    public function activeBlogList($request, int $perPage = 9)
    {
        $query = Blog::with(['blog_translations', 'authorInfo' => fn($q) => $q->select('id', 'name')])
            ->where('status', config('settings.general_status.active'));

        if ($request->filled('category')) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn($q) => $q->where('title', 'LIKE', "%{$term}%")
                ->orWhere('short_description', 'LIKE', "%{$term}%"));
        }

        return $query->orderByDesc('id')->paginate($perPage)->withQueryString();
    }

    /**
     * Find an active blog by permalink with relations loaded
     */
    public function getBlogByPermalink(string $permalink): ?Blog
    {
        return Blog::with(['blog_translations', 'categories.blog_category_translations', 'tags', 'authorInfo', 'comments'])
            ->where('permalink', $permalink)
            ->where('status', config('settings.general_status.active'))
            ->first();
    }

    /**
     * Return recent active blogs, excluding a given id
     */
    public function recentBlogs(int $exclude, int $limit = 4)
    {
        return Blog::with('blog_translations')
            ->where('status', config('settings.general_status.active'))
            ->where('id', '!=', $exclude)
            ->orderByDesc('id')
            ->take($limit)
            ->get();
    }

    /**
     * Store a new comment on a blog post
     */
    public function storeBlogComment(array $data): bool
    {
        try {
            $comment = new BlogComment();
            $comment->blog_id   = $data['blog_id'];
            $comment->user_id   = $data['user_id'] ?? null;
            $comment->guest_name  = $data['guest_name'] ?? null;
            $comment->guest_email = $data['guest_email'] ?? null;
            $comment->comment   = $data['comment'];
            $comment->status    = config('settings.general_status.active');
            $comment->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Will delete a blog
     *
     * @param Int $id
     * @return bool
     */
    public function deleteBlog($id)
    {
        try {
            DB::beginTransaction();
            $blog = Blog::find($id);
            $blog->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will delete a page
     * 
     * @param Int $id
     * @return bool
     */
    public function deleteBlogComment($id)
    {
        try {
            DB::beginTransaction();
            $comment = BlogComment::find($id);
            $comment->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
}
