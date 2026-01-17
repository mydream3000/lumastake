<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // Очищаем таблицу от старых данных
        Faq::query()->truncate();

        $faqs = [
            ['question' => 'What is Lumastake?', 'answer' => 'Lumastake is a staking platform that generates steady returns through arbitrage-based strategies, without the risks of futures or high-volatility trading.'],
            ['question' => 'How does Lumastake work?', 'answer' => 'You stake your USDT for a fixed duration in one of our tiers, and we use tested arbitrage methods to generate returns. Earnings are credited according to your plan.'],
            ['question' => 'Is Lumastake a trading platform?', 'answer' => 'No. Lumastake is a staking platform that operates on arbitrage principles, not day trading or speculative futures trading.'],
            ['question' => 'What makes Lumastake different from other crypto platforms?', 'answer' => 'We focus on low-risk, market-neutral strategies and do not engage in leveraged or futures trading. Our model is transparent, simple, and designed for steady growth.'],
            ['question' => 'Which cryptocurrency does Lumastake support?', 'answer' => 'Currently, we only support USDT (TRC20) for deposits and withdrawals.'],
            ['question' => 'Is Lumastake available worldwide?', 'answer' => 'Yes, Lumastake is available globally, except in jurisdictions where crypto activities are restricted by law.'],
            ['question' => 'How does Lumastake generate returns?', 'answer' => 'We identify price gaps across exchanges and execute arbitrage trades, capturing small, consistent profits over time.'],
            ['question' => 'Is Lumastake based on futures trading?', 'answer' => 'No. Lumastake does not participate in futures or margin trading.'],
            ['question' => 'How does Lumastake use arbitrage strategies?', 'answer' => 'Our system continuously monitors multiple exchanges for price differences and executes trades within seconds to capture those opportunities.'],
            ['question' => 'Who can use Lumastake?', 'answer' => 'Anyone above the legal age in their country of residence who owns USDT (TRC20) can use Lumastake.'],
            ['question' => 'How do I create an account?', 'answer' => 'Visit our website or app, click Sign Up, fill in your details, and verify your email.'],
            ['question' => 'Is there a minimum age to register?', 'answer' => 'Yes, you must be at least 18 years old.'],
            ['question' => 'Do I need KYC verification?', 'answer' => 'Yes, KYC is required for Tier 2 and higher. Tier 1 does not require KYC.'],
            ['question' => 'Can I open multiple accounts?', 'answer' => 'No, each person is allowed only one account.'],
            ['question' => 'How do I log in to my account?', 'answer' => 'Go to the login page, enter your email and password, and complete the authentication process.'],
            ['question' => 'I forgot my password — what should I do?', 'answer' => 'Click Forgot Password on the login page and follow the reset instructions sent to your email.'],
            ['question' => 'How do I update my account details?', 'answer' => 'You can update personal details from your account dashboard settings.'],
            ['question' => 'Is my account information secure?', 'answer' => 'Yes, we use encryption and multi-layered security protocols to protect user data.'],
            ['question' => 'Can I change my registered email address?', 'answer' => 'Yes, by submitting a request to our support team for verification.'],
            ['question' => 'How do I delete my account?', 'answer' => 'You can request account deletion via customer support after clearing all balances.'],
            ['question' => 'What currencies can I deposit?', 'answer' => 'We currently accept only USDT via the TRON (TRC20) network.'],
            ['question' => 'What is the minimum deposit amount?', 'answer' => 'The minimum deposit is based on your chosen staking tier, starting from Tier 1.'],
            ['question' => 'Does Lumastake support multiple networks?', 'answer' => 'No, we only support TRON TRC20 to ensure low fees and fast transactions.'],
            ['question' => 'How do I deposit funds into my account?', 'answer' => 'Log in, go to Deposit, copy your TRC20 wallet address, and transfer USDT from your personal wallet.'],
            ['question' => 'How long does it take for a deposit to reflect?', 'answer' => 'Deposits typically reflect within minutes, depending on network speed.'],
            ['question' => 'Are there any deposit fees?', 'answer' => 'Lumastake does not charge deposit fees.'],
            ['question' => 'What happens if I send funds to the wrong address?', 'answer' => 'Unfortunately, crypto transactions are irreversible, so please double-check before sending.'],
            ['question' => 'Can I deposit from any crypto wallet?', 'answer' => 'Yes, as long as it supports USDT (TRC20).'],
            ['question' => 'Is my deposit safe?', 'answer' => 'Yes, all deposits are stored securely in cold wallets only.'],
            ['question' => 'What is staking in Lumastake?', 'answer' => 'It\'s the process of locking your USDT for a fixed period to earn returns through our arbitrage strategies.'],
            ['question' => 'How do I start staking?', 'answer' => 'After depositing USDT, select your tier and duration from the staking section and confirm your stake.'],
            ['question' => 'What staking tiers are available?', 'answer' => 'We offer 7 tiers, starting from Tier 1 (0–500 USDT) up to Tier 7 (50,000+ USDT).'],
            ['question' => 'What are the staking durations?', 'answer' => 'You can choose from 10, 30, 60, 90, and 180-day plans.'],
            ['question' => 'Can I stake more than one plan at the same time?', 'answer' => 'Yes, you can stake multiple plans simultaneously.'],
            ['question' => 'How are returns calculated?', 'answer' => 'Returns are based on your tier and duration, with rates fixed at the time of staking.'],
            ['question' => 'When will I receive my earnings?', 'answer' => 'Earnings are credited at the end of the staking period.'],
            ['question' => 'Can I withdraw my stake before maturity?', 'answer' => 'No, early withdrawals are not available to maintain strategy stability.'],
            ['question' => 'What happens when my staking plan ends?', 'answer' => 'Your stake and earnings are released to your wallet balance, ready for withdrawal or reinvestment.'],
            ['question' => 'Is there a maximum staking limit?', 'answer' => 'No, but each tier has a defined deposit range.'],
            ['question' => 'How do I withdraw my funds?', 'answer' => 'Go to Withdraw, enter the amount, paste your TRC20 wallet address, and confirm.'],
            ['question' => 'What is the minimum withdrawal amount?', 'answer' => 'The minimum withdrawal amount is 50 USDT.'],
            ['question' => 'Are there withdrawal fees?', 'answer' => 'Yes, withdrawals have a fixed $2 fee.'],
            ['question' => 'How long do withdrawals take?', 'answer' => 'Withdrawals are typically processed within minutes, subject to network confirmation.'],
            ['question' => 'Which network is used for withdrawals?', 'answer' => 'All withdrawals are processed through the TRON (TRC20) network.'],
            ['question' => 'Can I withdraw to any wallet address?', 'answer' => 'Yes, as long as it\'s a valid TRC20 USDT address.'],
            ['question' => 'Are withdrawals processed 24/7?', 'answer' => 'Yes, withdrawal requests are processed around the clock.'],
            ['question' => 'What should I do if my withdrawal is delayed?', 'answer' => 'Contact our support team with your transaction ID for assistance.'],
            ['question' => 'Is there a daily withdrawal limit?', 'answer' => 'No fixed daily limit, but very large withdrawals may require additional verification.'],
            ['question' => 'Can I cancel a withdrawal request?', 'answer' => 'Yes, withdrawal requests can be cancelled before processing.'],
            ['question' => 'Is Lumastake regulated?', 'answer' => 'We operate in compliance with applicable laws in jurisdictions where we offer services.'],
            ['question' => 'How does Lumastake protect my funds?', 'answer' => 'Funds are secured using multi-layer wallet systems, cold storage, and advanced cybersecurity measures.'],
            ['question' => 'Are my transactions encrypted?', 'answer' => 'Yes, all transactions and data transfers are encrypted using industry-standard protocols.'],
            ['question' => 'How do I enable two-factor authentication (2FA)?', 'answer' => '2FA is enabled through email code verification.'],
            ['question' => 'What should I do if I suspect suspicious activity?', 'answer' => 'Immediately change your password, enable 2FA, and contact support.'],
            ['question' => 'Has Lumastake ever been hacked?', 'answer' => 'No, Lumastake has maintained a strong security record with no breaches.'],
            ['question' => 'How does Lumastake ensure transparency?', 'answer' => 'We provide clear staking terms, fixed rates, and accurate reporting in your dashboard.'],
            ['question' => 'How do I contact Lumastake support?', 'answer' => 'You can reach our support team through the Help Center, live chat, or email support@lumastake.com.'],
        ];

        foreach ($faqs as $index => $faq) {
            Faq::create([
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'is_active' => true,
                'order' => $index + 1,
            ]);
        }
    }
}

