<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    /**
     * Will redirect environment settings page
     */
    public function environmentSettings(): View
    {
        return view('backend.modules.system.environment');
    }
    /**
     * Will update environment settings
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function environmentSettingsUpdate(Request $request)
    {
        try {
            foreach ($request->except('_token') as $key => $value) {
                setEnv($key, $value);
            }
            toastNotification('Success', 'Environment value updated successfully', 'Success');
            return to_route('admin.system.settings.environment');
        } catch (\Exception $e) {
            toastNotification('error', 'Update failed', 'Error');
            return redirect()->back();
        } catch (\Error $e) {
            toastNotification('error', 'Update failed', 'Error');
            return redirect()->back();
        }
    }

    /**
     * Will redirect smtp settings page
     */
    public function smtpSettings(): View
    {
        return view('backend.modules.system.smtp');
    }

    /**
     * Will update smtp settings
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function smtpSettingsUpdate(Request $request): RedirectResponse
    {
        try {
            foreach ($request->except('_token') as $key => $value) {
                setEnv($key, $value);
            }
            toastNotification('Success', 'SMTP value updated successfully', 'Success');
            return to_route('admin.system.settings.smtp');
        } catch (\Exception $e) {
            toastNotification('error', 'Update failed', 'Error');
            return redirect()->back();
        } catch (\Error $e) {
            toastNotification('error', 'Update failed', 'Error');
            return redirect()->back();
        }
    }
    /**
     * Will send test mail
     */
    public function testMail(Request $request): RedirectResponse
    {

        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|max:150',
            'message' => 'required'
        ]);
        try {
            $to = $request['email'];
            $subject = $request['subject'];
            $message = $request['message'];

            Mail::raw($message, function ($message) use ($to, $subject) {
                $message->to($to)
                    ->subject($subject);
            });

            toastNotification('Success', 'Mail Sending Successfully', 'Success');
            return to_route('admin.system.settings.smtp');
        } catch (\Throwable $e) {
            Log::error('Test mail failed: ' . $e->getMessage(), ['exception' => $e]);
            toastNotification('error', 'Mail Sending Failed: ' . $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }

    public function socialLogin(): View
    {
        return view('backend.modules.system.social_login');
    }

    public function socialLoginUpdate(Request $request): RedirectResponse
    {
        try {
            foreach ($request->except('_token', 'facebook_login', 'google_login') as $key => $value) {
                setEnv($key, $value);
            }
            if ($request->type == 'google') {
                isset($request['google_login']) ? set_setting('google_login', config('settings.general_status.active')) : set_setting('google_login', config('settings.general_status.inactive'));
                toastNotification('success', 'Google Login Settings update successfully', 'Success');
            }
            if ($request->type == 'facebook') {
                isset($request['facebook_login']) ? set_setting('facebook_login', config('settings.general_status.active')) : set_setting('facebook_login', config('settings.general_status.inactive'));
                toastNotification('success', 'Facebook Login Settings update successfully', 'Success');
            }
            return to_route('admin.system.settings.social.login');
        } catch (\Exception $e) {
            toastNotification('error', 'Update failed', 'Error');
            return redirect()->back();
        } catch (\Error $e) {
            toastNotification('error', 'Update failed', 'Error');
            return redirect()->back();
        }
    }

    public function iptvSettings(): View
    {
        return view('backend.modules.settings.iptv-settings');
    }

    public function iptvSettingsUpdate(Request $request): RedirectResponse
    {
        $settings = [
            'xtream_base_url'           => $request->input('xtream_base_url', ''),
            'xtream_admin_username'     => $request->input('xtream_admin_username', ''),
            'xtream_admin_password'     => $request->input('xtream_admin_password', ''),
            'whmcs_api_url'             => $request->input('whmcs_api_url', ''),
            'whmcs_api_identifier'      => $request->input('whmcs_api_identifier', ''),
            'whmcs_api_secret'          => $request->input('whmcs_api_secret', ''),
            'whmcs_product_id'          => $request->input('whmcs_product_id', 0),
            'whmcs_webhook_secret'      => $request->input('whmcs_webhook_secret', ''),
            'iptv_provisioning_enabled' => $request->input('iptv_provisioning_enabled', 0),
            'whmcs_sync_enabled'        => $request->input('whmcs_sync_enabled', 0),
        ];

        foreach ($settings as $key => $value) {
            set_setting($key, $value);
        }

        toastNotification('success', __tr('IPTV settings updated successfully'));
        return back();
    }

    public function chatWidget()
    {
        return view('backend.modules.settings.chat-widget');
    }

    public function updateChatWidget(Request $request)
    {
        set_setting('chat_widget_enabled', $request->boolean('chat_widget_enabled') ? 1 : 0);
        set_setting('chat_widget_code', $request->input('chat_widget_code', ''));

        toastNotification('success', __tr('Chat widget settings updated.'));
        return back();
    }
}
