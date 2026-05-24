<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\EmployeeExport;
use App\Exports\MemberExport;
use App\Exports\FeedbackExport;
use App\Imports\EmployeeImport;
use App\Models\Employee;
use App\Models\Member;
use App\Models\Feedback;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ExportImportController extends Controller
{
    // ==================== EMPLOYEE ====================

    public function exportEmployeesExcel()
    {
        return (new EmployeeExport())->export();
    }

    public function exportEmployeesPdf()
    {
        $employees = Employee::orderBy('created_at', 'asc')->get();
        $pdf = Pdf::loadView('exports.employees-pdf', compact('employees'))
            ->setPaper('a4', 'landscape');
        
        return $pdf->download('employees_' . date('Y-m-d_His') . '.pdf');
    }

    public function importEmployeesTemplate()
    {
        return EmployeeExport::template();
    }

    public function importEmployees(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $file = $request->file('file');
        $tempPath = $file->store('temp', 'local');
        $fullPath = Storage::disk('local')->path($tempPath);

        try {
            $importer = (new EmployeeImport())->import($fullPath);
            
            $message = $importer->getImported() . ' data employee berhasil diimport.';
            if ($importer->getSkipped() > 0) {
                $message .= ' ' . $importer->getSkipped() . ' data dilewati.';
            }

            // Clean up
            @unlink($fullPath);

            if (!empty($importer->getErrors())) {
                return redirect()->route('dashboard.employees.index')
                    ->with('success', $message)
                    ->with('import_errors', $importer->getErrors());
            }

            return redirect()->route('dashboard.employees.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            @unlink($fullPath);
            return redirect()->route('dashboard.employees.index')
                ->withErrors(['file' => 'Gagal mengimport file: ' . $e->getMessage()]);
        }
    }

    // ==================== MEMBER ====================

    public function exportMembersExcel()
    {
        return (new MemberExport())->export();
    }

    public function exportMembersPdf()
    {
        $members = Member::with(['employee', 'role'])->orderBy('created_at', 'asc')->get();
        $pdf = Pdf::loadView('exports.members-pdf', compact('members'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('members_' . date('Y-m-d_His') . '.pdf');
    }

    // ==================== FEEDBACK ====================

    public function exportFeedbacksExcel()
    {
        return (new FeedbackExport())->export();
    }

    public function exportFeedbacksPdf()
    {
        $feedbacks = Feedback::with('member.employee')->orderBy('created_at', 'asc')->get();
        $pdf = Pdf::loadView('exports.feedbacks-pdf', compact('feedbacks'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('feedbacks_' . date('Y-m-d_His') . '.pdf');
    }
}
