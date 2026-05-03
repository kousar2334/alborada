<?php

namespace App\Repository;

use App\Models\Media;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class MediaRepository
{

    /**
     * Will uploads media files
     * 
     * @param Array $files
     * @return Array
     */
    public function uploadMedia($request): array
    {
        try {
            $files = $request->file('file');
            $uploaded_files = [];
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $file_original_name = $file->getClientOriginalName();
                $file_name_array = explode('.', $file_original_name);
                $file_name_with_out_extension = $file_name_array[0];
                $file_size = $file->getSize();
                $disk = 'public';
                $destination_folder = "uploads/" . date("Y") . "/" . date("M");
                $path = $file->store($destination_folder, $disk);

                $media = new Media();
                $media->title = $file_name_with_out_extension;
                $media->file_name = $file_original_name;
                $media->alt = $file_name_with_out_extension;
                $media->mime_type = $extension;
                $media->size = $file_size;
                $media->path = $path;
                $media->disk = $disk;
                $media->uploaded_by = auth()->user() != null ? auth()->user()->id : null;
                $media->save();

                array_push($uploaded_files, $media->id);
            }


            return [
                'success' => true,
                'files' => $uploaded_files
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
            ];
        } catch (\Error $e) {
            return [
                'success' => false,
            ];
        }
    }

    public function croppingImage($file_full_path, $destination_path)
    {
        try {

            $image_source_path = 'public/' . $file_full_path;
            $full_path_array = explode('/', $file_full_path);
            $file_full_name = $full_path_array[sizeof($full_path_array) - 1];
            $file_full_name_array = explode('.', $file_full_name);
            $file_name = $file_full_name_array[0];
            $extension = $file_full_name_array[1];

            $modified_file_path_prefix = 'public/' . $destination_path . '/' . $file_name;

            $cropping_sizes = config('settings.image_cropping_sizes');

            if ($cropping_sizes != null) {
                foreach ($cropping_sizes as $size) {
                    $image_dimension = explode('x', $size);
                    $resizing_file_path = $modified_file_path_prefix . $size . '.' . $extension;
                    $width = $image_dimension[0];
                    $height = $image_dimension[1];
                    $img = Image::make($image_source_path);

                    $img->crop($width, $height);

                    $img->save($resizing_file_path);
                    if (File::exists($resizing_file_path)) {
                        chmod($resizing_file_path, 0777);
                    }
                }
                return $cropping_sizes;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Upload media out side media manager
     */
    public static function uploadSingleMedia($file)
    {
        $extension = $file->getClientOriginalExtension();
        $file_original_name = $file->getClientOriginalName();
        $file_name_array = explode('.', $file_original_name);
        $file_name_with_out_extension = $file_name_array[0];
        $file_size = $file->getSize();
        $disk = 'public';
        $destination_folder = "uploads/" . date("Y") . "/" . date("M");
        $path = $file->store($destination_folder, $disk);

        $media = new Media();
        $media->title = $file_name_with_out_extension;
        $media->file_name = $file_original_name;
        $media->alt = $file_name_with_out_extension;
        $media->mime_type = $extension;
        $media->size = $file_size;
        $media->path = $path;
        $media->disk = $disk;
        $media->uploaded_by = auth()->user() != null ? auth()->user()->id : null;

        $media->save();

        return $path;
    }
    /**
     * Will return media list
     * 
     * @param Array $request
     * @return Collections
     */
    public function mediaLists($request)
    {
        $query = Media::query();

        $selected_items = json_decode($request['selected_items'], true);

        // Search by title or file name
        if (!empty($request['search'])) {
            $search = $request['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('file_name', 'like', '%' . $search . '%');
            });
        }

        // Filter by file type
        if (!empty($request['filter_type']) && $request['filter_type'] !== 'all') {
            $typeMap = [
                'image'    => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
                'video'    => ['mp4', 'webm', 'avi', 'mov'],
                'document' => ['pdf', 'zip', 'doc', 'docx', 'xls', 'xlsx'],
            ];
            $mimeTypes = $typeMap[$request['filter_type']] ?? [];
            if (!empty($mimeTypes)) {
                $query->whereIn('mime_type', $mimeTypes);
            }
        }

        // Order selected items first, then newest
        if (sizeof($selected_items) > 0) {
            $placeholders = implode(',', array_fill(0, count($selected_items), '?'));
            $query->orderByRaw("CASE WHEN id IN ({$placeholders}) THEN 0 ELSE 1 END, id DESC", $selected_items);
        } else {
            $query->orderBy('id', 'DESC');
        }

        return $query->paginate($request['per_page']);
    }

    /**
     * Will return media list
     * 
     * @param Array $selected_items
     * @return Collections
     */
    public function selectedMediaDetails($selected_items = [])
    {
        $query = Media::with(['user' => function ($q) {
            $q->select('name', 'id');
        }]);
        $query = $query->whereIn('id', $selected_items);
        return $query->get();
    }
}
