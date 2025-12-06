<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Seeder;

class PageSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'page_slug' => 'home',
                'page_name' => 'Home',
                'seo_title' => 'Arbitex — Secure Crypto Investments | Earn Passively, Trade Confidently',
                'meta_description' => 'Welcome to Arbitex — a reliable crypto investment platform where your USDT grows securely through staking. No trading stress, no hidden risks — just smart passive income with full transparency.',
                'meta_keywords' => 'Arbitex, crypto investments, passive income, secure staking, invest in crypto, USDT staking, reliable crypto platform, DeFi profits',
            ],
            [
                'page_slug' => 'about',
                'page_name' => 'About Us',
                'seo_title' => 'About Arbitex — Trusted Crypto Platform for Smart Investors',
                'meta_description' => 'Learn more about Arbitex and our mission to make crypto investing simple and secure. We combine technology, transparency, and experience to deliver real results and peace of mind.',
                'meta_keywords' => 'about Arbitex, Arbitex team, secure crypto platform, blockchain investments, trusted DeFi, transparent crypto projects, smart investing',
            ],
            [
                'page_slug' => 'tiers',
                'page_name' => 'Profit Tiers',
                'seo_title' => 'Arbitex Profit Tiers — Investment Plans & Earnings Levels',
                'meta_description' => 'Choose your profit tier with Arbitex. Flexible staking plans offering up to 15% monthly returns. Start earning with crypto safely, transparently, and automatically.',
                'meta_keywords' => 'Arbitex profit tiers, crypto income plans, staking returns, crypto investment packages, USDT staking, monthly profit, passive crypto earnings',
            ],
            [
                'page_slug' => 'blog',
                'page_name' => 'Blog',
                'seo_title' => 'Arbitex Blog — Crypto Insights, Strategies & Financial Freedom',
                'meta_description' => 'Explore the Arbitex blog for expert insights on crypto investing, DeFi trends, and passive income strategies. Learn how to grow your capital safely and confidently.',
                'meta_keywords' => 'Arbitex blog, crypto news, crypto investment tips, DeFi strategies, passive income, blockchain insights, cryptocurrency education',
            ],
            [
                'page_slug' => 'contact',
                'page_name' => 'Contact Us',
                'seo_title' => 'Contact Arbitex — Support, Partnership & Investment Assistance',
                'meta_description' => 'Need help or want to partner with us? The Arbitex team is here for you 24/7. Get in touch and start your journey toward safe, profitable crypto investing today.',
                'meta_keywords' => 'contact Arbitex, Arbitex support, partnership, crypto investing help, get in touch, customer service, crypto business cooperation',
            ],
            [
                'page_slug' => 'faq',
                'page_name' => 'FAQ',
                'seo_title' => 'Arbitex FAQ — Answers to Common Crypto Investment Questions',
                'meta_description' => 'Find quick answers about Arbitex, staking, profit tiers, and investment safety. Everything you need to know before starting your crypto journey with full confidence.',
                'meta_keywords' => 'Arbitex FAQ, crypto questions, staking explained, safe crypto investments, how to invest in crypto, DeFi income, passive crypto returns',
            ],
            [
                'page_slug' => 'login',
                'page_name' => 'Login',
                'seo_title' => 'Login to Arbitex — Access Your Crypto Investment Dashboard',
                'meta_description' => 'Sign in to your Arbitex account and manage your crypto investments easily. Track your profits, view staking progress, and stay in control of your digital assets — securely and instantly.',
                'meta_keywords' => 'Arbitex login, crypto dashboard, account access, crypto investment login, staking account, secure login, user dashboard, passive income tracking',
            ],
            [
                'page_slug' => 'register',
                'page_name' => 'Register',
                'seo_title' => 'Join Arbitex — Start Earning with Secure Crypto Investments',
                'meta_description' => 'Create your free Arbitex account today and begin earning passive income with crypto. Safe, transparent, and user-friendly — your gateway to smart digital wealth.',
                'meta_keywords' => 'Arbitex register, create account, join Arbitex, crypto registration, start staking, invest in crypto, passive income platform, secure crypto earnings',
            ],
        ];

        foreach ($pages as $page) {
            PageSeo::updateOrCreate(
                ['page_slug' => $page['page_slug']],
                $page
            );
        }
    }
}
