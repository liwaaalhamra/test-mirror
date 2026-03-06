<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Api\Users;
use App\Models\Api\Barter;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 
     * @return View
     */
    public function index(Request $request)
    {
        $registered_users = Users::count();
        $verified_users = Users::where('is_verified', 1)->count();
        $total_barters = Barter::count();
        $pending_barters = Barter::where('status', 'pending')->count();
        $rejected_barters = Barter::where('status', 'rejected')->count();
        $accepted_barters = Barter::where('status', 'accepted')->count();
        $completed_barters = Barter::where('status', 'completed')->count();
        
        return view('pages.dashboard.index', compact('registered_users','verified_users','total_barters','pending_barters','rejected_barters','accepted_barters','completed_barters'))
            ->with('section_name', 'dashboard')
            ->with('content_header', 'Dashboard');
    }

}
