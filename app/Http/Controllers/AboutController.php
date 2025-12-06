<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class AboutController extends BaseController
{
    public function index()
    {
        $seoKey = 'about';
        return view('public.about', compact('seoKey'));
    }
}
