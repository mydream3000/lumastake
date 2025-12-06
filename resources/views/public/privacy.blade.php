@extends('layouts.public')

@section('title', 'Privacy Policy - '.config('app.name'))

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10 text-white">
    <h1 class="text-3xl md:text-4xl font-bold mb-6">Privacy Policy</h1>

    <p class="text-gray-300 mb-6">Effective date: <strong>{{ date('Y-m-d') }}</strong></p>

    <div class="space-y-6 text-gray-200 leading-relaxed">
        <p>
            This Privacy Policy explains how {{ config('app.name') }} ("we", "us", "our") collects, uses, discloses and safeguards
            personal data when you use our website and services. We process personal data in accordance with applicable data protection
            laws, including the EU General Data Protection Regulation (GDPR) and the California Consumer Privacy Act (CCPA), where applicable.
        </p>

        <h2 class="text-2xl font-semibold">1. Data Controller</h2>
        <p>
            The data controller is the website operator. To finalize this section, please provide: company legal name, registered address,
            registration number, and contact email. Until provided, you can reach us at <strong>support@lumastake.com</strong> (placeholder).
        </p>

        <h2 class="text-2xl font-semibold">2. Categories of Personal Data We Collect</h2>
        <ul class="list-disc ml-6 space-y-2">
            <li>Identification data: name, email address, account ID.</li>
            <li>Contact data: country code, phone number, country.</li>
            <li>Account data: password (hashed), referral code, referred by, tier, balances and transactions related to platform features.</li>
            <li>Technical data: IP address, device and browser information, cookies and similar technologies.</li>
            <li>Communications: messages, support requests, and email preferences.</li>
        </ul>

        <h2 class="text-2xl font-semibold">3. Purposes and Legal Bases (GDPR)</h2>
        <ul class="list-disc ml-6 space-y-2">
            <li>Account registration and authentication — performance of a contract (Art. 6(1)(b) GDPR).</li>
            <li>Provision of platform services, payments, and security — performance of a contract and legitimate interests (Art. 6(1)(b),(f)).</li>
            <li>Fraud prevention, abuse detection, and platform integrity — legitimate interests (Art. 6(1)(f)).</li>
            <li>Legal compliance (e.g., financial record‑keeping, requests from authorities) — legal obligation (Art. 6(1)(c)).</li>
            <li>Marketing communications (where permitted) — consent (Art. 6(1)(a)) or legitimate interests (Art. 6(1)(f)).</li>
        </ul>

        <h2 class="text-2xl font-semibold">4. How We Share Personal Data</h2>
        <p>
            We may share personal data with service providers (e.g., hosting, email, analytics, payment/infrastructure vendors) under data
            processing agreements. We may disclose data when required by law, to protect rights and safety, or in connection with corporate
            transactions. We do not sell personal data.
        </p>

        <h2 class="text-2xl font-semibold">5. International Transfers</h2>
        <p>
            If personal data is transferred outside your jurisdiction, we implement appropriate safeguards (e.g., Standard Contractual
            Clauses under GDPR). Details of transfer mechanisms are available on request.
        </p>

        <h2 class="text-2xl font-semibold">6. Data Retention</h2>
        <p>
            We retain personal data for as long as necessary to fulfill the purposes outlined above, to comply with legal obligations,
            resolve disputes, and enforce agreements. Retention periods depend on the type of data and legal requirements.
        </p>

        <h2 class="text-2xl font-semibold">7. Your Rights</h2>
        <p>Subject to applicable law, you may have the following rights:</p>
        <ul class="list-disc ml-6 space-y-2">
            <li>Access: obtain a copy of your personal data.</li>
            <li>Rectification: correct inaccurate or incomplete data.</li>
            <li>Erasure: request deletion of your data in certain circumstances.</li>
            <li>Restriction: ask us to limit processing in certain cases.</li>
            <li>Portability: receive data in a structured, machine‑readable format.</li>
            <li>Objection: object to processing based on legitimate interests or direct marketing.</li>
            <li>Withdraw consent: where processing is based on consent, you may withdraw it at any time.</li>
            <li>CCPA: right to know, delete, and opt‑out of sale/share (we do not sell personal data); non‑discrimination for exercising rights.</li>
        </ul>
        <p>To exercise your rights, contact us at <strong>support@lumastake.com</strong> (placeholder) or via the account area.</p>

        <h2 class="text-2xl font-semibold">8. Cookies and Similar Technologies</h2>
        <p>
            We use cookies and similar technologies to provide, secure and improve the services (e.g., session management, analytics).
            You can control cookies via your browser settings. A separate Cookie Policy may be provided for detailed information.
        </p>

        <h2 class="text-2xl font-semibold">9. Security</h2>
        <p>
            We implement appropriate technical and organizational measures designed to protect personal data against unauthorized access,
            accidental loss, destruction or damage. However, no system can be guaranteed to be 100% secure.
        </p>

        <h2 class="text-2xl font-semibold">10. Children’s Privacy</h2>
        <p>
            Our services are not directed to children under the age of 18 (or age of majority in your jurisdiction). We do not knowingly
            collect personal data from children.
        </p>

        <h2 class="text-2xl font-semibold">11. Changes to this Policy</h2>
        <p>
            We may update this Privacy Policy from time to time. Material changes will be communicated via the interface and/or email,
            with the updated effective date shown at the top of this page.
        </p>

        <h2 class="text-2xl font-semibold">12. Contact</h2>
        <p>
            To complete this section, please provide your company’s legal address and a dedicated privacy/DPO contact email. Until then,
            you may contact us at <strong>support@lumastake.com</strong> (placeholder).
        </p>
    </div>
</div>
@endsection
