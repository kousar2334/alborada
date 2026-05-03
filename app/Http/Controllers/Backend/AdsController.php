<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Repository\AdRepository;
use App\Repository\ConditionRepository;
use App\Http\Requests\AdminAdUpdateRequest;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\AdsTag;

class AdsController extends Controller
{

    public function __construct(public AdRepository $adRepository, public ConditionRepository $conditionRepository) {}
    /**
     * Will return ads list
     */
    public function adListing(Request $request): View
    {
        return view('backend.modules.ads.ads.list', ['ads' => $this->adRepository->adLists($request)]);
    }
    /**
     * Will return featured ad listing
     */
    public function featuredAdListing(Request $request)
    {
        return view('backend.modules.ads.ads.featured', ['ads' => $this->adRepository->adLists($request, true)]);
    }
    /**
     * Will delete an ad
     */
    public function deleteAd(Request $request): RedirectResponse
    {
        $res = $this->adRepository->deleteAnAd($request['id']);
        if ($res) {
            toastNotification('success', 'Ad deleted successfully', 'Success');

            if ($request->has('is_featured') && $request['is_featured'] != null) {
                return to_route('classified.ads.list.featured');
            }
            return to_route('classified.ads.list');
        }

        toastNotification('error', 'Ad delete failed', 'Error');
        return redirect()->back();
    }
    /**
     * Will redirect edit page
     */
    public function editAd($id): View
    {
        $ad_details = $this->adRepository->adDetails($id);
        $conditions = $this->conditionRepository->allAdsCondition();
        $countries = Country::orderBy('name', 'asc')->get();
        $tags = AdsTag::orderBy('title', 'asc')->get();

        // Get states and cities for the ad's current location
        $states = [];
        $cities = [];
        if ($ad_details->country_id) {
            $states = State::where('country_id', $ad_details->country_id)->orderBy('name', 'asc')->get();
        }
        if ($ad_details->state_id) {
            $cities = City::where('state_id', $ad_details->state_id)->orderBy('name', 'asc')->get();
        }

        return view('backend.modules.ads.ads.edit', [
            'ad_details' => $ad_details,
            'conditions' => $conditions,
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
            'tags' => $tags
        ]);
    }
    /**
     * Will update ad
     *
     */
    public function updateAd(AdminAdUpdateRequest $request)
    {
        $res = $this->adRepository->updateAd($request);

        if ($res) {
            toastNotification('success', 'Ad updated successfully', 'Success');
            return to_route('classified.ads.edit', ['id' => $request['id'], 'lang' => $request['lang']]);
        }

        toastNotification('error', 'Ad update failed', 'Error');
        return redirect()->back();
    }

    /**
     * Get states by country (AJAX)
     */
    public function getStates(Request $request)
    {
        $states = State::where('country_id', $request->country_id)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json($states);
    }

    /**
     * Get cities by state (AJAX)
     */
    public function getCities(Request $request)
    {
        $cities = City::where('state_id', $request->state_id)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return response()->json($cities);
    }
}
