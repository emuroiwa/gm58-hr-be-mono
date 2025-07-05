<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LegalController extends Controller
{
    /**
     * Show terms of service
     */
    public function terms(): View
    {
        return view('legal.terms');
    }

    /**
     * Show privacy policy
     */
    public function privacy(): View
    {
        return view('legal.privacy');
    }

    /**
     * Show security information
     */
    public function security(): View
    {
        return view('legal.security');
    }
}
