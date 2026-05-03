<?php

namespace App\Http\ApiResource;;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\AdsCustomField;
use App\Models\AdsCustomFieldOption;

class AdAdditionalInfoCollection extends ResourceCollection
{

    public function toArray($request)
    {
        return $this->collection->map(function ($data) {
            return [
                'option' => $this->fieldTitle($data['flied_id']),
                'type' => $data['type'],
                'value' => $this->value($data['value'], $data['type'])
            ];
        });
    }

    public function fieldTitle($id)
    {
        $field = AdsCustomField::find($id);
        if ($field != null) {
            return $field->translation('title', session()->get('api_locale'));
        }
        return null;
    }

    public function value($value, $type)
    {
        switch ($type) {
            case config('settings.input_types.text'):
                return $value;
                break;
            case config('settings.input_types.number'):
                return $value;
                break;

            case config('settings.input_types.select'):
                $option = AdsCustomFieldOption::find($value);
                if ($option != null) {
                    return $option->translation('value', session()->get('api_locale'));
                }
                return null;
                break;
            case config('settings.input_types.text_area'):
                return $value;
                break;
            case config('settings.input_types.checkbox'):
                return $value != null ? $this->getCheckboxValues($value) : null;
                break;
            case config('settings.input_types.radio'):
                $option = AdsCustomFieldOption::find($value);
                if ($option != null) {
                    return $option->translation('value', session()->get('api_locale'));
                }
                return null;
                break;
            case config('settings.input_types.file'):
                return getFilePath($value);
                break;
            case config('settings.input_types.date'):
                return $value;
                break;
            case config('settings.input_types.date_range'):
                return $value;
                break;
            default:
                return $value;
                break;
        }
    }

    public function getCheckboxValues($ids)
    {
        $res = [];
        foreach ($ids as $id) {
            $option = AdsCustomFieldOption::find($id);
            $title = "";
            if ($option != null) {
                $title = $option->translation('value', session()->get('api_locale'));
            }

            array_push($res, $title);
        }

        return implode(', ', $res);
    }
}
