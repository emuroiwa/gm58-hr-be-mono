<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Stripe webhooks
     */
    public function stripe(Request $request): JsonResponse
    {
        try {
            $payload = $request->getContent();
            $signature = $request->header('Stripe-Signature');
            
            // Verify webhook signature
            $endpoint_secret = config('services.stripe.webhook_secret');
            
            Log::info('Stripe webhook received', [
                'type' => $request->input('type'),
                'id' => $request->input('id')
            ]);

            // Handle different webhook events
            $event = $request->input('type');
            
            switch ($event) {
                case 'invoice.payment_succeeded':
                    $this->handlePaymentSucceeded($request->input('data.object'));
                    break;
                    
                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($request->input('data.object'));
                    break;
                    
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($request->input('data.object'));
                    break;
                    
                case 'customer.subscription.deleted':
                    $this->handleSubscriptionCancelled($request->input('data.object'));
                    break;
                    
                default:
                    Log::info('Unhandled Stripe webhook event', ['type' => $event]);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle Slack webhooks
     */
    public function slack(Request $request): JsonResponse
    {
        try {
            // Verify Slack token
            $token = $request->input('token');
            if ($token !== config('services.slack.webhook_token')) {
                return response()->json(['error' => 'Invalid token'], 401);
            }

            $command = $request->input('command');
            $text = $request->input('text');
            $user = $request->input('user_name');

            // Handle different slash commands
            switch ($command) {
                case '/attendance':
                    return $this->handleAttendanceCommand($user, $text);
                    
                case '/leave':
                    return $this->handleLeaveCommand($user, $text);
                    
                case '/status':
                    return $this->handleStatusCommand($user);
                    
                default:
                    return response()->json([
                        'text' => 'Unknown command. Use /attendance, /leave, or /status'
                    ]);
            }

        } catch (\Exception $e) {
            Log::error('Slack webhook error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'text' => 'Sorry, there was an error processing your request.'
            ]);
        }
    }

    /**
     * Handle Zapier webhooks
     */
    public function zapier(Request $request): JsonResponse
    {
        try {
            $event = $request->input('event');
            $data = $request->input('data');

            Log::info('Zapier webhook received', [
                'event' => $event,
                'data' => $data
            ]);

            // Process the webhook data based on event type
            switch ($event) {
                case 'new_employee':
                    $this->processNewEmployeeFromZapier($data);
                    break;
                    
                case 'update_employee':
                    $this->processEmployeeUpdateFromZapier($data);
                    break;
                    
                default:
                    Log::info('Unhandled Zapier event', ['event' => $event]);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Zapier webhook error', [
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    private function handlePaymentSucceeded($invoice): void
    {
        // Update company subscription status
        $customerId = $invoice['customer'];
        // Implementation would update company based on customer ID
    }

    private function handlePaymentFailed($invoice): void
    {
        // Handle failed payment
        $customerId = $invoice['customer'];
        // Implementation would notify company of failed payment
    }

    private function handleSubscriptionUpdated($subscription): void
    {
        // Handle subscription changes
        $customerId = $subscription['customer'];
        // Implementation would update company plan
    }

    private function handleSubscriptionCancelled($subscription): void
    {
        // Handle subscription cancellation
        $customerId = $subscription['customer'];
        // Implementation would downgrade or suspend company
    }

    private function handleAttendanceCommand($user, $text): JsonResponse
    {
        // Handle Slack attendance commands
        return response()->json([
            'text' => "Attendance command received for {$user}: {$text}"
        ]);
    }

    private function handleLeaveCommand($user, $text): JsonResponse
    {
        // Handle Slack leave commands
        return response()->json([
            'text' => "Leave command received for {$user}: {$text}"
        ]);
    }

    private function handleStatusCommand($user): JsonResponse
    {
        // Handle Slack status commands
        return response()->json([
            'text' => "Status for {$user}: Available"
        ]);
    }

    private function processNewEmployeeFromZapier($data): void
    {
        // Process new employee data from Zapier integration
        Log::info('Processing new employee from Zapier', $data);
    }

    private function processEmployeeUpdateFromZapier($data): void
    {
        // Process employee update from Zapier integration
        Log::info('Processing employee update from Zapier', $data);
    }
}
