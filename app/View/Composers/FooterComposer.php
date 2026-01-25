<?php

namespace App\View\Composers;

use App\Models\SocialLink;
use Illuminate\View\View;

class FooterComposer
{
    public function compose(View $view): void
    {
        // Footer links (for footer + blog sharing)
        $footerLinks = SocialLink::footer()
            ->active()
            ->orderByRaw("FIELD(platform, 'Instagram', 'Facebook', 'Twitter', 'TikTok', 'Telegram', 'YouTube')")
            ->get();

        // Cabinet links (for contact form)
        $cabinetLinks = SocialLink::cabinet()
            ->active()
            ->orderByRaw("FIELD(platform, 'Instagram', 'Facebook', 'Twitter', 'TikTok', 'Telegram', 'YouTube')")
            ->get();

        // Keep backward compatibility with old $socialLinks variable
        $view->with('socialLinks', $footerLinks);
        $view->with('footerLinks', $footerLinks);
        $view->with('cabinetLinks', $cabinetLinks);
    }
}
