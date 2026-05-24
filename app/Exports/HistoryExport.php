<?php

namespace App\Exports;

use App\Models\MemberLog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HistoryExport
{
    public function export()
    {
        $logs = MemberLog::with(['member.employee', 'actor'])->orderBy('created_at', 'desc')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('History Logs');

        // Headers
        $headers = ['No', 'Tanggal', 'Aktor', 'Aktivitas', 'Target Member', 'Deskripsi'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F'];

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1b007c']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue($columns[$index] . '1', $header);
        }
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Data
        $row = 2;
        foreach ($logs as $index => $log) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $log->created_at?->format('d/m/Y H:i:s'));
            $sheet->setCellValue("C{$row}", $log->actor ? $log->actor->name : 'Sistem');
            $sheet->setCellValue("D{$row}", ucfirst($log->activity));
            $sheet->setCellValue("E{$row}", $log->member && $log->member->employee ? $log->member->employee->name : '-');
            $sheet->setCellValue("F{$row}", $log->description ?? '-');

            if ($index % 2 === 0) {
                $sheet->getStyle("A{$row}:F{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F4F6');
            }
            $row++;
        }

        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $sheet->getStyle("A2:F{$row}")->applyFromArray($dataStyle);
        $sheet->getStyle("A2:A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'history_logs_' . date('Y-m-d_His') . '.xlsx';
        $tempFile = storage_path('app/' . $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
