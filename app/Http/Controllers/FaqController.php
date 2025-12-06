<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Models\Faq;

class FaqController extends BaseController
{
    public function index()
    {
        $faqs = Faq::active()->ordered()->get();
        $seoKey = 'faq';
        return view('public.faq.index', compact('faqs', 'seoKey'));
    }
}

