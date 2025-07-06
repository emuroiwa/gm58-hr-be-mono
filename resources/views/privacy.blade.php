@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="bg-white py-16 sm:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">Privacy Policy</h1>
            <p class="mt-4 text-lg text-gray-600">Last updated: {{ date('F j, Y') }}</p>
        </div>

        <div class="prose prose-lg mx-auto">
            <h2>Information We Collect</h2>
            <p>We collect information you provide directly to us, such as when you create an account, use our services, or contact us for support.</p>
            
            <h3>Personal Information</h3>
            <ul>
                <li>Name, email address, and contact information</li>
                <li>Employment information and HR data</li>
                <li>Payment and billing information</li>
                <li>Communications with our support team</li>
            </ul>

            <h3>Automatically Collected Information</h3>
            <ul>
                <li>Device and browser information</li>
                <li>IP address and location data</li>
                <li>Usage patterns and analytics</li>
                <li>Cookies and similar tracking technologies</li>
            </ul>

            <h2>How We Use Your Information</h2>
            <p>We use the information we collect to:</p>
            <ul>
                <li>Provide, maintain, and improve our services</li>
                <li>Process transactions and send related information</li>
                <li>Send technical notices and support messages</li>
                <li>Respond to your comments and questions</li>
                <li>Comply with legal obligations</li>
            </ul>

            <h2>Information Sharing</h2>
            <p>We do not sell, trade, or otherwise transfer your personal information to third parties except in the following circumstances:</p>
            <ul>
                <li>With your explicit consent</li>
                <li>To trusted service providers who assist in operations</li>
                <li>When required by law or to protect our rights</li>
                <li>In connection with a business transaction</li>
            </ul>

            <h2>Data Security</h2>
            <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

            <h2>Your Rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li>Access and update your personal information</li>
                <li>Request deletion of your data</li>
                <li>Opt-out of certain communications</li>
                <li>Portability of your data</li>
            </ul>

            <h2>Cookies</h2>
            <p>We use cookies and similar tracking technologies to collect and use personal information about you. You can control cookies through your browser settings.</p>

            <h2>Changes to This Policy</h2>
            <p>We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last updated" date.</p>

            <h2>Contact Us</h2>
            <p>If you have any questions about this privacy policy, please contact us at:</p>
            <ul>
                <li>Email: privacy@{{ config('app.name', 'hrmanagement') }}.com</li>
                <li>Address: [Your Company Address]</li>
            </ul>
        </div>
    </div>
</div>
@endsection
