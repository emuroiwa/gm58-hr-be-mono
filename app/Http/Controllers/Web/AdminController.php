<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index(): View
    {
        return view('admin.index');
    }

    /**
     * Show admin settings
     */
    public function settings(): View
    {
        return view('admin.settings');
    }

    /**
     * Show user management
     */
    public function users(): View
    {
        return view('admin.users');
    }

    /**
     * Show reports section
     */
    public function reports(): View
    {
        return view('admin.reports');
    }
}
