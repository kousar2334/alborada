<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    /**
     * Will redirect site settings page
     */
    public function siteSetting(): View
    {
        return view('backend.modules.appearances.site-setting.site');
    }

    /**
     * Will update site settings 
     */
    public function siteSettingUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'site_name' => 'required|max:100'
        ]);
        try {
            foreach ($request->except('_token') as $key => $value) {
                set_setting($key, $value);
            }

            toastNotification('success', 'Settings update successfully', 'Success');
            return to_route('admin.appearance.site.setting');
        } catch (\Exception $e) {
            toastNotification('error', 'Settings update failed');
            return redirect()->back();
        }
    }
    /**
     * Will redirect footer settings page
     */
    public function footerSetting(): View
    {

        return view('backend.modules.appearances.site-setting.footer');
    }
    /**
     * Will update footer settings
     * 
     */
    public function footerSettingUpdate(Request $request): RedirectResponse
    {
        try {

            foreach ($request->except('_token') as $key => $value) {
                set_setting($key, $value);
            }

            toastNotification('success', 'Footer Settings update successfully', 'Success');
            return to_route('admin.appearance.site.setting.footer');
        } catch (\Exception $e) {
            toastNotification('error', 'Footer Settings update failed');
            return redirect()->back();
        }
    }

    /**
     * Will redirect seo settings page
     */
    public function seoSetting(): View
    {
        return view('backend.modules.appearances.site-setting.seo');
    }
    /**
     * Will update seo settings
     * 
     */
    public function seoSettingUpdate(Request $request): RedirectResponse
    {

        try {

            foreach ($request->except('_token') as $key => $value) {
                set_setting($key, $value);
            }

            toastNotification('success', 'Seo information update successfully', 'Success');
            return to_route('admin.appearance.site.setting.seo');
        } catch (\Exception $e) {
            toastNotification('error', 'Seo information update failed');
            return redirect()->back();
        }
    }


    /**
     * Will redirect colors settings page
     */
    public function colorSetting(): View
    {
        return view('backend.modules.appearances.site-setting.colors');
    }
    /**
     * Will update colors settings
     * 
     */
    public function colorSettingUpdate(Request $request): RedirectResponse
    {
        try {
            foreach ($request->except('_token') as $key => $value) {
                set_setting($key, $value);
            }
            toastNotification('success', 'Colors Settings update successfully', 'Success');
            return to_route('admin.appearance.site.setting.colors');
        } catch (\Exception $e) {
            toastNotification('error', 'Colors Settings update failed');
            return redirect()->back();
        }
    }

    /**
     * Will redirect custom css settings page
     */
    public function customCssSetting(): View
    {
        return view('backend.modules.appearances.site-setting.custom_css');
    }
    /**
     * Will update custom css settings
     * 
     */
    public function customCssSettingUpdate(Request $request): RedirectResponse
    {
        try {
            foreach ($request->except('_token') as $key => $value) {
                set_setting($key, $value);
            }
            toastNotification('success', 'Custom CSS update successfully', 'Success');
            return to_route('admin.appearance.site.setting.custom.css');
        } catch (\Exception $e) {
            toastNotification('error', 'Custom CSS update failed');
            return redirect()->back();
        }
    }
}
