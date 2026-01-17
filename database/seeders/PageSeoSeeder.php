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
                'seo_title' => 'Lumastake — Secure Crypto Investments | Earn Passively, Trade Confidently',
                'meta_description' => 'Welcome to Lumastake — a reliable crypto investment platform where your USDT grows securely through staking. No trading stress, no hidden risks — just smart passive income with full transparency.',
                'meta_keywords' => 'Lumastake, crypto investments, passive income, secure staking, invest in crypto, USDT staking, reliable crypto platform, DeFi profits',
            ],
            [
                'page_slug' => 'about',
                'page_name' => 'About Us',
                'seo_title' => 'About Lumastake — Trusted Crypto Platform for Smart Investors',
                'meta_description' => 'Learn more about Lumastake and our mission to make crypto investing simple and secure. We combine technology, transparency, and experience to deliver real results and peace of mind.',
                'meta_keywords' => 'about Lumastake, Lumastake team, secure crypto platform, blockchain investments, trusted DeFi, transparent crypto projects, smart investing',
            ],
            [
                'page_slug' => 'tiers',
                'page_name' => 'Profit Tiers',
                'seo_title' => 'Lumastake Profit Tiers — Investment Plans & Earnings Levels',
                'meta_description' => 'Choose your profit tier with Lumastake. Flexible staking plans offering up to 15% monthly returns. Start earning with crypto safely, transparently, and automatically.',
                'meta_keywords' => 'Lumastake profit tiers, crypto income plans, staking returns, crypto investment packages, USDT staking, monthly profit, passive crypto earnings',
            ],
            [
                'page_slug' => 'blog',
                'page_name' => 'Blog',
                'seo_title' => 'Lumastake Blog — Crypto Insights, Strategies & Financial Freedom',
                'meta_description' => 'Explore the Lumastake blog for expert insights on crypto investing, DeFi trends, and passive income strategies. Learn how to grow your capital safely and confidently.',
                'meta_keywords' => 'Lumastake blog, crypto news, crypto investment tips, DeFi strategies, passive income, blockchain insights, cryptocurrency education',
            ],
            [
                'page_slug' => 'contact',
                'page_name' => 'Contact Us',
                'seo_title' => 'Contact Lumastake — Support, Partnership & Investment Assistance',
                'meta_description' => 'Need help or want to partner with us? The Lumastake team is here for you 24/7. Get in touch and start your journey toward safe, profitable crypto investing today.',
                'meta_keywords' => 'contact Lumastake, Lumastake support, partnership, crypto investing help, get in touch, customer service, crypto business cooperation',
            ],
            [
                'page_slug' => 'faq',
                'page_name' => 'FAQ',
                'seo_title' => 'Lumastake FAQ — Answers to Common Crypto Investment Questions',
                'meta_description' => 'Find quick answers about Lumastake, staking, profit tiers, and investment safety. Everything you need to know before starting your crypto journey with full confidence.',
                'meta_keywords' => 'Lumastake FAQ, crypto questions, staking explained, safe crypto investments, how to invest in crypto, DeFi income, passive crypto returns',
            ],
            [
                'page_slug' => 'login',
                'page_name' => 'Login',
                'seo_title' => 'Login to Lumastake — Access Your Crypto Investment Dashboard',
                'meta_description' => 'Sign in to your Lumastake account and manage your crypto investments easily. Track your profits, view staking progress, and stay in control of your digital assets — securely and instantly.',
                'meta_keywords' => 'Lumastake login, crypto dashboard, account access, crypto investment login, staking account, secure login, user dashboard, passive income tracking',
            ],
            [
                'page_slug' => 'register',
                'page_name' => 'Register',
                'seo_title' => 'Join Lumastake — Start Earning with Secure Crypto Investments',
                'meta_description' => 'Create your free Lumastake account today and begin earning passive income with crypto. Safe, transparent, and user-friendly — your gateway to smart digital wealth.',
                'meta_keywords' => 'Lumastake register, create account, join Lumastake, crypto registration, start staking, invest in crypto, passive income platform, secure crypto earnings',
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
