<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Api\Terms;

class GeneralController extends Controller
{
    public function privacy($lang = 'en') {
        return Terms::whereType('privacy')->first()->{'description_'.$lang};
    }

    public function handleOTP(Request $request)
    {
        $page_title = 'Delete Account';

        if ($request->input('action') === 'send_otp') {
            return redirect()->back()->with('otp_sent', true)->withInput();
        }
        elseif ($request->input('action') === 'verify_otp' && $request->method() === 'DELETE') {
            return redirect()->back()->with('account_deleted', true);
        }

        return view('web.pages.delete-account', compact('page_title'));
    }

    public function handle(Request $request)
    {
        $page_title = 'Delete Account';

        if ($request->input('action') === 'send_otp') {
            return redirect()->back()->with('account_deleted', true);
        }

        return view('web.pages.delete-account', compact('page_title'));
    }

    public function support()
    {
        $page_title = 'Support';

        return view('web.pages.support', compact('page_title'));
    }

    public function submitSupport(Request $request)
    {
        $page_title = 'Support';

        return redirect()->route('support')->with('success', 'Thank you for your message. Our team will check it and get back to you.');
    }
}
