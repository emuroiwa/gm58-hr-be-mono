<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    /**
     * Download payslip
     */
    public function payslip(Request $request, string $payrollId): BinaryFileResponse
    {
        $user = $request->user();
        
        // Find payroll record
        $payroll = \App\Models\Payroll::where('id', $payrollId)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$payroll) {
            abort(404, 'Payslip not found');
        }

        // Check if user can access this payslip
        if ($user->role === 'employee' && $payroll->employee_id !== $user->employee_id) {
            abort(403, 'Unauthorized access');
        }

        // Generate or retrieve payslip file
        $filename = "payslip_{$payroll->id}_{$payroll->employee->employee_id}.pdf";
        $filepath = "payslips/{$filename}";

        if (!Storage::exists($filepath)) {
            // Generate payslip PDF if it doesn't exist
            $this->generatePayslipPDF($payroll, $filepath);
        }

        return Storage::download($filepath, $filename);
    }

    /**
     * Download report
     */
    public function report(Request $request, string $reportId): BinaryFileResponse
    {
        $user = $request->user();
        
        // Check if user has permission to download reports
        if (!in_array($user->role, ['admin', 'hr', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $filepath = "reports/{$reportId}";

        if (!Storage::exists($filepath)) {
            abort(404, 'Report not found');
        }

        // Verify the report belongs to user's company (based on filename pattern)
        if (!str_contains($reportId, "company_{$user->company_id}_")) {
            abort(403, 'Unauthorized access');
        }

        return Storage::download($filepath);
    }

    /**
     * Download document
     */
    public function document(Request $request, string $documentId): BinaryFileResponse
    {
        $user = $request->user();
        
        // Find document record
        $document = \App\Models\Document::where('id', $documentId)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$document) {
            abort(404, 'Document not found');
        }

        // Check if user can access this document
        if ($user->role === 'employee' && $document->employee_id !== $user->employee_id) {
            abort(403, 'Unauthorized access');
        }

        if (!Storage::exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::download($document->file_path, $document->original_name);
    }

    private function generatePayslipPDF($payroll, $filepath): void
    {
        // This would generate a PDF payslip
        // For now, create a simple text file as placeholder
        $content = "Payslip for {$payroll->employee->first_name} {$payroll->employee->last_name}\n";
        $content .= "Period: {$payroll->payrollPeriod->name}\n";
        $content .= "Gross Pay: {$payroll->gross_pay}\n";
        $content .= "Net Pay: {$payroll->net_pay}\n";
        
        Storage::put($filepath, $content);
    }
}
