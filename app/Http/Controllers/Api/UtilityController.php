<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiResource\ShareOptionsCollection;
use App\Http\ApiResource\TipsCollection;
use App\Http\Controllers\Api\ApiController;
use App\Models\AdShareOption;
use App\Models\QuickSellTip;
use App\Models\SafetyTips;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UtilityController extends ApiController
{

    /**
     * Will store image
     */
    public function storeImage(Request $request): JsonResponse
    {
        try {
            $file = saveFileInStorage($request['file'], false, 'custom-input');
            return response()->json([
                'success' => true,
                'file' => $file
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will return safety tips
     */
    public function safetyTips(): TipsCollection
    {
        $tips = SafetyTips::with(['tip_translations'])
            ->where('status', config('settings.general_status.active'))
            ->get();

        return new TipsCollection($tips);
    }
    /**
     * Will return quick sell tips
     */
    public function quickSellTips(): TipsCollection
    {
        $tips = QuickSellTip::with(['tip_translations'])
            ->where('status', config('settings.general_status.active'))
            ->get();

        return new TipsCollection($tips);
    }
    /**
     * Will return share options
     */
    public function shareOptions(): ShareOptionsCollection
    {
        $options = AdShareOption::where('status', config('settings.general_status.active'))->get();

        return new ShareOptionsCollection($options);
    }
}
