<?php

namespace App\View\Composers;

use App\Models\PageSeo;
use Illuminate\View\View;

class SeoComposer
{
    /**
     * Mapping роутов к slug'ам страниц
     */
    protected array $routeToSlugMap = [
        'home' => 'home',
        'about' => 'about',
        'profit-tiers' => 'tiers',
        'blog' => 'blog',
        'contact' => 'contact',
        'faq' => 'faq',
        'login' => 'login',
        'register' => 'register',
    ];

    /**
     * Bind SEO data to the view
     */
    public function compose(View $view): void
    {
        // Если контроллер уже передал $seo в представление (например, страница статьи блога),
        // НЕ перезаписываем его данными из PageSeo. Это сохраняет приоритет SEO конкретной страницы.
        $existingData = $view->getData();
        if (array_key_exists('seo', $existingData) && !empty($existingData['seo'])) {
            return;
        }

        $currentRoute = request()->route()?->getName();

        // Определяем slug страницы на основе роута
        $pageSlug = $this->routeToSlugMap[$currentRoute] ?? null;

        // Если это детальная страница блога, используем slug 'blog'
        if ($currentRoute === 'blog.show') {
            $pageSlug = 'blog';
        }

        // Получаем SEO данные для страницы
        $seo = null;
        if ($pageSlug) {
            $seo = PageSeo::getForPage($pageSlug);
        }

        // Fallback на дефолтные значения если SEO данные не найдены
        if (!$seo) {
            $seo = (object) [
                'seo_title' => 'Arbitex — Secure Crypto Investments | Earn Passively, Trade Confidently',
                'meta_description' => 'Welcome to Arbitex — a reliable crypto investment platform where your USDT grows securely through staking.',
                'meta_keywords' => 'Arbitex, crypto investments, passive income, secure staking, invest in crypto',
            ];
        }

        $view->with('seo', $seo);
    }
}
