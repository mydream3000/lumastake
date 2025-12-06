<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;

class SocialLinksController extends Controller
{
    public function index()
    {
        $socialLinks = SocialLink::orderBy('id')->get();

        return view('admin.social-links.index', compact('socialLinks'));
    }

    public function update(Request $request, SocialLink $socialLink)
    {
        $request->validate([
            'url' => 'nullable|url',
        ]);

        $socialLink->update([
            'url' => $request->url,
        ]);

        return redirect()->route('admin.social-links.index')
            ->with('success', 'Социальная ссылка успешно обновлена');
    }

    public function toggleStatus(SocialLink $socialLink)
    {
        $socialLink->update([
            'is_active' => !$socialLink->is_active,
        ]);

        return redirect()->route('admin.social-links.index')
            ->with('success', 'Статус изменен');
    }
}
