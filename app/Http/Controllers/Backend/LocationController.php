<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CityRequest;
use App\Http\Requests\StateRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use Illuminate\Http\RedirectResponse;
use App\Repository\LocationRepository;
use BeyondCode\QueryDetector\Outputs\Json;

class LocationController extends Controller
{
    public function __construct(public LocationRepository $location_repository) {}
    /**
     * Will return country list page
     */
    public function countries(Request $request): View
    {
        $countries = $this->location_repository->countryList($request, [1, 0]);
        return view('backend.modules.location.countries.list', ['countries' => $countries]);
    }
    /**
     * Will redirect new country page
     */
    public function addNewCountry(): View
    {
        return view('backend.modules.location.countries.new');
    }
    /**
     * Will store new country
     */
    public function storeNewCountry(CountryRequest $request): JsonResponse
    {
        $res = $this->location_repository->storeCountryData($request->validated());

        if ($res) {
            return response()->json([
                'success' => true,
                'message' => __tr('Country added successfully'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __tr('Country added failed'),
        ]);
    }
    /**
     * Will redirect edit country page
     *
     */
    public function editCountry(Request $request): JsonResponse
    {
        $country = $this->location_repository->countryDetails($request->id);
        $lang = $request->input('lang', defaultLangCode());
        return response()->json([
            'success' => true,
            'html' => view('backend.modules.location.countries.edit', [
                'country'   => $country,
                'lang'      => $lang,
                'languages' => activeLanguages(),
            ])->render(),
        ]);
    }
    /**
     * Will update country
     * 
     */
    public function updateCountry(CountryRequest $request): JsonResponse
    {
        $res = $this->location_repository->updateCountry($request);
        if ($res == true) {
            return response()->json([
                'success' => true,
                'message' => __tr('Country updated successfully'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __tr('Country update failed'),
        ]);
    }
    /**
     * Will delete a country
     */
    public function deleteCountry(Request $request): RedirectResponse
    {
        $res = $this->location_repository->deleteCountry($request['id']);

        if ($res) {
            toastNotification('success', 'Country deleted successfully', 'Success');
        } else {
            toastNotification('error', 'Country delete failed', 'Error');
        }
        return to_route('classified.locations.country.list');
    }
    /**
     * Change language status
     */
    public function countryStatusChange(Request $request): JsonResponse
    {
        $res = $this->location_repository->changeCountryStatus($request->id);
        if ($res == true) {
            toastNotification('success',  __tr('Status updated successfully'), 'Success');
            return response()->json([
                'success' => true,
            ]);
        } else {
            toastNotification('success', __tr('Status update failed'), 'failed');
            return response()->json([
                'success' => false
            ]);
        }
    }
    /**
     * Will fire country bulk actions
     */
    public function countryBulkActions(Request $request): JsonResponse
    {
        $res = $this->location_repository->countryBulkAction($request);
        return response()->json(
            [
                'success' => $res,
            ]
        );
    }
    /**
     * Will return state list
     */
    public function states(Request $request): View
    {
        return view('backend.modules.location.states.list')->with(
            [
                'states' => $this->location_repository->statesList($request, [1, 0]),
                'countries' => $this->location_repository->countries([config('settings.general_status.active')])
            ]
        );
    }


    /**
     * Store new state
     */
    public function storeNewState(StateRequest $request): JsonResponse
    {
        $res = $this->location_repository->storeState($request->validated());
        if ($res == true) {
            return response()->json([
                'success' => true,
                'message' => __tr('State added successfully'),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __tr('Action failed'),
            ]);
        }
    }

    /**
     * Will delete a store
     */
    public function deleteState(Request $request): JsonResponse
    {
        $res = $this->location_repository->deleteState($request->id);
        if ($res == true) {
            return response()->json([
                'success' => true,
                'message' => __tr('State deleted successfully'),
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => __tr('Action failed'),
        ]);
    }

    /**
     * Will redirect state edit page
     */
    public function editState(Request $request): JsonResponse
    {
        $lang = $request->input('lang', defaultLangCode());
        return response()->json([
            'success' => true,
            'html' => view('backend.modules.location.states.edit', [
                'countries' => $this->location_repository->countries(),
                'state'     => $this->location_repository->stateDetails($request->id),
                'lang'      => $lang,
                'languages' => activeLanguages(),
            ])->render()
        ]);
    }
    /**
     * will update state
     */
    public function updateState(StateRequest $request): JsonResponse
    {
        $res = $this->location_repository->updateState($request);
        if ($res == true) {
            return response()->json([
                'success' => true,
                'message' => __tr('State updated successfully'),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __tr('Action failed'),
            ]);
        }
    }

    /**
     * Will return cities list
     * 
     * @return mixed
     */
    public function cities(Request $request)
    {
        return view('backend.modules.location.cities.list')->with(
            [
                'cities' => $this->location_repository->citiesList($request, [1, 0]),
                'countries' => $this->location_repository->countries([config('settings.general_status.active')]),
            ]
        );
    }

    /**
     * Will store new city information
     */
    public function storeNewCity(CityRequest $request): JsonResponse
    {
        $res = $this->location_repository->storeCity($request->validated());
        if ($res == true) {
            return response()->json([
                'success' => true,
                'message' => __tr('City added successfully'),
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => __tr('Action failed'),
        ]);
    }
    /**
     * Will delete city
     */
    public function deleteCity(Request $request): RedirectResponse
    {
        $res = $this->location_repository->deleteCity($request->id);
        if ($res == true) {
            toastNotification('success', __tr('City deleted successfully'), 'Success');
            return to_route('classified.locations.city.list');
        }
        toastNotification('error', __tr('Action failed'), 'Error');
        return to_route('classified.locations.city.list');
    }

    /**
     * Will redirect edit city page
     * 
     */
    public function editCity(Request $request): JsonResponse
    {
        $city = $this->location_repository->cityDetails($request->id);
        $lang = $request->input('lang', defaultLangCode());
        return response()->json(
            [
                'success' => true,
                'html' => view('backend.modules.location.cities.edit', [
                    'countries' => $this->location_repository->countries(),
                    'city'      => $city,
                    'states'    => $this->location_repository->statesByCountry($city->state?->country_id),
                    'lang'      => $lang,
                    'languages' => activeLanguages(),
                ])->render(),
            ]
        );
    }
    /**
     * will update city
     */
    public function updateCity(CityRequest $request): JsonResponse
    {
        $res = $this->location_repository->updateCity($request);
        if ($res == true) {
            return response()->json([
                'success' => true,
                'message' => __tr('City updated successfully'),
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => __tr('Action failed'),
        ]);
    }
}
