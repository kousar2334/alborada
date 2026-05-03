<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Plugin\Location\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Ad;
use App\Models\AdsTag;
use App\Models\SavedAd;
use App\Http\Controllers\Api\ApiController;
use App\Models\AdsCategory;
use App\Models\AdsCondition;
use App\Models\AdsCustomField;
use App\Repository\AdRepository;
use App\Http\Requests\AdPostingRequest;
use App\Http\ApiResource\AdCollection;
use App\Http\ApiResource\AdsTagCollection;
use App\Http\ApiResource\SingleAdResource;
use App\Http\ApiResource\AdCategoryResource;
use App\Http\ApiResource\AdCategoryCollection;
use App\Http\ApiResource\AdsConditionCollection;
use App\Http\ApiResource\MegaCategoryCollection;
use App\Http\ApiResource\AdsCustomFieldCollection;
use App\Http\ApiResource\CustomerEditableAdResource;

class AdsController extends ApiController
{

    public function __construct(public AdRepository $adRepository) {}
    /**
     * Will return categories list
     */
    public function categories(Request $request): AdCategoryCollection
    {
        $categories = AdsCategory::with(['child', 'category_translations'])
            ->where('parent', $request['parent'])
            ->select('id', 'title', 'permalink', 'icon', 'image', 'parent', 'status')
            ->where('status', config('settings.general_status.active'))
            ->orderBy('id', 'ASC')
            ->get();

        return new AdCategoryCollection($categories);
    }
    /**
     * Will return all categories list
     */
    public function allCategories(Request $request)
    {
        $categories = AdsCategory::with(['child', 'category_translations'])
            ->whereNull('parent')
            ->select('id', 'title', 'permalink', 'icon', 'image', 'parent', 'status')
            ->where('status', config('settings.general_status.active'))
            ->orderBy('id', 'ASC')
            ->get();

        return new MegaCategoryCollection($categories);
    }
    /**
     * will return only parent categories
     */
    public function parentCategories(Request $request): AdCategoryCollection
    {
        $categories = AdsCategory::with(['child', 'category_translations'])
            ->where('parent', null)
            ->select('id', 'title', 'permalink', 'icon', 'image', 'parent', 'status')
            ->where('status', config('settings.general_status.active'))
            ->orderBy('id', 'ASC')
            ->get();

        return new AdCategoryCollection($categories);
    }
    /**
     * Will return single category details
     */
    public function categoryDetails(Request $request): mixed
    {
        $category = AdsCategory::where('id', $request['id'])->first();

        if ($category != null) {
            return new AdCategoryResource($category);
        }

        return $this->jsonError();
    }

    /**
     * Will return condition list
     */
    public function conditions(Request $request): AdsConditionCollection
    {
        $categories = AdsCondition::with(['condition_translations'])
            ->where('status', config('settings.general_status.active'))
            ->get();

        return new AdsConditionCollection($categories);
    }
    /**
     * Will return ads tags
     */
    public function tags(Request $request): AdsTagCollection
    {
        $tags = AdsTag::where('title', 'like', '%' . $request['search_key'] . '%')->get();

        return new AdsTagCollection($tags);
    }
    /**
     * Will return category custom field
     */
    public function customField(Request $request): AdsCustomFieldCollection
    {
        $query = AdsCustomField::with(['options' => function ($q) {
            $q->with('option_translations')
                ->where('status', config('settings.general_status.active'))
                ->select(['id as option_id', 'field_id', 'value']);
        }])
            ->where('category_id', $request['category_id'])
            ->where('status', config('settings.general_status.active'))
            ->select(['id', 'title', 'is_required', 'default_value', 'type', 'is_filterable']);

        if ($request->has('is_filterable')) {
            $query = $query->where('is_filterable', config('settings.general_status.active'));
        }
        $custom_fields = $query->get();


        return new AdsCustomFieldCollection($custom_fields);
    }

    /**
     * Will calculate payable amount
     */
    public function calculatePayable(Request $request): JsonResponse
    {
        try {
            if ($request->has('ad_id') && $request['ad_id'] != null) {
                $ad_details = Ad::find($request['ad_id']);
                if ($ad_details != null) {
                    return response()->json([
                        'success' => true,
                        'cost' => $ad_details->cost,
                        'is_payable' => $ad_details->isPaymentPending()
                    ]);
                }
            }
            $cost = getGeneralSetting('cost_per_ads');
            $free_ad_posting_limit = getGeneralSetting('free_ad_posting_limit');
            $total_free_ad_posting = Ad::where('user_id', auth('jwt-customer')->user()->id)
                ->where('is_featured', config('settings.general_status.in_active'))
                ->count();

            if ($total_free_ad_posting < $free_ad_posting_limit) {
                $cost = 0;
            }


            if ($request->has('is_featured') && $request['is_featured'] == config('settings.general_status.active')) {
                $cost = getGeneralSetting('cost_per_featured_ads');
            }


            return response()->json([
                'success' => true,
                'cost' => $cost,
                'total_free_ad_posting' => $total_free_ad_posting
            ]);
        } catch (\Exception $th) {
            return $this->jsonError();
        }
    }
    /**
     * Will store member ad
     */
    public function storeMemberAd(AdPostingRequest $request): JsonResponse
    {
        $res = $this->adRepository->storeNewAd($request, auth('jwt-customer')->user()->id);
        return response()->json($res);
    }
    /**
     * Will update member ad
     */
    public function updateMemberAd(AdPostingRequest $request): JsonResponse
    {
        $res = $this->adRepository->updateMemberAd($request, auth('jwt-customer')->user()->id);
        return response()->json($res);
    }
    /**
     * Will update ad status
     */
    public function updateMemberAdStatus(Request $request): JsonResponse
    {
        try {
            $ad_details = Ad::find($request['ad_id']);

            if ($ad_details != null) {
                $updated_status = $ad_details->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
                $ad_details->status = $updated_status;
                $ad_details->save();

                return response()->json([
                    'success' => true,
                    'status' => $updated_status,
                ]);
            }
            return $this->jsonError();
        } catch (\Exception $e) {
            return $this->jsonError();
        }
    }
    /**
     * Will return payment list
     */
    public function generatePaymentLink(Request $request)
    {
        try {
            $ad_details = Ad::find($request['ad_id']);
            $link = $this->adRepository->generatePaymentLink($request['payment_id'], $ad_details->cost, $ad_details->id, $ad_details->ad_details);
            return response()->json([
                'success' => true,
                'link' => $link
            ]);
        } catch (\Exception $e) {
            return $this->jsonError();
        }
    }
    /**
     * Will return add details
     */
    public function editAd(Request $request)
    {
        $details = Ad::where('uid', $request['uid'])->first();

        if ($details != null) {
            return new CustomerEditableAdResource($details, $request['lang']);
        }

        return $this->jsonError();
    }
    /**
     * Will return customer all ads 
     */
    public function customerAllAds(Request $request): AdCollection
    {
        $query = Ad::with(['ad_translations'])
            ->where('user_id', auth('jwt-customer')->user()->id);

        $perPage = $request->has('perPage') ? $request['perPage'] : 10;

        $ads = $query->orderBy('id', 'DESC')
            ->paginate($perPage);

        return new AdCollection($ads);
    }
    /**
     * Will save a customer add
     */
    public function saveAd(Request $request): JsonResponse
    {
        try {
            $ad = SavedAd::where('ad_id', $request['item_id'])
                ->where('user_id', auth('jwt-customer')->user()->id)
                ->first();
            if ($ad != null) {
                return response()->json([
                    'success' => false,
                    'message' => __tr('Already saved this add', session()->get('api_locale'))
                ]);
            } else {
                $new_ad = new SavedAd();
                $new_ad->ad_id = $request['item_id'];
                $new_ad->user_id = auth('jwt-customer')->user()->id;
                $new_ad->save();
                return response()->json([
                    'success' => true,
                    'message' => __tr('Ad saved successfully', session()->get('api_locale'))
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __tr('Ad saving failed', session()->get('api_locale'))
            ]);
        }
    }
    /**
     * Will return customer all saved ads 
     */
    public function savedAdList(Request $request): AdCollection
    {
        $query = Ad::with(['ad_translations'])
            ->whereIn('id', SavedAd::where('user_id', auth('jwt-customer')->user()->id)->pluck('ad_id'));

        $perPage = $request->has('perPage') ? $request['perPage'] : 10;

        $ads = $query->orderBy('id', 'DESC')
            ->paginate($perPage);

        return new AdCollection($ads);
    }
    /**
     * Will save a customer add
     */
    public function removeSaveAd(Request $request): JsonResponse
    {
        try {
            $ad = SavedAd::where('ad_id', $request['item_id'])
                ->where('user_id', auth('jwt-customer')->user()->id)
                ->first();
            if ($ad != null) {
                $ad->delete();
                return response()->json([
                    'success' => true,
                    'message' => __tr('Ad remove successfully', session()->get('api_locale'))
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __tr('Ad removing failed', session()->get('api_locale'))
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __tr('Ad removing failed', session()->get('api_locale'))
            ]);
        }
    }
    /**
     * Will delete member ad
     */
    public function deleteMemberAd(Request $request): JsonResponse
    {
        try {
            $ad = Ad::where('id', $request['id'])
                ->where('user_id', auth('jwt-customer')->user()->id)
                ->first();
            if ($ad != null) {
                $ad->delete();
                return response()->json([
                    'success' => true,
                    'message' => __tr('Ad deleted successfully', session()->get('api_locale'))
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __tr('Ad deleting failed', session()->get('api_locale'))
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __tr('Ad deleting failed', session()->get('api_locale'))
            ]);
        }
    }

    /**
     * Will return all ads 
     */
    public function AdListing(Request $request)
    {
        $query = Ad::with(['ad_translations', 'cityInfo' => function ($q) {
            $q->with(['city_translations'])->select(['id', 'name']);
        }, 'categoryInfo' => function ($q) {
            $q->with(['category_translations'])->select(['id', 'title', 'permalink']);
        }])
            ->select(['id', 'uid', 'title', 'city', 'category', 'thumbnail_image', 'price', 'is_negotiable', 'is_featured', 'created_at', 'status', 'cost']);

        //Filter By Category    
        if ($request->has('category_id') && $request['category_id'] != '') {
            $category = AdsCategory::with(['child'])
                ->where('id', $request['category_id'])
                ->select('id', 'parent', 'title')
                ->first();
            if ($category->child->count() > 0) {
                $all_categories = $this->nestedCategories($category->child);
                $all_categories_id = array_map(function ($item) {
                    return $item->id;
                }, $all_categories);
                $query = $query->whereIn('category', $all_categories_id);
            } else {
                $query = $query->where('category', $request['category_id']);
            }
        }
        //Filter By Featured items
        if ($request->has('type') && $request['type'] != '') {
            $query = $query->where('is_featured', config('settings.general_status.active'));
        }

        //Filter by Price
        if ($request->has('min_price') && $request->has('max_price') && $request['min_price'] != '' && $request['max_price'] != '') {
            $query = $query->whereBetween('price', [$request['min_price'], $request['max_price']])
                ->orderBy('price', 'ASC');
        }

        //Filter By State
        if ($request->has('state') && $request['state'] != '' && $request['city'] == '' && isActivePlugin('location')) {
            $cities = City::where('state_id', $request['state'])->pluck('id');
            $query = $query->whereIn('city', $cities);
        }

        //Filter By City
        if ($request->has('city') && $request['city'] != '') {
            $query = $query->where('city', $request['city']);
        }

        //Filter by condition
        if ($request->has('condition') && $request['condition'] != '') {
            $query = $query->where('item_condition', $request['condition']);
        }
        //Filter by Custom field

        if ($request->has('search_category') && $request['search_category'] != '') {
            $query = $query->where('category', $request['search_category']);
        }

        if ($request->has('search_location') && $request['search_location'] != '') {
            $query = $query->where('city', $request['search_location']);
        }

        if ($request->has('search_location') && $request['search_location'] != '') {
            $query = $query->where('city', $request['search_location']);
        }

        // Search by title/description
        if ($request->has('search_key') && $request['search_key'] != '') {
            $query = $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request['search_key'] . '%')
                    ->orWhere('description', 'like', '%' . $request['search_key'] . '%');
            });
        }

        // Filter by date posted
        if ($request->has('date_posted') && $request['date_posted'] != '') {
            $now = now();

            switch ($request['date_posted']) {
                case 'today':
                    $query = $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'yesterday':
                    $query = $query->whereDate('created_at', $now->subDay()->toDateString());
                    break;
                case 'last_week':
                    $query = $query->whereBetween('created_at', [
                        $now->subWeek()->startOfDay(),
                        now()->endOfDay()
                    ]);
                    break;
            }
        }

        //Sorting items
        if ($request->has('sorting') && $request['sorting'] != '') {

            if ($request['sorting'] == 'newest') {
                $query = $query->orderBy('id', 'DESC');
            }

            if ($request['sorting'] == 'lowToHigh') {
                $query = $query->orderBy('price', 'ASC');
            }

            if ($request['sorting'] == 'highToLow') {
                $query = $query->orderBy('price', 'DESC');
            }
        }

        $perPage = $request->has('perPage') ? $request['perPage'] : 10;

        $ads = $query->paginate($perPage);

        return new AdCollection($ads);
    }
    /**
     * Will return nested categories
     */
    public function nestedCategories($categories, $depth = 0): array
    {
        $flattened = [];
        foreach ($categories as $category) {
            $category->depth = $depth;
            $flattened[] = $category;

            $children = DB::table('tl_ads_categories')
                ->where('parent', $category->id)
                ->select('id', 'parent', 'title')
                ->where('status', config('settings.general_status.active'))
                ->get();

            if ($depth + 1 < 10) {
                $flattened = array_merge($flattened, $this->nestedCategories($children, $depth + 1));
            }
        }
        return $flattened;
    }
    /**
     * Will return add details
     */
    public function adDetails(Request $request): SingleAdResource |JsonResponse
    {
        $details = Ad::where('uid', $request['uid'])
            ->where('status', config('settings.general_status.active'))
            ->where('payment_status', config('settings.general_status.active'))
            ->first();
        if ($details != null) {
            $updated_counter = $details->view_counter + 1;
            $details->view_counter = $updated_counter;
            $details->save();
            return new SingleAdResource($details);
        }

        return $this->jsonError();
    }
    /**
     * Will return similar ads
     */
    public function similarAds(Request $request): AdCollection | JsonResponse
    {
        $details = Ad::where('uid', $request['uid'])
            ->where('status', config('settings.general_status.active'))
            ->where('payment_status', config('settings.general_status.active'))
            ->select('city', 'id', 'category', 'user_id')
            ->first();

        if ($details != null) {
            $similar_ads = Ad::with(['ad_translations', 'cityInfo' => function ($q) {
                $q->with(['city_translations'])->select(['id', 'name']);
            }, 'categoryInfo' => function ($q) {
                $q->with(['category_translations'])->select(['id', 'title', 'permalink']);
            }])
                ->select(['id', 'uid', 'title', 'city', 'category', 'thumbnail_image', 'price', 'is_negotiable', 'is_featured', 'created_at', 'status', 'cost'])
                ->whereNot('id', $details->id)
                ->orWhere('city', $details->city)
                ->orWhere('category', $details->category)
                ->orWhere('user_id', $details->user_id)
                ->take(8)
                ->get();

            return new AdCollection($similar_ads);
        }

        return $this->jsonError();
    }
}
