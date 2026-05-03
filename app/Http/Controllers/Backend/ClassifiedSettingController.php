<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Models\SafetyTips;
use App\Http\Requests\SafetyTipsRequest;
use App\Models\AdShareOption;
use App\Models\QuickSellTip;
use App\Models\QuickSellTipTranslation;
use App\Models\SafetyTipTranslation;

class ClassifiedSettingController extends Controller
{
    /**
     * Will return General Setting
     */
    public function generalSettings(): View
    {
        return view('backend.modules.settings.general_settings');
    }

    /**
     * Will return Currency Setting
     */
    public function currencySettings(): View
    {
        return view('backend.modules.settings.currency_settings');
    }

    /**
     * Will return Member Setting
     */
    public function memberSettings(): View
    {
        return view('backend.modules.settings.member_settings');
    }

    /**
     * Will update member settings
     */
    public function updateMemberSetting(Request $request)
    {
        try {
            DB::beginTransaction();
            $member_auto_approved = $request->has('member_auto_approved') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            setGeneralSetting('member_auto_approved', $member_auto_approved);

            $member_email_verification = $request->has('member_email_verification') ? config('settings.general_status.active') : config('settings.general_status.in_active');
            setGeneralSetting('member_email_verification', $member_email_verification);

            DB::commit();
            toastNotification('success', 'Settings updated successfully', 'Success');
            return to_route('classified.settings.member');
        } catch (\Exception $ex) {
            DB::rollBack();
            toastNotification('error', 'Settings update failed', 'Error');
            return to_route('classified.settings.member');
        }
    }

    /**
     * Will return Ads Setting
     */
    public function adsSettings(): View
    {
        return view('backend.modules.settings.ads_settings');
    }
    /**
     * Will redirect map setting
     */
    public function mapSettings(): View
    {
        return view('backend.modules.settings.map');
    }
    /**
     * Will return safety tips settings page
     */
    public function safetyTips(): View
    {
        $tips = SafetyTips::with(['tip_translations'])
            ->orderBy('id', 'DESC')
            ->paginate(10)
            ->withQueryString();

        return view('backend.modules.safety-tips.index', ['tips' => $tips]);
    }
    /**
     * Will store new safety tips
     */
    public function storeSafetyTips(SafetyTipsRequest $request): JsonResponse
    {
        try {
            $tips = new SafetyTips();
            $tips->title = xss_clean($request['title']);
            $tips->status = xss_clean($request['status']);
            $tips->save();
            return response()->json(
                [
                    'success' => true,
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false
            ], 500);
        }
    }
    /**
     * Will delete safety tips
     */
    public function deleteSafetyTips(Request $request): RedirectResponse
    {
        try {
            $tips = SafetyTips::findOrFail($request['id']);
            $tips->delete();
            toastNotification('success', 'Safety tips deleted successfully', 'Success');
            return to_route('classified.settings.safety.tips.list');
        } catch (\Exception $e) {
            toastNotification('error', 'Safety tips delete failed');
            return redirect()->back();
        }
    }
    /**
     * Will redirect to edit safety tips
     */
    public function editSafetyTips($id, Request $request): View
    {
        $tips = SafetyTips::findOrFail($id);
        return view('backend.modules.safety-tips.edit', ['tips' => $tips]);
    }
    /**
     * Will update safety tips
     */
    public function updateSafetyTips(SafetyTipsRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != defaultLangCode()) {
                $tips_translation = SafetyTipTranslation::firstOrNew(['tips_id' => $request['id'], 'lang' => $request['lang']]);
                $tips_translation->title = $request['title'];
                $tips_translation->save();
            } else {
                $tips = SafetyTips::findOrFail($request['id']);
                $tips->title = $request['title'];
                $tips->status = $request['status'];
                $tips->save();
            }
            DB::commit();
            toastNotification('success', 'Tips updated successfully', 'Success');
            return to_route('classified.settings.safety.tips.edit', ['id' => $request['id'], 'lang' => $request['lang'] != null ? $request['lang'] : defaultLangCode()]);
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Tips update failed', 'Error');
            return redirect()->back();
        }
    }
    /**
     * Will return quick sell tips settings page
     */
    public function quickSellTips(): View
    {
        $tips = QuickSellTip::with(['tip_translations'])
            ->orderBy('id', 'DESC')
            ->paginate(10)
            ->withQueryString();

        return view('backend.modules.settings.quick-sell-tips', ['tips' => $tips]);
    }
    /**
     * Will store new  tips
     */
    public function storeQuickSellTips(SafetyTipsRequest $request): JsonResponse
    {
        try {
            $tips = new QuickSellTip();
            $tips->title = xss_clean($request['title']);
            $tips->status = xss_clean($request['status']);
            $tips->save();
            return response()->json(
                [
                    'success' => true,
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false
            ], 500);
        }
    }
    /**
     * Will delete  tips
     */
    public function deleteQuickSellTips(Request $request): RedirectResponse
    {
        try {
            $tips = QuickSellTip::findOrFail($request['id']);
            $tips->delete();
            toastNotification('success', 'Quick sell tips deleted successfully', 'Success');
            return to_route('classified.settings.quick.sell.tips.list');
        } catch (\Exception $e) {
            toastNotification('error', 'Quick sell tips delete failed');
            return redirect()->back();
        }
    }
    /**
     * Will redirect to edit tips
     */
    public function editQuickSellTips($id, Request $request): View
    {
        $tips = QuickSellTip::findOrFail($id);
        return view('backend.modules.settings.edit-quick-sell-tips', ['tips' => $tips]);
    }
    /**
     * Will update quick sell tips
     */
    public function updateQuickSellTips(SafetyTipsRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            if ($request['lang'] != null && $request['lang'] != defaultLangCode()) {
                $tips_translation = QuickSellTipTranslation::firstOrNew(['tips_id' => $request['id'], 'lang' => $request['lang']]);
                $tips_translation->title = $request['title'];
                $tips_translation->save();
            } else {
                $tips = QuickSellTip::findOrFail($request['id']);
                $tips->title = $request['title'];
                $tips->status = $request['status'];
                $tips->save();
            }
            DB::commit();
            toastNotification('success', 'Tips updated successfully', 'Success');
            return to_route('classified.settings.quick.sell.tips.edit', ['id' => $request['id'], 'lang' => $request['lang'] != null ? $request['lang'] : defaultLangCode()]);
        } catch (\Exception $e) {
            DB::rollBack();
            toastNotification('error', 'Tips update failed', 'Error');
            return redirect()->back();
        }
    }
    /**
     * Will redirect share options page 
     */
    public function shareOptions(): View
    {
        $options = AdShareOption::all();
        return view('backend.modules.settings.share-options', ['options' => $options]);
    }
    /**
     * Update status of product share option
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function shareOptionUpdateStatus(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $option = AdShareOption::find($request['id']);
            $option->status = $option->status == config('settings.general_status.active') ? config('settings.general_status.in_active') : config('settings.general_status.active');
            $option->save();
            DB::commit();
            return response()->json([
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
            ], 500);
        }
    }
    /**
     * Will update classified settings
     */
    public function updateSetting(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            foreach ($request->except(['_token']) as $key => $value) {
                setGeneralSetting($key, xss_clean($value));
            }
            DB::commit();
            toastNotification('success', 'Settings updated successfully', 'Success');
            return redirect()->back();
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', 'Settings update failed', 'Error');
            return redirect()->back();
        }
    }
}
