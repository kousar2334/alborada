<?php

namespace App\Http\ApiResource;;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\AdsCustomField;
use App\Http\ApiResource;

\AdsCustomFieldOptionCollection;

use App\Models\AdsCustomFieldOption;

class CustomerEditableAdResource extends JsonResource
{

    public $lang;

    public function __construct($resource, $lang)
    {
        parent::__construct($resource);
        $this->lang = $lang;
    }


    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uid' => $this->uid,
            'title' => $this->translation('title', $this->lang),
            'post_date' => $this->created_at->diffForHumans(),
            'old_thumbnail_id' => $this->thumbnail_image,
            'thumbnail' => getFilePath($this->thumbnail_image, true, '240x160'),
            "thumbnail_image" => "",
            'old_gallery_images' => new AdGalleryImageCollection($this->galleryImages),
            'gallery_images' => [],
            'condition' => $this->getConditionInfo(),
            'description' => $this->translation('description', $this->lang),
            'price' => $this->price,
            'cost' => $this->cost,
            'is_negotiable' => $this->is_negotiable == 1 ? true : false,
            'is_featured' => $this->is_featured == 1 ? true : false,
            'category' => $this->getCategoryInfo(),
            'city' => $this->cityInfo != null ? $this->getLocation() : null,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'contact_is_hide' => $this->contact_is_hide == 1 ? true : false,
            'tags' => $this->tags,
            'status' => $this->status,
            'is_payable' => $this->isPaymentPending(),
            'payment_method' => $this->getPaymentMethod(),
            'customFieldOptions' => $this->getCustomField(),
        ];
    }

    public function getCustomField()
    {
        $final_custom_filed_options = [];
        foreach ($this->customFields() as $item) {
            $field = AdsCustomField::find($item['flied_id']);

            if ($field != null) {
                $temp = [];
                $temp['id'] = $field->id;
                $temp['title'] = $field->translation('title', session()->get('api_locale'));
                $temp['is_required'] = $field->is_required;
                $temp['type'] = $field->type;
                $temp['options'] = new AdsCustomFieldOptionCollection($field->options);

                if ($field->type == config('settings.input_types.file') && $item['value'] != null) {
                    $temp['value']['image_id'] = $item['value'];
                    $temp['value']['image'] = getFilePath($item['value']);
                } elseif ($field->type == config('settings.input_types.select') && $item['value'] != null) {
                    $option = AdsCustomFieldOption::find($item['value']);
                    if ($option != null) {
                        $temp['value']['value'] = $option->translation('value', session()->get('api_locale'));
                        $temp['value']['id'] = $option->id;
                        $temp['value']['field_id'] = $option->field_id;
                    } else {
                        $temp['value'] = $item['value'];
                    }
                } else {
                    $temp['value'] = $item['value'];
                }


                array_push($final_custom_filed_options, $temp);
            }
        }
        return $final_custom_filed_options;
    }

    public function getPaymentMethod()
    {
        if (isActivePlugin('payment')) {
            return \Plugin\Payment\Models\PaymentMethod::find($this->payment_method);
        }

        return null;
    }

    public function getCategoryInfo()
    {
        if ($this->categoryInfo != null) {
            return [
                'id' => $this->categoryInfo->id,
                'name' => $this->categoryInfo->translation('title', session()->get('api_locale'))
            ];
        }

        return null;
    }

    public function getConditionInfo()
    {
        if ($this->condition != null) {
            return [
                'id' => $this->condition->id,
                'title' => $this->condition->translation('title', session()->get('api_locale'))
            ];
        }
        return null;
    }

    public function getAdditionalInfo()
    {
        return $this->customFields();
    }

    public function getLocation()
    {
        return [
            'id' => $this->cityInfo->id,
            'name' => $this->cityInfo->translation('name', session()->get('api_locale')),
            'state_id' => $this->cityInfo->state->id,
        ];
    }


    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
