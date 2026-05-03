<?php

namespace App\Http\Controllers\Backend;

use App\Models\Menu;
use App\Models\Page;
use App\Models\MenuItem;
use App\Models\MenuPosition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use App\Models\MenuItemTranslation;
use Illuminate\Http\RedirectResponse;

class MenuController extends Controller
{
    /**
     * Will return menu builder page
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function menus(Request $request): View
    {
        $all_menus = Menu::all();
        $header_menu = MenuPosition::with(['menu'])->where('position_id', config('settings.menu_position.header'))->first();
        $footer_menu = MenuPosition::with(['menu'])->where('position_id', config('settings.menu_position.footer'))->first();
        $selected_menu_id = $request->has('item') ? $request['item'] : null;
        $selected_menu = Menu::with(['items' => function ($q) {
            $q->with(['children', 'menu_translations'])
                ->where('parent', null)
                ->orderBy('position', 'ASC');
        }])
            ->find($selected_menu_id);


        $pages = Page::where('status', config('settings.general_status.active'))->get();

        return view('backend.modules.appearances.menus.builder', [
            'all_menus'       => $all_menus,
            'selected_menu'   => $selected_menu,
            'header_menu'     => $header_menu,
            'footer_menu'     => $footer_menu,
            'pages'           => $pages,
            'product_categories' => []
        ]);
    }

    /**
     * Will update and create menu menu
     * 
     * @param \Illuminate\Http\Request $request
     * 
     */
    public function menuManagement(Request $request)
    {
        try {
            //Store new Menu
            if ($request['action'] === 'create') {
                $menu = new Menu();
                $menu->title = $request['menu_name'];
                $menu->save();
                $this->updateMenuPosition($menu->id, $request);
                toastNotification('success', 'New menu created successfully', 'Success');
                return redirect()->route('admin.appearance.menu.builder', ['action' => 'edit', 'item' => $menu->id]);
            }

            //Update menu
            if ($request['action'] === 'edit') {
                //Update menu
                $editable_menu = Menu::find($request['menu_id']);
                $editable_menu->title = $request['menu_name'];
                $editable_menu->save();

                //Update menu items positions
                $menu_items = json_decode($request['menu-items'], true);
                foreach ($menu_items as $key => $item) {
                    $menu_item = MenuItem::find($item['id']);
                    if ($menu_item != null) {
                        $menu_item->position = $key + 1;
                        $menu_item->parent = null;
                        $menu_item->save();
                    }
                    if (array_key_exists('children', $item)) {
                        $this->updateSubMenuItems($item['id'], $item['children']);
                    }
                }
                $this->updateMenuPosition($editable_menu->id, $request);
                toastNotification('success', 'Menu updated Successfully', 'Success');
                return redirect()->route('admin.appearance.menu.builder', ['action' => 'edit', 'item' => $request['menu_id']]);
            }
        } catch (\Exception $e) {
            toastNotification('error', 'Something went wrong');
            return redirect()->back();
        }
    }
    /**
     * Will update sub menu items position
     * 
     * @param Int $parent_id
     * @param Array $children
     */
    public function updateSubMenuItems($parent_id, $children): void
    {
        foreach ($children as $key => $item) {
            $menu_item = MenuItem::find($item['id']);
            if ($menu_item != null) {
                $menu_item->position = $key + 1;
                $menu_item->parent = $parent_id;
                $menu_item->save();
            }

            if (array_key_exists('children', $item)) {
                $this->updateSubMenuItems($item['id'], $item['children']);
            }
        }
    }
    /**
     * Will update menu position
     * 
     * @param Int $menu_id
     * @param Object $request
     */
    public function updateMenuPosition($menu_id, $request): void
    {
        try {
            DB::beginTransaction();
            //Update Header Menu position
            if ($request->has('header_menu')) {
                $header_menu = MenuPosition::firstOrCreate(['position_id' => config('settings.menu_position.header')]);
                $header_menu->menu_id = $menu_id;
                $header_menu->save();
            } else {
                MenuPosition::where('position_id', config('settings.menu_position.header'))->where('menu_id')->delete();
            }
            //Update Footer Menu position
            if ($request->has('footer_menu')) {
                $footer_menu = MenuPosition::firstOrCreate(['position_id' => config('settings.menu_position.footer')]);
                $footer_menu->menu_id = $menu_id;
                $footer_menu->save();
            } else {
                MenuPosition::where('position_id', config('settings.menu_position.footer'))->where('menu_id')->delete();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
    /**
     * Will add item to menu
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function addMenuItems(Request $request): JsonResponse
    {
        try {

            $data = json_decode($request['data'], true);
            $last_item = MenuItem::where('menu_id', $data['menu_id'])->orderBy('position', 'DESC')->first();

            $last_position = $last_item != null ? $last_item->position : 1;
            //Add Custom item
            if ($data['item'] == 'custom') {
                $menu_item = new MenuItem();
                $menu_item->menu_id = $data['menu_id'];
                $menu_item->item_type = config('settings.menu_item_type.custom');
                $menu_item->title = $data['text'];
                $menu_item->link = $data['link'];
                $menu_item->position = $last_position + 1;
                $menu_item->save();
                return response()->json([
                    'success' => true
                ]);
            }

            //Add Page item
            if ($data['item'] == 'page') {
                foreach ($data['pages'] as $key => $page) {
                    $page_info = Page::where('id', $page)->first();
                    $menu_item = new MenuItem();
                    $menu_item->menu_id = $data['menu_id'];
                    $menu_item->item_type = config('settings.menu_item_type.page');
                    $menu_item->title = $page_info != null ? $page_info->title : null;
                    $menu_item->link = $page_info != null ? $page_info->permalink : null;
                    $menu_item->linkable_type = 'App\Models\Page';
                    $menu_item->linkable_id = $page;
                    $menu_item->position = $last_position + $key + 1;
                    $menu_item->save();
                }
                return response()->json([
                    'success' => true
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * Will remove menu item
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function removeMenuItem(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $menu_item = MenuItem::find($request['data']);
            if ($menu_item != null) {
                $menu_item->delete();
                DB::commit();
                return response()->json([
                    'success' => true
                ]);
            }
            DB::commit();
            return response()->json([
                'success' => false
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * Will update menu item
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function updateMenuItem(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $menu_item = MenuItem::find($request['id']);
            if ($menu_item != null) {
                if ($request['lang'] != null && $request['lang'] != defaultLangCode()) {
                    $menu_items_translation = MenuItemTranslation::firstOrNew(['item_id' => $request['id'], 'lang' => $request['lang']]);
                    $menu_items_translation->title = x_clean($request['text']);
                    $menu_items_translation->save();
                } else {
                    $menu_item->title = $request['text'];
                    if ($menu_item->item_type == config('settings.menu_item_type.custom')) {
                        $menu_item->link = $request['link'];
                    }
                    $menu_item->save();
                }

                DB::commit();
                return response()->json([
                    'success' => true
                ]);
            }
            DB::commit();
            return response()->json([
                'success' => false
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * Will delete menu 
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function deleteMenu(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $menu = Menu::where('id', $request['id'])->first();
            if ($menu != null) {
                if ($menu->position != null) {
                    $menu->position->delete();
                }
                if ($menu->items != null) {
                    $menu->items()->delete();
                }
                $menu->delete();
                DB::commit();
                toastNotification('success', 'Menu deleted successfully', 'Success');
                return to_route('admin.appearance.menu.builder', ['action' => 'create']);
            }
            DB::commit();
            toastNotification('error', 'Menu delete failed', 'Error');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Menu delete failed', 'Error');
            return redirect()->back();
        }
    }
}
