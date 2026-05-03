<?php

use App\Models\Media;
use App\Models\Setting;
use App\Models\Language;
use App\Models\PageContent;
use App\Models\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Cache;
use App\Models\PageContentTranslation;
use Illuminate\Support\Facades\Artisan;

if (! function_exists("saveFileInStorage")) {
    /**
     * upload file in storage
     * @param $file
     * @param $path
     * @return string
     */
    function saveFileInStorage($file)
    {
        $extension = $file->getClientOriginalExtension();
        $file_original_name = $file->getClientOriginalName();
        $file_name_array = explode('.', $file_original_name);
        $file_name_with_out_extension = $file_name_array[0];
        $file_size = $file->getSize();
        $disk = 'public';
        $destination_folder = "uploaded";
        $path = $file->store($destination_folder, $disk);

        $media = new Media();
        $media->title = $file_name_with_out_extension;
        $media->file_name = $file_original_name;
        $media->alt = $file_name_with_out_extension;
        $media->mime_type = $extension;
        $media->size = $file_size;
        $media->path = $path;
        $media->disk = $disk;
        $media->uploaded_by = auth()->user() != null ? auth()->user()->id : null;

        $media->save();

        return $media->path;
    }
}


if (!function_exists('getDefaultLang')) {
    function getDefaultLang()
    {
        // $language = Language::where('is_default', 1)->first();
        // if($language){
        //     return $language->code;
        // }
        return 'en';
    }
}

if (!function_exists('toastNotification')) {
    /**
     * Set toast message
     *
     * @param String $type
     * @param String $message
     * @param String $header
     * @return void
     */
    function toastNotification($type, $message, $header = null)
    {
        Toastr::$type($message, $header);
    }
}

if (!function_exists('setEnv')) {
    /**
     * update env value
     * @param $name KeyName
     * @param $value Key Value
     * @return void
     */
    function setEnv($name, $value)
    {
        $value = trim($value);
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $name . '=' . env($name),
                $name . '=' . $value,
                file_get_contents($path)
            ));
        }
    }
}

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null)
    {
        $settings = Cache::remember('settings', 86400, function () {
            return Setting::all();
        });
        $setting = $settings->where('key', $key)->first();

        return $setting == null ? $default : $setting->value;
    }
}

if (!function_exists('set_setting')) {
    function set_setting($key, $value = null)
    {
        try {
            $setting = Setting::firstOrCreate(['key' => $key]);
            $setting->value = $value;
            $setting->save();
            cache()->forget('settings');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('getFilePath')) {
    function getFilePath(string|null $path, bool $placeholder = true, string|null $size = null): string
    {

        if ($placeholder && $path == null) {
            $location = getPlaceHolder();
            return $location;
        }

        if ($size != null) {
            $full_path_array = explode('/', $path);
            $file_full_name = $full_path_array[sizeof($full_path_array) - 1];
            $file_full_name_array = explode('.', $file_full_name);
            $file_name = sizeof($file_full_name_array) > 1 ? $file_full_name_array[0] : null;
            $extension = sizeof($file_full_name_array) > 1 ? $file_full_name_array[1] : null;
            $variant_image_name = $file_name . $size . '.' . $extension;
            array_pop($full_path_array);
            $location = implode('/', $full_path_array);
            $variant_image_path = $location . '/' . $variant_image_name;
            $full_path = 'public/' . $variant_image_path;
            if (file_exists($full_path)) {
                return $full_path;
            }
        }

        $location = 'public/' . $path;
        if (!file_exists($location)) {
            return getPlaceHolder();
        }

        return $location;
    }
}

if (!function_exists('getPlaceHolder')) {
    function getPlaceHolder()
    {
        return "/public/web-assets/backend/img/media/place_holder.jpg";
    }
}

if (!function_exists('translation')) {
    function __tr($key, $lang = null, $addslashes = false)
    {
        if ($lang == null) {
            $lang = session()->get('locale');
        }

        $lang_key = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', strtolower($key)));

        $translations_en = Cache::rememberForever('translations-en', function () {
            return Translation::where('lang', 'en')->pluck('lang_value', 'lang_key')->toArray();
        });

        if (!isset($translations_en[$lang_key])) {
            $translation_def = new Translation;
            $translation_def->lang = 'en';
            $translation_def->lang_key = $lang_key;
            $translation_def->lang_value = str_replace(array("\r", "\n", "\r\n"), "", $key);
            $translation_def->save();
            cache_clear();
        }

        // return user session lang
        $translation_locale = Cache::rememberForever("translations-{$lang}", function () use ($lang) {
            return Translation::where('lang', $lang)->pluck('lang_value', 'lang_key')->toArray();
        });

        if (isset($translation_locale[$lang_key]) && trim($translation_locale[$lang_key]) !== '') {
            return $addslashes ? addslashes(trim($translation_locale[$lang_key])) : trim($translation_locale[$lang_key]);
        }

        // return default lang if session lang not found
        $translations_default = Cache::rememberForever('translations-' . env('DEFAULT_LANGUAGE', 'en'), function () {
            return Translation::where('lang', env('DEFAULT_LANGUAGE', 'en'))->pluck('lang_value', 'lang_key')->toArray();
        });

        if (isset($translations_default[$lang_key])) {
            return $addslashes ? addslashes(trim($translations_default[$lang_key])) : trim($translations_default[$lang_key]);
        }

        // fallback to en lang
        if (!isset($translations_en[$lang_key])) {
            return trim($key);
        }

        return $addslashes ? addslashes(trim($translations_en[$lang_key])) : trim($translations_en[$lang_key]);
    }
}



if (!function_exists('cache_clear')) {
    /**
     * cache clear
     *
     * @return void
     */
    function cache_clear()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
    }
}


if (!function_exists('x_clean')) {
    function x_clean($text)
    {
        return strip_tags($text);
        // return clean($text, array('Attr.EnableID' => true));
    }
}

if (!function_exists('defaultLangCode')) {
    function defaultLangCode()
    {
        return 'en';
    }
}

if (!function_exists('getLocale')) {
    function getLocale()
    {
        return session()->get('locale') != null ? session()->get('locale') : defaultLangCode();
    }
}

if (!function_exists('getUserLocale')) {
    function getUserLocale()
    {
        return session()->get('user-locale') != null ? session()->get('user-locale') : defaultLangCode();
    }
}

if (!function_exists('activeLanguages')) {
    function activeLanguages()
    {
        return Language::where('status', config('settings.general_status.active'))->get();
    }
}

if (!function_exists('getAllLanguages')) {
    function getAllLanguages()
    {
        return Language::orderBy('id', 'asc')->get();
    }
}

if (!function_exists('bodyDirection')) {
    function bodyDirection()
    {
        return session()->get('body-direction') != null ? session()->get('body-direction') : 'ltr';
    }
}


if (!function_exists('p_trans')) {
    function p_trans($key, $lang = null, $fallback = null)
    {
        if ($lang == null) {
            $lang = getLocale();
        }

        $defaultLang = defaultLangCode();

        $page_content = PageContent::where('key', $key)->first();

        if ($lang == null || $lang == $defaultLang) {
            return ($page_content && $page_content->value) ? $page_content->value : $fallback;
        } else {
            $page_content_translation = PageContentTranslation::where('key', $key)
                ->where('lang', $lang)
                ->first();

            if ($page_content_translation && $page_content_translation->value) {
                return $page_content_translation->value;
            }
            return ($page_content && $page_content->value) ? $page_content->value : $fallback;
        }
    }
}

if (!function_exists('getGeneralSetting')) {
    function getGeneralSetting($key, $default = null)
    {
        return get_setting($key, $default);
    }
}

if (!function_exists('setGeneralSetting')) {
    function setGeneralSetting($key, $value = null)
    {
        return set_setting($key, $value);
    }
}

if (!function_exists('getCurrencyPosition')) {
    function getCurrencyPosition()
    {
        return config('settings.currency_position');
    }
}

if (!function_exists('format_amount')) {
    function format_amount($amount)
    {
        $decimals      = (int) get_setting('default_currency_number_of_decimal', 2);
        $decimalSep    = get_setting('default_currency_decimal_separator', '.');
        $thousandSep   = get_setting('default_currency_thousand_separator', ',');
        $symbol        = get_setting('default_currency_symbol', '$');
        $position      = (int) get_setting('default_currency_position', 1); // 1 = [Currency][Amount], 2 = [Amount][Currency]

        $formatted = number_format((float) $amount, $decimals, $decimalSep, $thousandSep);

        return $position === 2 ? $formatted . $symbol : $symbol . $formatted;
    }
}

if (!function_exists('xss_clean')) {
    /**
     * get the filtered text
     * @return mixed|string
     */
    function xss_clean($data)
    {
        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);
        // we are done...
        return $data;
    }
}
