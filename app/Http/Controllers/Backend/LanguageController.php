<?php

namespace App\Http\Controllers\Backend;

use App\Models\Language;
use Illuminate\View\View;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    /**
     * Will return language list
     */
    public function language(): View
    {
        $languages = Language::all();
        return view('backend.modules.system.language.list', ['languages' => $languages]);
    }
    /**
     * Will store new language
     */
    public function languageStore(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|max:250',
            'code' => 'required|max:250',
            'native_name' => 'required|max:250'
        ]);
        try {
            $language = new Language();
            $language->title = $request['name'];
            $language->code = $request['code'];
            $language->native_title = $request['native_name'];
            $language->status = config('settings.general_status.active');
            $language->save();

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }
    /**
     * Will return language edit form
     */
    public function languageEdit(Request $request): JsonResponse
    {
        try {
            $lang = Language::find($request['id']);
            if ($lang != null) {
                return response()->json([
                    'success' => true,
                    'data' => view('backend.modules.system.language.edit', ['lang' => $lang])->render()
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false
            ], 500);
        }
    }
    /**
     * Will update language
     */
    public function languageUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|max:250',
            'code' => 'required|max:250',
            'native_name' => 'required|max:250'
        ]);
        try {
            $language = Language::find($request['id']);
            if ($language == null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Language not found'
                ]);
            }

            $language->title = x_clean($request['name']);
            $language->code = x_clean($request['code']);
            $language->native_title = x_clean($request['native_name']);
            $language->status = $request['status'];
            $language->save();

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }
    /**
     * Will delete a language
     */
    public function languageDelete(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $lang = Language::findOrFail($request['id']);
            $lang->delete();
            DB::commit();
            toastNotification('success', 'Language Deleted Successfully', 'Success');
            return to_route('admin.system.settings.language.list');
        } catch (\Exception $e) {
            DB::rollback();
            toastNotification('error', 'Language Delete Failed', 'Error');
            return redirect()->back();
        }
    }
    /**
     * Will return language key value
     */
    public function LanguageKeys(Request $request, $id): View
    {
        $language = Language::findOrFail($id);

        $lang_keys = Translation::where('lang', 'en');
        if ($request->has('search_key') && $request['search_key'] != null) {
            $lang_keys = $lang_keys->where('lang_key', 'like', '%' . preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', strtolower($request['search_key']))) . '%')
                ->orWhere('lang_value', 'like', '%' . $request['search_key'] . '%')
                ->orWhere('lang_key', 'like', '%' . $request['search_key'] . '%');
        }
        $lang_keys = $lang_keys->paginate(20);
        return view('backend.modules.system.language.lang_keys')->with(
            [
                'language' => $language,
                'lang_keys' => $lang_keys,
            ]
        );
    }

    /**
     * Will update translation
     */
    public function translationUpdate(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $language = Language::findOrFail($request->id);
            foreach ($request->values as $key => $value) {
                $translation = Translation::where('lang_key', $key)->where('lang', $language->code)->latest()->first();
                if ($translation == null) {
                    $translation = new Translation;
                    $translation->lang = $language->code;
                    $translation->lang_key = x_clean($key);
                    $translation->lang_value = x_clean($value);
                    $translation->save();
                } else {
                    $translation->lang_value = x_clean($value);
                    $translation->save();
                }
            }
            DB::commit();
            toastNotification('success', 'Translation updated successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Translation update failed', 'Error');
            return redirect()->back();
        }
    }

    /**
     * Will set session language
     */
    public function setSessionLanguage($code): RedirectResponse
    {
        try {
            $lang = Language::where('code', $code)->first();
            if ($lang != null) {
                session()->put('locale', $code);
                app()->setLocale($code);
                return redirect()->back();
            }
            toastNotification('error', 'Something went wrong', 'Error');
            return redirect()->back();
        } catch (\Exception $e) {
            toastNotification('error', 'Something went wrong', 'Error');
            return redirect()->back();
        }
    }
}
