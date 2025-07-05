@extends('layouts.app')

@section('title', 'Pricing')

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="max-w-7xl mx-auto py-24 px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:flex-col sm:align-center">
            <h1 class="text-5xl font-extrabold text-gray-900 sm:text-center">Pricing Plans</h1>
            <p class="mt-5 text-xl text-gray-500 sm:text-center">
                Choose the perfect plan for your organization. Start free, upgrade as you grow.
            </p>
        </div>

        <!-- Pricing cards -->
        <div class="mt-12 space-y-4 sm:mt-16 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-6 lg:max-w-4xl lg:mx-auto xl:max-w-none xl:mx-0 xl:grid-cols-3">
            @foreach($plans as $plan)
            <div class="border border-gray-200 rounded-lg shadow-sm divide-y divide-gray-200 {{ $plan['highlighted'] ? 'border-blue-500 shadow-blue-100' : '' }}">
                <div class="p-6">
                    @if($plan['highlighted'])
                        <div class="inline-flex px-4 py-1 rounded-full text-sm font-semibold tracking-wide uppercase bg-blue-600 text-white">
                            Most Popular
                        </div>
                    @endif
                    <h2 class="text-lg leading-6 font-medium text-gray-900 {{ $plan['highlighted'] ? 'mt-2' : '' }}">{{ $plan['name'] }}</h2>
                    <p class="mt-4 text-sm text-gray-500">{{ $plan['description'] }}</p>
                    <p class="mt-8">
                        <span class="text-4xl font-extrabold text-gray-900">${{ $plan['price'] }}</span>
                        <span class="text-base font-medium text-gray-500">{{ $plan['period'] }}</span>
                    </p>
                    <a href="{{ route('auth.register') }}" class="mt-8 block w-full bg-{{ $plan['highlighted'] ? 'blue' : 'gray' }}-800 border border-transparent rounded-md py-2 text-sm font-semibold text-white text-center hover:bg-{{ $plan['highlighted'] ? 'blue' : 'gray' }}-900">
                        Get started
                    </a>
                </div>
                <div class="pt-6 pb-8 px-6">
                    <h3 class="text-xs font-medium text-gray-900 tracking-wide uppercase">What's included</h3>
                    <ul class="mt-6 space-y-4">
                        @foreach($plan['features'] as $feature)
                        <li class="flex space-x-3">
                            <svg class="flex-shrink-0 h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-gray-500">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- FAQ section -->
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-3xl font-extrabold text-gray-900 text-center">Frequently asked questions</h2>
            <div class="mt-12 divide-y divide-gray-200">
                <div class="py-8">
                    <h3 class="text-lg font-medium text-gray-900">Can I change plans later?</h3>
                    <p class="mt-3 text-gray-500">Yes, you can upgrade or downgrade your plan at any time. Changes will be reflected in your next billing cycle.</p>
                </div>
                <div class="py-8">
                    <h3 class="text-lg font-medium text-gray-900">Is there a free trial?</h3>
                    <p class="mt-3 text-gray-500">Yes, all plans come with a 14-day free trial. No credit card required to get started.</p>
                </div>
                <div class="py-8">
                    <h3 class="text-lg font-medium text-gray-900">What support do you offer?</h3>
                    <p class="mt-3 text-gray-500">We offer email support for all plans, with priority support for Professional and Enterprise customers.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
