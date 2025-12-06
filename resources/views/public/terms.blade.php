@extends('layouts.public')

@section('title', 'Terms of Service - '.config('app.name'))

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10 text-white">
    <h1 class="text-3xl md:text-4xl font-bold mb-6">Terms of Service</h1>

    <p class="text-gray-300 mb-6">Effective date: <strong>{{ date('Y-m-d') }}</strong></p>

    <div class="space-y-6 text-gray-200 leading-relaxed">
        <p>
            These Terms of Service ("Terms") govern your access to and use of the website, products and services
            provided by {{ config('app.name') }} ("we", "us", "our"). By creating an account, accessing or using our
            services, you agree to be bound by these Terms. If you do not agree, do not use our services.
        </p>

        <h2 class="text-2xl font-semibold">1. Eligibility and Accounts</h2>
        <ul class="list-disc ml-6 space-y-2">
            <li>You must be at least 18 years old (or the age of majority in your jurisdiction) to use the services.</li>
            <li>You agree to provide accurate, current and complete information during registration and to keep it updated.</li>
            <li>You are responsible for maintaining the confidentiality of your credentials and for all activities under your account.</li>
        </ul>

        <h2 class="text-2xl font-semibold">2. Use of Services</h2>
        <ul class="list-disc ml-6 space-y-2">
            <li>You agree to use the services only for lawful purposes and in compliance with these Terms and all applicable laws.</li>
            <li>Prohibited activities include, without limitation: fraud, money‑laundering, terrorist financing, sanctions evasion,
                abuse of referral programs, interference with the platform, automated scraping, reverse engineering, and any activity
                that harms other users or our infrastructure.</li>
            <li>We may update, suspend or discontinue any part of the services at any time with or without notice.</li>
        </ul>

        <h2 class="text-2xl font-semibold">3. Financial Risk Disclosure</h2>
        <p>
            Digital assets are volatile and may lose value. Past performance does not guarantee future results.
            You are solely responsible for evaluating the risks and for any decisions you make. We do not provide
            investment, legal or tax advice.
        </p>

        <h2 class="text-2xl font-semibold">4. Fees</h2>
        <p>
            Applicable fees (if any) are disclosed in the interface or relevant documentation and may change from time to time.
        </p>

        <h2 class="text-2xl font-semibold">5. Intellectual Property</h2>
        <p>
            All content, trademarks, logos and materials on the site are owned by {{ config('app.name') }} or its licensors
            and are protected by intellectual property laws. You may not use them without prior written permission.
        </p>

        <h2 class="text-2xl font-semibold">6. Third‑Party Services</h2>
        <p>
            The services may link to or integrate third‑party services. We are not responsible for third‑party content,
            terms or policies. Your use of such services is at your own risk.
        </p>

        <h2 class="text-2xl font-semibold">7. Disclaimer of Warranties</h2>
        <p>
            THE SERVICES ARE PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, EXPRESS OR IMPLIED,
            INCLUDING THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON‑INFRINGEMENT.
        </p>

        <h2 class="text-2xl font-semibold">8. Limitation of Liability</h2>
        <p>
            TO THE MAXIMUM EXTENT PERMITTED BY LAW, {{ config('app.name') }} AND ITS AFFILIATES SHALL NOT BE LIABLE FOR ANY INDIRECT,
            INCIDENTAL, SPECIAL, CONSEQUENTIAL OR PUNITIVE DAMAGES, OR ANY LOSS OF PROFITS OR REVENUES, WHETHER INCURRED DIRECTLY OR
            INDIRECTLY, OR ANY LOSS OF DATA, USE, GOOD‑WILL, OR OTHER INTANGIBLE LOSSES, RESULTING FROM YOUR ACCESS TO OR USE OF (OR
            INABILITY TO ACCESS OR USE) THE SERVICES.
        </p>

        <h2 class="text-2xl font-semibold">9. Indemnification</h2>
        <p>
            You agree to defend, indemnify, and hold harmless {{ config('app.name') }}, its affiliates, officers, employees and agents
            from and against any claims, liabilities, damages, losses and expenses, including reasonable attorney fees, arising out of
            or in any way connected with your use of the services or violation of these Terms or applicable laws.
        </p>

        <h2 class="text-2xl font-semibold">10. Termination</h2>
        <p>
            We may suspend or terminate your access to the services at any time, with or without notice, for any reason, including if
            we believe you have violated these Terms. Upon termination, your right to use the services ceases immediately.
        </p>

        <h2 class="text-2xl font-semibold">11. Governing Law and Dispute Resolution</h2>
        <p>
            These Terms are governed by the laws of your applicable jurisdiction (to be provided by the site owner).
            Any disputes will be resolved in the competent courts of that jurisdiction, unless mandatory consumer protection
            rules provide otherwise.
        </p>

        <h2 class="text-2xl font-semibold">12. Changes to Terms</h2>
        <p>
            We may modify these Terms at any time. Material changes will be notified by updating the date at the top of this page
            and/or via the interface or email. Continued use of the services after changes become effective constitutes acceptance.
        </p>

        <h2 class="text-2xl font-semibold">13. Contact</h2>
        <p>
            Legal entity name, registered address, and contact email are required to complete this section.
            Please provide:
        </p>
        <ul class="list-disc ml-6 space-y-1">
            <li>Company legal name and registration number</li>
            <li>Registered address (street, city, country)</li>
            <li>Primary contact email (support/legal)</li>
        </ul>

        <p class="mt-4">Until provided, you may contact us at: <strong>support@lumastake.com</strong> (placeholder).</p>
    </div>
</div>
@endsection
