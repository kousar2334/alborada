<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Http\ApiResource\CityResource;
use App\Http\ApiResource\CountryResource;
use App\Http\ApiResource\SingleCityResource;
use App\Http\ApiResource\StateResource;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationController extends ApiController
{
    /**
     * Will return countries list
     */
    public function countries(): CountryResource
    {
        $default_country = getGeneralSetting('default_country');
        $countries = Country::where('id', $default_country)
            ->where('status', config('settings.general_status.active'))
            ->get();

        return new CountryResource($countries);
    }

    /**
     * Will return state of a country
     */
    public function states(Request $request): StateResource
    {
        $states = State::where('country_id', $request['country'])
            ->where('status', config('settings.general_status.active'))
            ->get();

        return new StateResource($states);
    }

    /**
     * Will return cities of a state
     */
    public function cities(Request $request): CityResource
    {
        $cities = City::where('state_id', $request['state'])
            ->where('status', config('settings.general_status.active'))
            ->get();

        return new CityResource($cities);
    }
    /**
     * Will return city Details
     */
    public function cityDetails(Request $request): JsonResource | SingleCityResource
    {
        $city = City::with(['state'])
            ->where('id', $request['city_id'])
            ->where('status', config('settings.general_status.active'))
            ->first();

        if ($city != null) {
            return new SingleCityResource($city);
        }
        return new JsonResource([
            'success' => false,
            'message' => 'City not found or inactive',
        ]);
    }
}
