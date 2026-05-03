<?php

namespace App\Repository;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Ad;
use App\Models\AdsTag;
use App\Models\AdHasTag;
use App\Models\AdGalleryImage;
use App\Models\AdsCustomField;

class AdRepository
{
    /**
     * Will return ads list
     */
    public function adLists($request, $is_featured = false)
    {
        $query = Ad::with(['userInfo', 'categoryInfo', 'cityInfo', 'stateInfo', 'countryInfo', 'userInfo']);

        if ($request->has('status') && $request['status'] != null) {
            $query = $query->where('status', $request['status']);
        }

        if ($request->has('payment_status') && $request['payment_status'] != null) {
            $query = $query->where('payment_status', $request['payment_status']);
        }

        if ($request->has('search') && $request['search'] != null) {
            $query = $query->where('title', 'like', '%' . $request['search'] . '%');
        }

        if ($is_featured) {
            $query = $query->where('is_featured', config('settings.general_status.active'));
        }

        $perPage = 10;
        if ($request->has('per_page') && $request['per_page'] != null) {
            if ($request['per_page'] == 'all') {
                $perPage = $query->count();
            } else {
                $perPage = $request['per_page'];
            }
        }

        return $query->orderBy('id', 'DESC')->paginate($perPage)->withQueryString();
    }
    /**
     * Will store new ad
     */
    public function storeNewAd($request, $user_id)
    {
        try {
            DB::beginTransaction();
            //Prepare custom field
            $custom_field = json_decode($request['custom_field'], true);
            $final_custom_input = [];
            foreach ($custom_field as $key => $item) {
                $temp['flied_id'] = $item['id'];
                $temp['type'] = $item['type'];
                if ($item['type'] == config('settings.input_types.select')) {
                    if (!empty($item['value'])) {
                        $value = $item['value']['id'];
                        $temp['value'] = $value;
                    }
                } else {
                    $temp['value'] = $item['value'];
                }
                array_push($final_custom_input, $temp);
            }

            $status = $request['cost'] > 0 ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $payment_status = $request['cost'] > 0 ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $ad = new Ad();
            $ad->user_id = $user_id;
            $ad->title = xss_clean($request['title']);
            $ad->description = xss_clean($request['description']);
            $ad->category = $request['category'];
            $ad->city = $request['city'];
            $ad->item_condition = $request['condition'];
            $ad->price = $request['price'];
            $ad->is_negotiable = $request['is_negotiable'];
            $ad->contact_email = xss_clean($request['contact_email']);
            $ad->contact_phone = xss_clean($request['contact_phone']);
            $ad->contact_is_hide = $request['contact_is_hide'];
            $ad->is_featured = $request['is_featured'];
            $ad->cost = $request['cost'];
            $ad->payment_method = $request['payment_method'];
            $ad->status = $status;
            $ad->payment_status = $payment_status;
            $ad->thumbnail_image = saveFileInStorage($request['thumbnail_image']);
            $ad->custom_field = json_encode($final_custom_input);
            $ad->save();

            //Store ad tag
            $this->storeAdTags($request, $ad->id);

            //Store gallery image
            $this->storeGalleryImages($request, $ad->id);

            DB::commit();
            if ($request['cost'] > 0) {
                return [
                    'success' => true,
                    'link' => $this->generatePaymentLink($ad->payment_method, $ad->cost, $ad->id, $user_id)
                ];
            } else {
                return [
                    'success' => true,
                    'message' => "Ad posting successfully",
                    'link' => '/ads' . '/' . Str::slug($ad->title) . '/' . $ad->uid,
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return [
                'success' => false,
            ];
        } catch (\Error $e) {
            DB::rollBack();
            dd($e);
            return [
                'success' => false,
            ];
        }
    }
    /**
     * Will store new ad
     */
    public function updateMemberAd($request, int $user_id): array
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != getDefaultLang()) {
                $ad_translation = AdTranslation::firstOrNew(['ad_id' => $request['id'], 'lang' => $request['lang']]);
                $ad_translation->title = xss_clean($request['title']);
                $ad_translation->description = xss_clean($request['description']);
                $ad_translation->save();
                DB::commit();
                return [
                    'success' => true,
                ];
            } else {
                //Prepare custom field
                $custom_field = json_decode($request['custom_field'], true);
                $final_custom_input = [];
                foreach ($custom_field as $key => $item) {
                    $temp['flied_id'] = $item['id'];
                    $temp['type'] = $item['type'];
                    if ($item['type'] == config('settings.input_types.select')) {
                        if (!empty($item['value'])) {
                            $value = $item['value']['id'];
                            $temp['value'] = $value;
                        }
                    } elseif ($item['type'] == config('settings.input_types.file')) {
                        if (!empty($item['value'])) {
                            if (isset($item['value']['image_id'])) {
                                $value = $item['value']['image_id'];
                                $temp['value'] = $value;
                            } else {
                                $temp['value'] = $item['value'];
                            }
                        } else {
                            $temp['value'] = $item['value'];
                        }
                    } else {
                        $temp['value'] = $item['value'];
                    }
                    array_push($final_custom_input, $temp);
                }

                $ad = Ad::findOrFail($request['id']);
                $ad->user_id = $user_id;
                $ad->title = xss_clean($request['title']);
                $ad->description = xss_clean($request['description']);
                $ad->category = $request['category'];
                $ad->city = $request['city'];
                $ad->item_condition = $request['condition'];
                $ad->price = $request['price'];
                $ad->is_negotiable = $request['is_negotiable'];
                $ad->contact_email = xss_clean($request['contact_email']);
                $ad->contact_phone = xss_clean($request['contact_phone']);
                $ad->contact_is_hide = $request['contact_is_hide'];
                $ad->is_featured = $request['is_featured'];

                if ($request->has('thumbnail_image') && $request['thumbnail_image'] != null) {
                    $ad->thumbnail_image = saveFileInStorage($request['thumbnail_image']);
                }
                $ad->custom_field = json_encode($final_custom_input);
                $ad->save();

                //Store ad tag
                $this->storeAdTags($request, $ad->id);

                //Store gallery image
                $this->storeGalleryImages($request, $ad->id);

                DB::commit();
                if ($request['is_payable'] == config('settings.general_status.active')) {
                    $ad->cost = $request['cost'];
                    $ad->payment_method = $request['payment_method'];
                    $ad->save();
                    return [
                        'success' => true,
                        'link' => $this->generatePaymentLink($ad->payment_method, $ad->cost, $ad->id, $user_id)
                    ];
                } else {
                    return [
                        'success' => true
                    ];
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
            ];
        }
    }
    /**
     * Will delete a ad
     */
    public function deleteAnAd(int $id)
    {
        try {
            DB::beginTransaction();
            $ad = Ad::find($id);
            if ($ad != null) {
                $ad->delete();
                DB::commit();
                return true;
            }
            DB::commit();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will return ad details
     */
    public function adDetails($id)
    {
        $details = Ad::with(['tags', 'categoryInfo', 'galleryImages', 'countryInfo', 'stateInfo', 'cityInfo', 'userInfo'])->find($id);

        return $details;
    }
    /**
     * Will update an ad
     */
    public function updateAd(Request $request)
    {
        try {
            DB::beginTransaction();

            //Prepare custom field
            $final_custom_input = [];
            if ($request->has('custom_field') && $request['custom_field'] != null) {
                $custom_field = $request['custom_field'];
                foreach ($custom_field as $key => $value) {
                    $field = AdsCustomField::find($key);
                    if ($field != null) {
                        $temp['flied_id'] = $key;
                        $temp['type'] = $field->type;
                        $temp['value'] = $value;
                        array_push($final_custom_input, $temp);
                    }
                }
            }

            foreach ($request->all() as $key => $value) {
                if (str_contains($key, 'customfile_')) {
                    $key_array = explode('_', $key);
                    $field_id = $key_array[sizeof($key_array) - 1];
                    $field = AdsCustomField::find($field_id);
                    if ($field != null) {
                        $temp['flied_id'] = $field_id;
                        $temp['type'] = $field->type;
                        $temp['value'] = $value;
                        array_push($final_custom_input, $temp);
                    }
                }
            }

            $ad = Ad::findOrFail($request['id']);
            $ad->title = xss_clean($request['title']);
            $ad->description = xss_clean($request['description']);
            $ad->category_id = $request['category'];
            $ad->condition_id = $request['condition'];
            $ad->price = $request['price'];
            $ad->is_negotiable = $request->has('is_negotiable') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $ad->contact_is_hide = $request->has('contact_is_hide') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $ad->is_featured = $request->has('is_featured') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $ad->status = $request['status'];
            $ad->thumbnail_image = $request['thumbnail_image'];
            $ad->custom_field = json_encode($final_custom_input);

            // Update location fields
            if ($request->has('country')) {
                $ad->country_id = $request['country'];
            }
            if ($request->has('state')) {
                $ad->state_id = $request['state'];
            }
            if ($request->has('city')) {
                $ad->city_id = $request['city'];
            }
            if ($request->has('address')) {
                $ad->address = xss_clean($request['address']);
            }

            // Update contact fields
            if ($request->has('contact_email')) {
                $ad->contact_email = xss_clean($request['contact_email']);
            }
            if ($request->has('contact_phone')) {
                $ad->contact_phone = xss_clean($request['contact_phone']);
            }

            // Update video URL
            if ($request->has('video_url')) {
                $ad->video_url = xss_clean($request['video_url']);
            }

            $ad->save();

            //Store or update ad tag
            AdHasTag::where('ad_id', $ad->id)->delete();
            if ($request->has('tags') && $request['tags'] != null) {
                foreach ($request['tags'] as $key => $tag) {
                    $tag_info = AdsTag::find($tag);
                    if ($tag_info != null) {
                        $tag_id = $tag_info->id;
                    } else {
                        $new_tag = new AdsTag();
                        $new_tag->title = $tag;
                        $new_tag->save();
                        $tag_id = $new_tag->id;
                    }

                    $ad_tag = AdHasTag::firstOrCreate(['ad_id' => $ad->id, 'tag_id' => $tag_id]);
                    $ad_tag->save();
                }
            }

            //Store or update gallery image
            AdGalleryImage::where('ad_id', $ad->id)->delete();
            if ($request->has('gallery_images') && $request['gallery_images'] != null) {
                $images = explode(',', $request['gallery_images']);
                foreach ($images as $image) {
                    $ad_gallery_image = new AdGalleryImage();
                    $ad_gallery_image->image_id = $image;
                    $ad_gallery_image->ad_id = $ad->id;
                    $ad_gallery_image->save();
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Wil store ad tags
     */
    public function storeAdTags($request, $ad_id)
    {
        AdHasTag::where('ad_id', $ad_id)->delete();
        $tags = json_decode($request['tags'], true);
        foreach ($tags as $key => $tag) {

            if (isset($tag['id'])) {
                $tag_id = $tag['id'];
            } else {
                $new_tag = new AdsTag();
                $new_tag->title = $tag;
                $new_tag->save();
                $tag_id = $new_tag->id;
            }

            $ad_tag = AdHasTag::firstOrCreate(['ad_id' => $ad_id, 'tag_id' => $tag_id]);
            $ad_tag->save();
        }
    }
    /**
     * Will store gallery images
     */
    public function storeGalleryImages($request, $ad_id)
    {
        AdGalleryImage::where('ad_id', $ad_id)->delete();

        if ($request->has('gallery_images') && $request['gallery_images'] != null) {
            $images = $request->file('gallery_images');
            foreach ($images as $image) {
                $image = saveFileInStorage($image);
                $ad_gallery_image = new AdGalleryImage();
                $ad_gallery_image->image_id = $image;
                $ad_gallery_image->ad_id = $ad_id;
                $ad_gallery_image->save();
            }
        }

        if ($request->has('old_gallery_images') && $request['old_gallery_images'] != null) {
            $old_images = json_decode($request['old_gallery_images'], true);

            foreach ($old_images as $image) {
                $ad_gallery_image = new AdGalleryImage();
                $ad_gallery_image->image_id = $image['image_id'];
                $ad_gallery_image->ad_id = $ad_id;
                $ad_gallery_image->save();
            }
        }
    }

    /**
     * Generate payment link
     */
    public function generatePaymentLink($payment_id, $amount, $ad_id, $user_id)
    {
        $success_url = $this->adDetailsPage($ad_id);
        $base_url = url('/');
        if (isActivePlugin('payment') && $payment_id != null && $amount > 0) {
            $payment_method = \Plugin\Payment\Models\PaymentMethod::find($payment_id);
            $url = $base_url . '/' . 'payment/' . Str::slug($payment_method->name) . '/pay';
            session()->put('payment_type', 'ad_posting');
            session()->put('ad_id', $ad_id);
            session()->put('payable_amount', $amount);
            session()->put('payment_method', $payment_method->name);
            session()->put('payment_method_id', $payment_method->id);
            session()->put('redirect_url', $url);
            session()->put('success_url', $success_url);
            session()->put('user_id', $user_id);

            return $url;
        }
        return $base_url;
    }
    /**
     * Will return ad details page url
     */
    public function adDetailsPage($ad_id)
    {
        $ad_details = Ad::find($ad_id);
        if ($ad_details != null) {
            return url('/ads') . '/' . Str::slug($ad_details->title) . '/' . $ad_details->uid;
        }

        return '/';
    }
}
