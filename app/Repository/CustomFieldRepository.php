<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use App\Models\AdsCustomField;
use App\Models\AdsCustomFieldOption;
use App\Models\AdsCustomFieldOptionTranslation;
use App\Models\AdsCustomFieldTranslation;

class CustomFieldRepository
{

    //Will return ads custom field 
    public function adsCustomFiledList($request, $status = [1, 2])
    {
        $query = AdsCustomField::with(['category'])->orderBy('id', 'DESC');

        if ($request->has('search')) {
            $query = $query->where('title', 'like', '%' . $request['search'] . '%');
        }

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

        if ($per_page != null && $per_page == 'all') {
            return $query->whereIn('status', $status)->paginate($query->get()->count())->withQueryString();
        } else {
            return $query->whereIn('status', $status)->paginate($per_page)->withQueryString();
        }
    }

    /**
     * Will store new custom field
     */
    public function storeNewCustomField($request)
    {
        try {
            $field = new AdsCustomField();
            $field->title = xss_clean($request['title']);
            $field->type = $request['type'];
            $field->is_required = $request->has('is_required') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $field->is_filterable = $request->has('is_filterable') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            $field->default_value = xss_clean($request['default_value']);
            $field->status = $request['status'];
            $field->save();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Will delete custom field
     */
    public function deleteAField($id)
    {
        try {
            DB::beginTransaction();
            $field = AdsCustomField::findOrFail($id);
            $field->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will return custom field details
     */
    public function fieldDetails($id)
    {
        return AdsCustomField::with('field_translations')->findOrFail($id);
    }
    /**
     * Will update a custom field
     */
    public function updateAField($request)
    {
        try {
            DB::beginTransaction();

            if (!empty($request['lang']) && $request['lang'] != defaultLangCode()) {
                $translation = AdsCustomFieldTranslation::firstOrNew([
                    'field_id' => $request['id'],
                    'lang'     => $request['lang'],
                ]);
                $translation->title = xss_clean($request['title']);
                $translation->save();
            } else {
                $field = AdsCustomField::findOrFail($request['id']);
                $field->title = xss_clean($request['title']);
                $field->type = $request['type'];
                $field->is_required = $request->has('is_required') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $field->is_filterable = $request->has('is_filterable') ? config('settings.general_status.active') : config('settings.general_status.in_active');
                $field->default_value = xss_clean($request['default_value']);
                $field->status = $request['status'];
                $field->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Will apply bulk action
     */
    public function bulkAction($request)
    {
        try {
            foreach ($request['items'] as $item) {
                $field = AdsCustomField::find($item);
                //Delete
                if ($field != null & $request['action'] == 'delete_all') {
                    $field->delete();
                }
                //Active
                if ($field != null & $request['action'] == 'active') {
                    $field->status = config('settings.general_status.active');
                    $field->save();
                }
                //Inactive
                if ($field != null & $request['action'] == 'in_active') {
                    $field->status = config('settings.general_status.in_active');
                    $field->save();
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Will assign a category
     */
    public function assignACategory($request)
    {
        try {
            $field = AdsCustomField::findOrFail($request['id']);
            $field->category_id = $request['category'];
            $field->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Will return field options list
     */
    public function fieldOptions($field, $request, $status = [1, 2])
    {
        $query = AdsCustomFieldOption::with('field')->where('field_id', $field);

        if ($request->has('search') && $request['search'] != null) {
            $query = $query->where('value', 'like', '%' . $request['search'] . '%');
        }

        $per_page = $request->has('per_page') && $request['per_page'] != null ? $request['per_page'] : 10;

        $query = $query->orderBy('id', 'DESC');

        if ($per_page != null && $per_page == 'all') {
            return $query->whereIn('status', $status)->paginate($query->get()->count())->withQueryString();
        } else {
            return $query->whereIn('status', $status)->paginate($per_page)->withQueryString();
        }
    }
    /**
     * Will store custom field options
     */
    public function storeCustomFieldOption($request)
    {
        try {
            $option = new AdsCustomFieldOption();
            $option->field_id = $request['field'];
            $option->value = xss_clean($request['value']);
            $option->status = $request['status'];
            $option->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Will delete custom field options
     */
    public function deleteCustomFieldOption($request)
    {
        try {
            DB::beginTransaction();
            $option = AdsCustomFieldOption::findOrFail($request['id']);
            $option->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will return option details
     */
    public function optionDetails($id)
    {
        return AdsCustomFieldOption::with(['field', 'option_translations'])->findOrFail($id);
    }
    /**
     * Will update custom field option
     */
    public function updateCustomFieldOption($request)
    {
        try {
            DB::beginTransaction();

            if (!empty($request['lang']) && $request['lang'] != defaultLangCode()) {
                $translation = AdsCustomFieldOptionTranslation::firstOrNew([
                    'option_id' => $request['id'],
                    'lang'      => $request['lang'],
                ]);
                $translation->value = xss_clean($request['value']);
                $translation->save();
            } else {
                $option = AdsCustomFieldOption::findOrFail($request['id']);
                $option->value  = xss_clean($request['value']);
                $option->status = $request['status'];
                $option->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Will apply bulk action
     */
    public function optionBulkAction($request)
    {
        try {
            foreach ($request['items'] as $item) {
                $field = AdsCustomFieldOption::find($item);
                //Delete
                if ($field != null & $request['action'] == 'delete_all') {
                    $field->delete();
                }
                //Active
                if ($field != null & $request['action'] == 'active') {
                    $field->status = config('settings.general_status.active');
                    $field->save();
                }
                //Inactive
                if ($field != null & $request['action'] == 'in_active') {
                    $field->status = config('settings.general_status.in_active');
                    $field->save();
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
