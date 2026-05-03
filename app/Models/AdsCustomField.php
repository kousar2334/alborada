<?php

namespace App\Models;

use App\Models\AdsCategory;
use App\Models\AdsCustomFieldOption;
use App\Models\AdsCustomFieldTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdsCustomField extends Model
{
    protected $fillable = ['title', 'type', 'category_id', 'is_required', 'is_filterable', 'default_value', 'status'];

    public function options(): HasMany
    {
        return $this->hasMany(AdsCustomFieldOption::class, 'field_id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(AdsCategory::class, 'id', 'category_id');
    }

    public function field_translations(): HasMany
    {
        return $this->hasMany(AdsCustomFieldTranslation::class, 'field_id');
    }

    public function translation($field = '', $lang = false)
    {
        $lang = $lang == false ? session()->get('locale') : $lang;
        $translation = $this->field_translations->where('lang', $lang)->first();
        return $translation != null ? $translation->$field : $this->$field;
    }

    public function get_type()
    {
        switch ($this->type) {
            case config('settings.input_types.text'):
                return 'Text';
                break;
            case config('settings.input_types.number'):
                return 'Number';
                break;
            case config('settings.input_types.select'):
                return 'Select';
                break;
            case config('settings.input_types.text_area'):
                return 'Text Area';
                break;
            case config('settings.input_types.checkbox'):
                return 'Checkbox';
                break;
            case config('settings.input_types.radio'):
                return 'Radio';
                break;
            case config('settings.input_types.file'):
                return 'File';
                break;
            case config('settings.input_types.date'):
                return 'Date';
                break;
            case config('settings.input_types.date_range'):
                return 'Date Range';
                break;
            default:
                return 'Unknown';
                break;
        }
    }

    public function has_options()
    {
        switch ($this->type) {
            case config('settings.input_types.text'):
                return config('settings.general_status.in_active');
                break;
            case config('settings.input_types.number'):
                return config('settings.general_status.in_active');
                break;

            case config('settings.input_types.select'):
                return config('settings.general_status.active');
                break;
            case config('settings.input_types.text_area'):
                return config('settings.general_status.in_active');
                break;
            case config('settings.input_types.checkbox'):
                return config('settings.general_status.active');
                break;
            case config('settings.input_types.radio'):
                return config('settings.general_status.active');
                break;
            case config('settings.input_types.file'):
                return config('settings.general_status.in_active');
                break;
            case config('settings.input_types.date'):
                return config('settings.general_status.in_active');
                break;
            case config('settings.input_types.date_range'):
                return config('settings.general_status.in_active');
                break;
            default:
                return config('settings.general_status.in_active');
                break;
        }
    }
}
