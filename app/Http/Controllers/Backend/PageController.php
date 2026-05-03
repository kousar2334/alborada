<?php

namespace App\Http\Controllers\Backend;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Repository\PageRepository;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class PageController extends Controller
{

    public function __construct(public PageRepository $pageRepository)
    {
    }

    /**
     * Will redirect page list
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function pageList(Request $request): View
    {
        return view('backend.modules.page.list', ['pages' => $this->pageRepository->pageList($request)]);
    }

    /**
     * Will redirect new page form page
     * 
     * 
     */
    public function createNewPage(): View
    {
        return view('backend.modules.page.new_page');
    }
    /**
     * Will store new page
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function storeNewPage(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:250',
            'permalink' => 'required|unique:pages,permalink',
        ]);

        $res = $this->pageRepository->storeNewPage($request, auth()->user()->id);
        if (!$res) {
            toastNotification('error', 'Page Create Failed', 'Error');
            return redirect()->back();
        }

        toastNotification('success', 'Page Create Successfully');
        return to_route('admin.page.list');
    }
    /**
     *Will redirect page edit page
     *
     *@param Page $page
     **/
    public function editPage(Page $page,Request $request): View
    {
        return view('backend.modules.page.edit_page', ['page' => $page,'lang'=> $request->lang]);
    }
    /**
     * Will update page
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function updatePage(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:250',
            'permalink' => 'required|unique:pages,permalink,' . $request['id'],
        ]);
        $res = $this->pageRepository->updatePage($request);
        if (!$res) {
            toastNotification('error', 'Page Update Failed', 'Error');
            return to_route('admin.page.edit', ['page' => $request['id'],'lang'=>$request['lang']]);
        }

        toastNotification('success', 'Page Update Successfully', 'Success');
        return to_route('admin.page.edit', ['page' => $request['id'],'lang'=>$request['lang']]);
    }
    /**
     * Will delete page
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function deletePage(Request $request)
    {
        $res = $this->pageRepository->deletePage($request['id']);
        if (!$res) {
            toastNotification('error', 'Action failed. Please try again', 'Error');
            return redirect()->back();
        }

        toastNotification('success', 'Page Deleted successfully', 'Success');
        return to_route('admin.page.list');
    }
}
