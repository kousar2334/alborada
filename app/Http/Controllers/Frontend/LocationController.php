<?php

namespace App\Http\Controllers\Frontend;

use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\City;

class LocationController extends Controller
{
    public function stateListofCountry(Request $request)
    {
        $country_id = $request->country_id;
        $query = State::with('state_translations')
            ->where('status', config('settings.general_status.active'));

        if ($country_id) {
            $query = $query->where('country_id', $country_id);
        }

        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE', '%' . $term . '%');
        }

        $states = $query->orderBy('id', 'asc')->paginate(2);

        $output = [];

        foreach ($states->items() as $state) {
            $item['id']   = $state->id;
            $item['text'] = $state->translation('name');
            array_push($output, $item);
        }

        $morePages = true;

        if (empty($states->nextPageUrl())) {
            $morePages = false;
        }
        $results = array(
            "results"    => $output,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    public function cityListofState(Request $request)
    {
        $state_id = $request->state_id;
        $query = City::with('city_translations')
            ->where('status', config('settings.general_status.active'));

        if ($state_id) {
            $query = $query->where('state_id', $state_id);
        }
        if ($request->has('term')) {
            $term = trim($request->term);
            $query = $query->where('name', 'LIKE', '%' . $term . '%');
        }

        $cities = $query->orderBy('id', 'asc')->paginate(2);

        $output = [];

        foreach ($cities->items() as $city) {
            $item['id']   = $city->id;
            $item['text'] = $city->translation('name');
            array_push($output, $item);
        }

        $morePages = true;

        if (empty($cities->nextPageUrl())) {
            $morePages = false;
        }
        $results = array(
            "results"    => $output,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}
