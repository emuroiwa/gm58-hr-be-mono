<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;

    public function __construct(
        public string $email,
        public string $subject,
        public string $message,
        public array $data = [],
        public string $template = 'default',
        public ?int $userId = null
    ) {}

    public function handle()
    {
        try {
            Log::info("Sending email notification", [
                'email' => $this->email,
                'subject' => $this->subject,
                'template' => $this->template
            ]);

            // Get user data if user ID provided
            $user = $this->userId ? User::find($this->userId) : null;

            // Prepare email data
            $emailData = array_merge($this->data, [
                'subject' => $this->subject,
                'message' => $this->message,
                'user' => $user,
                'company' => $user?->company
            ]);

            // Send email based on template type
            switch ($this->template) {
                case 'welcome':
                    Mail::send('emails.welcome', $emailData, function ($mail) {
                        $mail->to($this->email)->subject($this->subject);
                    });
                    break;

                case 'payroll':
                    Mail::send('emails.payroll', $emailData, function ($mail) {
                        $mail->to($this->email)->subject($this->subject);
                    });
                    break;

                case 'leave_approval':
                    Mail::send('emails.leave-approval', $emailData, function ($mail) {
                        $mail->to($this->email)->subject($this->subject);
                    });
                    break;

                case 'password_reset':
                    Mail::send('emails.password-reset', $emailData, function ($mail) {
                        $mail->to($this->email)->subject($this->subject);
                    });
                    break;

                default:
                    Mail::send('emails.notification', $emailData, function ($mail) {
                        $mail->to($this->email)->subject($this->subject);
                    });
                    break;
            }

            Log::info("Email sent successfully", ['email' => $this->email]);

        } catch (Exception $e) {
            Log::error("Failed to send email notification", [
                'email' => $this->email,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function failed(Exception $exception)
    {
        Log::error("Email notification job permanently failed", [
            'email' => $this->email,
            'subject' => $this->subject,
            'error' => $exception->getMessage()
        ]);
    }
}
