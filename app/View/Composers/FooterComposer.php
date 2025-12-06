<?php

namespace App\View\Composers;

use App\Models\SocialLink;
use Illuminate\View\View;

class FooterComposer
{
    public function compose(View $view): void
    {
        $socialLinks = SocialLink::where('is_active', true)
            ->whereNotNull('url')
            ->orderByRaw("FIELD(platform, 'Instagram', 'Facebook', 'Twitter', 'TikTok')")
            ->get();

        $view->with('socialLinks', $socialLinks);
    }
}
