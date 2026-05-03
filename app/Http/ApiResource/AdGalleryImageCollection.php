<?php

namespace App\Http\ApiResource;;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AdGalleryImageCollection extends ResourceCollection
{

    public function toArray($request)
    {
        return $this->collection->map(function ($data) {
            return [
                'image_id' => $data->image_id,
                'regular' => getFilePath($data->image_id, false, '240x160'),
                'zoom' => getFilePath($data->image_id, false, '825x550'),
                'type' => 'image'
            ];
        });
    }
}
