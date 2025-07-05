<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Show the landing page
     */
    public function index(): View
    {
        $features = [
            [
                'title' => 'Employee Management',
                'description' => 'Comprehensive employee profiles, hierarchy, and organization management.',
                'icon' => 'users'
            ],
            [
                'title' => 'Payroll Processing',
                'description' => 'Automated payroll calculations with tax deductions and benefits.',
                'icon' => 'credit-card'
            ],
            [
                'title' => 'Attendance Tracking',
                'description' => 'Real-time attendance monitoring with flexible check-in/out options.',
                'icon' => 'clock'
            ],
            [
                'title' => 'Leave Management',
                'description' => 'Streamlined leave applications with approval workflows.',
                'icon' => 'calendar'
            ],
            [
                'title' => 'Performance Reviews',
                'description' => 'Structured performance evaluations with goal tracking.',
                'icon' => 'trending-up'
            ],
            [
                'title' => 'Advanced Reporting',
                'description' => 'Comprehensive analytics and insights into HR metrics.',
                'icon' => 'bar-chart'
            ]
        ];

        $stats = [
            'companies' => \App\Models\Company::count(),
            'employees' => \App\Models\Employee::count(),
            'countries' => 25, // Static for demo
            'satisfaction' => 98 // Static for demo
        ];

        return view('home.index', compact('features', 'stats'));
    }

    /**
     * Show about page
     */
    public function about(): View
    {
        $team = [
            [
                'name' => 'Sarah Johnson',
                'role' => 'CEO & Founder',
                'bio' => 'Former HR Director with 15+ years experience in enterprise HR systems.',
                'image' => 'team/sarah.jpg'
            ],
            [
                'name' => 'Michael Chen',
                'role' => 'CTO',
                'bio' => 'Full-stack developer specializing in scalable HR technology solutions.',
                'image' => 'team/michael.jpg'
            ],
            [
                'name' => 'Emily Rodriguez',
                'role' => 'Head of Product',
                'bio' => 'Product strategist focused on user experience and HR workflow optimization.',
                'image' => 'team/emily.jpg'
            ]
        ];

        return view('home.about', compact('team'));
    }

    /**
     * Show features page
     */
    public function features(): View
    {
        $featureCategories = [
            'Core HR' => [
                'Employee Management',
                'Organization Structure',
                'Department & Position Management',
                'Employee Self-Service Portal'
            ],
            'Time & Attendance' => [
                'Clock In/Out Tracking',
                'Overtime Management',
                'Shift Scheduling',
                'Time-off Requests'
            ],
            'Payroll & Benefits' => [
                'Automated Payroll Processing',
                'Tax Calculations',
                'Benefits Administration',
                'Direct Deposit'
            ],
            'Performance & Development' => [
                'Performance Reviews',
                'Goal Setting & Tracking',
                'Training Management',
                'Career Development'
            ],
            'Analytics & Reporting' => [
                'Real-time Dashboards',
                'Custom Report Builder',
                'Compliance Reporting',
                'Data Export Tools'
            ]
        ];

        return view('home.features', compact('featureCategories'));
    }

    /**
     * Show pricing page
     */
    public function pricing(): View
    {
        $plans = [
            [
                'name' => 'Starter',
                'price' => 5,
                'period' => 'per employee/month',
                'description' => 'Perfect for small businesses getting started',
                'features' => [
                    'Up to 25 employees',
                    'Basic employee management',
                    'Time tracking',
                    'Leave management',
                    'Email support'
                ],
                'highlighted' => false
            ],
            [
                'name' => 'Professional',
                'price' => 8,
                'period' => 'per employee/month',
                'description' => 'Ideal for growing companies',
                'features' => [
                    'Up to 100 employees',
                    'Advanced payroll',
                    'Performance reviews',
                    'Custom reports',
                    'API access',
                    'Priority support'
                ],
                'highlighted' => true
            ],
            [
                'name' => 'Enterprise',
                'price' => 12,
                'period' => 'per employee/month',
                'description' => 'For large organizations with complex needs',
                'features' => [
                    'Unlimited employees',
                    'Advanced integrations',
                    'Custom workflows',
                    'Dedicated support',
                    'SLA guarantee',
                    'Custom training'
                ],
                'highlighted' => false
            ]
        ];

        return view('home.pricing', compact('plans'));
    }

    /**
     * Show contact page
     */
    public function contact(): View
    {
        return view('home.contact');
    }

    /**
     * Handle contact form submission
     */
    public function submitContact(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Store contact inquiry
            \App\Models\ContactInquiry::create([
                'name' => $request->name,
                'email' => $request->email,
                'company' => $request->company,
                'phone' => $request->phone,
                'message' => $request->message,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Send notification email to sales team
            // Mail::to('sales@hrms.com')->send(new ContactInquiryMail($request->all()));

            return response()->json([
                'message' => 'Thank you for your inquiry! We\'ll get back to you within 24 hours.',
                'success' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sorry, there was an error sending your message. Please try again.',
                'success' => false
            ], 500);
        }
    }
}
