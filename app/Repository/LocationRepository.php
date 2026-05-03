<?php

namespace App\Repository;

use App\Models\City;
use App\Models\State;
use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Models\CityTranslation;
use App\Models\StateTranslation;
use App\Models\CountryTranslation;
use Illuminate\Http\Request;

class LocationRepository
{
    /**
     * will return country list
     *
     * @return Collections
     */
    public function countries($status = [1, 2])
    {
        return Country::withCount('states')->orderBy('id', 'ASC')->whereIn('status', $status)->get();
    }

    /**
     * Wll return countries
     */
    public function countryList($request, $status = [1, 0])
    {
        $query = Country::query();
        if ($request->has('search_key')) {
            $query = $query->where('name', "like", '%' . $request['search_key'] . '%');
        }

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;
        if ($per_page != null && $per_page == 'all') {
            return $query->orderBy('name', 'ASC')->whereIn('status', $status)->paginate($query->get()->count())->withQueryString();
        } else {
            return $query->orderBy('name', 'ASC')->whereIn('status', $status)->paginate($per_page)->withQueryString();
        }
    }
    /**
     * Will store new country
     *
     * @param Request $data
     * @return bool
     */
    public function storeCountryData($data)
    {
        try {
            $country = new Country();
            $country->name = $data['name'];
            $country->code = $data['code'];
            $country->status = $data['status'];
            $country->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * will return country details
     *
     * @param Int $id
     * @return Collection
     */
    public function countryDetails($id)
    {
        return Country::with('country_translations')->findOrFail($id);
    }
    /**
     * will update country
     *
     * @param Request $request
     * @return bool
     */
    public function updateCountry($request)
    {
        try {
            DB::beginTransaction();
            $country = Country::findOrFail($request['id']);
            $lang = $request['lang'] ?? defaultLangCode();

            if ($lang != defaultLangCode()) {
                $translation = CountryTranslation::firstOrNew([
                    'country_id' => $country->id,
                    'lang'       => $lang,
                ]);
                $translation->name = $request['name'];
                $translation->save();
            } else {
                $country->name   = $request['name'];
                $country->code   = $request['code'];
                $country->status = $request['status'];
                $country->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Change country status
     *
     * @param Int $id
     * @return bool
     */
    public function changeCountryStatus($id)
    {
        try {
            DB::beginTransaction();
            $country = Country::findOrFail($id);
            $status = $country->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $country->status = $status;
            $country->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will applied country bulk action
     *
     * @param Request $request
     * @return bool
     */
    public function countryBulkAction($request)
    {
        try {
            DB::beginTransaction();

            //Active Country status
            if ($request['action'] == 'active') {
                $status = config('settings.general_status.active');
                Country::whereIn('id', $request['items'])
                    ->update(['status' => $status]);
            }
            //Inactive Country status
            if ($request['action'] == 'in_active') {
                $status = config('settings.general_status.in_active');
                Country::whereIn('id', $request['items'])
                    ->update(['status' => $status]);
            }

            //Delete selected countries
            if ($request['action'] == 'delete_all') {
                Country::whereIn('id', $request['items'])->delete();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will delete a country
     *
     * @param Int $id
     * @return bool
     */
    public function deleteCountry($id)
    {
        try {
            DB::beginTransaction();
            $country = Country::findOrFail($id);
            $country->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will return state list
     *
     * @return collections
     */
    public function statesList($request, $status = [1, 0])
    {
        $query = State::with('country');

        if ($request->has('search_key')) {
            $query = $query->where('name', 'like', '%' . $request['search_key'] . '%');
        }
        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;
        if ($per_page != null && $per_page == 'all') {
            return $query->orderBy('name', 'ASC')->whereIn('status', $status)->paginate($query->get()->count())->withQueryString();
        } else {
            return $query->orderBy('name', 'ASC')->whereIn('status', $status)->paginate($per_page)->withQueryString();
        }
    }
    /**
     * Will return state list
     *
     * @return collections
     */
    public function states($status = [1, 2])
    {
        return State::with(['country', 'state_translations'])->whereIn('status', $status)->orderBy('id', 'DESC')->get();
    }
    /**
     * Store new State
     *
     * @param Request $request
     * @return bool
     */
    public function storeState($request)
    {
        try {
            DB::beginTransaction();
            $state = new State;
            $state->name = $request['name'];
            $state->country_id = $request['country'];
            $state->status = $request['status'];
            $state->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will delete state
     *
     * @param Int $id
     * @return bool
     */
    public function deleteState($id)
    {
        try {
            DB::beginTransaction();
            $state = State::findOrFail($id);
            $state->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * will return state details
     *
     * @param Int $id
     * @return Collection
     */
    public function stateDetails($id)
    {
        return State::with('state_translations')->findOrFail($id);
    }
    /**
     * will update state
     *
     * @param Request $request
     * @return bool
     */
    public function updateState($request)
    {
        try {
            DB::beginTransaction();
            $state = State::findOrFail($request['id']);
            $lang = $request['lang'] ?? defaultLangCode();

            if ($lang != defaultLangCode()) {
                $translation = StateTranslation::firstOrNew([
                    'state_id' => $state->id,
                    'lang'     => $lang,
                ]);
                $translation->name = $request['name'];
                $translation->save();
            } else {
                $state->name       = $request['name'];
                $state->country_id = $request['country'];
                $state->status     = $request['status'];
                $state->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    public function statesByCountry($country_id = null)
    {
        return State::when($country_id, function ($query) use ($country_id) {
            $query->where('country_id', $country_id);
        })->where('status', config('settings.general_status.active'))->orderBy('name', 'ASC')->get();
    }

    /**
     * Will return cities
     *
     * @return City
     */
    public function cities()
    {
        return City::with('state')->orderBy('id', 'DESC')->get();
    }
    /**
     * Will return cities
     *
     * @return Collections
     */
    public function citiesList($request, $status = [1, 0])
    {
        $query = City::with('state');
        if ($request->has('search_key')) {
            $query = $query->where('name', 'like', '%' . $request['search_key'] . '%');
        }

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;
        if ($per_page != null && $per_page == 'all') {
            return $query->orderBy('name', 'ASC')->whereIn('status', $status)->paginate($query->get()->count())->withQueryString();
        } else {
            return $query->orderBy('name', 'ASC')->whereIn('status', $status)->paginate($per_page)->withQueryString();
        }
    }
    /**
     * Store new city
     *
     * @param Request $request
     * @return bool
     */
    public function storeCity($request)
    {
        try {
            DB::beginTransaction();
            $city = new City;
            $city->name = $request['name'];
            $city->state_id = $request['state_id'];
            $city->status = config('settings.general_status.active');
            $city->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * will delete city
     * @return bool
     */
    public function deleteCity(int $id)
    {
        try {
            DB::beginTransaction();
            $city = City::findOrFail($id);
            $city->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Change city's status
     * @return bool
     */
    public function changeCityStatus(int $id)
    {
        try {
            DB::beginTransaction();
            $city = City::findOrFail($id);
            $status = $city->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $city->status = $status;
            $city->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will return city details
     * @return City
     */
    public function cityDetails(int $id)
    {
        return City::with('city_translations')->findOrFail($id);
    }
    /**
     * will update city
     *
     * @param Request $request
     * @return bool
     */
    public function updateCity($request)
    {
        try {
            DB::beginTransaction();
            $city = City::findOrFail($request['id']);
            $lang = $request['lang'] ?? defaultLangCode();

            if ($lang != defaultLangCode()) {
                $translation = CityTranslation::firstOrNew([
                    'city_id' => $city->id,
                    'lang'    => $lang,
                ]);
                $translation->name = $request['name'];
                $translation->save();
            } else {
                $city->name     = $request['name'];
                $city->state_id = $request['state_id'];
                $city->status   = $request['status'];
                $city->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will applied cities bulk action
     *
     * @param Request $request
     * @return bool
     */
    public function citiesBulkAction($request)
    {
        try {
            DB::beginTransaction();
            //Active Cities status
            if ($request['action'] == 'active') {
                $status = config('settings.general_status.active');
                City::whereIn('id', $request['items'])->update(['status' => $status]);
            }
            //Inactive Cities status
            if ($request['action'] == 'in_active') {
                $status = config('settings.general_status.in_active');
                City::whereIn('id', $request['items'])->update(['status' => $status]);
            }

            //Delete selected cities
            if ($request['action'] == 'delete_all') {
                City::whereIn('id', $request['items'])->delete();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Error $e) {
            DB::rollBack();
            return false;
        }
    }
}
