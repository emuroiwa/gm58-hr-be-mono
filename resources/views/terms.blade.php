@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="bg-white py-16 sm:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">Terms of Service</h1>
            <p class="mt-4 text-lg text-gray-600">Last updated: {{ date('F j, Y') }}</p>
        </div>

        <div class="prose prose-lg mx-auto">
            <h2>Acceptance of Terms</h2>
            <p>By accessing and using this service, you accept and agree to be bound by the terms and provision of this agreement.</p>

            <h2>Description of Service</h2>
            <p>Our HR Management System provides comprehensive human resources management tools including employee management, payroll processing, time tracking, and reporting capabilities.</p>

            <h2>User Accounts</h2>
            <ul>
                <li>You are responsible for maintaining the confidentiality of your account</li>
                <li>You must provide accurate and complete registration information</li>
                <li>You are responsible for all activities under your account</li>
                <li>You must notify us immediately of any unauthorized use</li>
            </ul>

            <h2>Acceptable Use</h2>
            <p>You agree not to:</p>
            <ul>
                <li>Use the service for any unlawful purpose</li>
                <li>Attempt to gain unauthorized access to any part of the service</li>
                <li>Interfere with or disrupt the service or servers</li>
                <li>Upload malicious code or conduct security attacks</li>
                <li>Violate any applicable laws or regulations</li>
            </ul>

            <h2>Subscription and Payment</h2>
            <ul>
                <li>Subscription fees are billed in advance on a recurring basis</li>
                <li>All fees are non-refundable except as required by law</li>
                <li>We reserve the right to change pricing with notice</li>
                <li>Failure to pay may result in service suspension</li>
            </ul>

            <h2>Data and Privacy</h2>
            <ul>
                <li>You retain ownership of your data</li>
                <li>We will protect your data according to our Privacy Policy</li>
                <li>You are responsible for backing up your data</li>
                <li>We may anonymize and aggregate data for analytics</li>
            </ul>

            <h2>Service Availability</h2>
            <p>While we strive for high availability, we do not guarantee uninterrupted access to the service. We may perform maintenance and updates that temporarily affect service availability.</p>

            <h2>Intellectual Property</h2>
            <ul>
                <li>The service and its original content remain our property</li>
                <li>You may not copy, modify, or distribute our content without permission</li>
                <li>You grant us a license to use your content as necessary to provide the service</li>
            </ul>

            <h2>Limitation of Liability</h2>
            <p>To the fullest extent permitted by law, we shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of the service.</p>

            <h2>Termination</h2>
            <ul>
                <li>Either party may terminate this agreement at any time</li>
                <li>We may suspend or terminate accounts for violations of these terms</li>
                <li>Upon termination, your access to the service will cease</li>
                <li>Data export options may be available for a limited time after termination</li>
            </ul>

            <h2>Changes to Terms</h2>
            <p>We reserve the right to modify these terms at any time. We will provide notice of significant changes and your continued use constitutes acceptance of the new terms.</p>

            <h2>Governing Law</h2>
            <p>These terms shall be governed by and construed in accordance with the laws of [Your Jurisdiction], without regard to its conflict of law provisions.</p>

            <h2>Contact Information</h2>
            <p>If you have any questions about these Terms of Service, please contact us at:</p>
            <ul>
                <li>Email: legal@{{ config('app.name', 'hrmanagement') }}.com</li>
                <li>Address: [Your Company Address]</li>
            </ul>
        </div>
    </div>
</div>
@endsection
