<?php

namespace App\Http\Controllers\Admin;


use App\Models\Api\Appointment;
use App\Models\Api\Dashboard;
use App\Models\Api\Order;
use App\Models\Api\OrderLog;
use App\Models\Api\OrderRequestCard;
use App\Models\Api\PrivateHospital;
use App\Models\Api\Product;
use App\Models\Api\ProductType;
use App\Models\Api\PublicHospital;
use App\Models\Api\Role;
use App\Models\Api\Setting;
use App\Models\Api\User;
use App\Models\Api\UserPoints;
use App\Models\Api\Users;
use App\Models\Api\UserTransaction;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ResetRequest;

/**
 * @author Hassan Itani
 * @package App\Http\Controllers\Admin
 */
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
    public function index(Request $request) /*: View*/
    {
        $stats = Dashboard::whereIsVisible(1)->orderby('meta_order','asc')->get();

        
        return view('pages.dashboard.index', compact('stats'))
            ->with('section_name', 'dashboard')
            ->with('content_header', 'Dashboard');
    }

}
