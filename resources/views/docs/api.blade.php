@extends('layouts.app')

@section('title', 'API Documentation')

@section('content')
<div class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-12">
            <nav class="text-sm breadcrumbs mb-4">
                <a href="{{ url('/docs') }}" class="text-blue-600 hover:text-blue-500">Documentation</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-500">API Reference</span>
            </nav>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">API Documentation</h1>
            <p class="text-xl text-gray-600">Complete reference for the HR Management System API</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Navigation -->
            <div class="lg:col-span-1">
                <div class="sticky top-8">
                    <nav class="space-y-2">
                        <h3 class="font-semibold text-gray-900 mb-3">API Sections</h3>
                        @foreach($endpoints as $section => $items)
                        <a href="#{{ strtolower($section) }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md">
                            {{ $section }}
                        </a>
                        @endforeach
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Base URL -->
                <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Base URL</h2>
                    <code class="text-sm bg-gray-800 text-green-400 px-3 py-1 rounded">{{ config('app.url') }}/api/v1</code>
                </div>

                <!-- Authentication -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Authentication</h2>
                    <p class="text-gray-600 mb-4">All API requests require authentication using Bearer tokens.</p>
                    <div class="bg-gray-900 rounded-lg p-4 mb-4">
                        <pre class="text-green-400 text-sm overflow-x-auto"><code>Authorization: Bearer your-api-token-here</code></pre>
                    </div>
                </div>

                <!-- Endpoints -->
                @foreach($endpoints as $section => $items)
                <div id="{{ strtolower($section) }}" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $section }}</h2>
                    
                    @foreach($items as $endpoint)
                    <div class="mb-6 border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($endpoint['method'] === 'GET') bg-green-100 text-green-800
                                    @elseif($endpoint['method'] === 'POST') bg-blue-100 text-blue-800
                                    @elseif($endpoint['method'] === 'PUT') bg-yellow-100 text-yellow-800
                                    @elseif($endpoint['method'] === 'DELETE') bg-red-100 text-red-800
                                    @endif">
                                    {{ $endpoint['method'] }}
                                </span>
                                <code class="text-sm font-mono text-gray-800">{{ $endpoint['endpoint'] }}</code>
                            </div>
                        </div>
                        <div class="px-6 py-4">
                            <p class="text-gray-600 mb-4">{{ $endpoint['description'] }}</p>
                            
                            @if($endpoint['method'] === 'POST' && str_contains($endpoint['endpoint'], 'login'))
                            <div class="mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Request Body</h4>
                                <div class="bg-gray-900 rounded-lg p-4">
                                    <pre class="text-green-400 text-sm overflow-x-auto"><code>{
  "email": "user@example.com",
  "password": "password123"
}</code></pre>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Response</h4>
                                <div class="bg-gray-900 rounded-lg p-4">
                                    <pre class="text-green-400 text-sm overflow-x-auto"><code>{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com"
  }
}</code></pre>
                                </div>
                            </div>
                            @endif

                            @if($endpoint['method'] === 'GET' && str_contains($endpoint['endpoint'], 'employees'))
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Response</h4>
                                <div class="bg-gray-900 rounded-lg p-4">
                                    <pre class="text-green-400 text-sm overflow-x-auto"><code>{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@company.com",
      "position": "Software Engineer",
      "department": "Engineering",
      "hire_date": "2024-01-15"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 25
  }
}</code></pre>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach

                <!-- Rate Limiting -->
                <div class="mb-8 p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Rate Limiting</h3>
                    <p class="text-yellow-700">API requests are limited to 1000 requests per hour per API key.</p>
                </div>

                <!-- Error Codes -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Error Codes</h2>
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">200</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Success</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">401</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Unauthorized</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">403</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Forbidden</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">404</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Not Found</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">422</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Validation Error</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">500</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Internal Server Error</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
