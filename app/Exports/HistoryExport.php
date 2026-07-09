<?php

namespace App\Exports;

use App\Models\RiwayatAnggota;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HistoryExport
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Build the query with applied filters (shared between Excel and PDF).
     */
    public static function buildQuery(array $filters = [])
    {
        $query = RiwayatAnggota::with(['anggota.karyawan', 'pelaku'])->latest();

        $search = $filters['q'] ?? null;
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('aktivitas', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('pelaku', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('anggota.karyawan', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $activity = $filters['activity'] ?? null;
        if ($activity && $activity !== 'Semua Aktivitas') {
            $query->where('aktivitas', $activity);
        }

        $actor = $filters['actor'] ?? null;
        if ($actor && $actor !== 'Semua Pelaku') {
            $query->where('pelaku_id', $actor);
        }

        $date = $filters['date'] ?? null;
        if ($date) {
            $query->whereDate('created_at', $date);
        }

        return $query;
    }

    public function export()
    {
        $logs = self::buildQuery($this->filters)->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Log');

        // Headers
        $headers = ['No', 'Tanggal', 'Pelaku', 'Aktivitas', 'Target Anggota', 'Deskripsi'];
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
            $sheet->setCellValue("C{$row}", $log->pelaku ? $log->pelaku->nama : 'Sistem');
            $sheet->setCellValue("D{$row}", ucfirst($log->aktivitas));
            $sheet->setCellValue("E{$row}", $log->anggota && $log->anggota->karyawan ? $log->anggota->karyawan->nama : '-');
            $sheet->setCellValue("F{$row}", $log->deskripsi ?? '-');

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
        $filename = 'riwayat_log_' . date('Y-m-d_His') . '.xlsx';
        $tempFile = storage_path('app/' . $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
