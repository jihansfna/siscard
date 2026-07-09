<?php

namespace App\Exports;

use App\Models\Saran;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FeedbackExport
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
        $q = $filters['q'] ?? null;
        $status = $filters['status'] ?? null;
        $sort = $filters['sort'] ?? 'desc';

        return Saran::with('anggota.karyawan')
            ->when($q, function($query, $q) {
                $query->whereHas('anggota.karyawan', function($q2) use ($q) {
                    $q2->where('nama', 'like', "%{$q}%")
                       ->orWhere('badge', 'like', "%{$q}%");
                });
            })
            ->when($status && $status !== 'Semua Status', function($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('updated_at', $sort);
    }

    public function export()
    {
        $feedbacks = self::buildQuery($this->filters)->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Saran');

        // Headers
        $headers = ['No', 'Badge Pengirim', 'Nama Pengirim', 'Isi Saran', 'Tanggal', 'Status', 'Catatan / Balasan'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

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
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Data
        $row = 2;
        foreach ($feedbacks as $index => $fb) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $fb->anonim ? 'Rahasia' : ($fb->anggota->karyawan->badge ?? '-'));
            $sheet->setCellValue("C{$row}", $fb->anonim ? 'Anonim' : ($fb->anggota->karyawan->nama ?? 'Tidak diketahui'));
            $sheet->setCellValue("D{$row}", $fb->deskripsi);
            $sheet->setCellValue("E{$row}", $fb->created_at?->format('d/m/Y H:i'));
            $sheet->setCellValue("F{$row}", $fb->status);
            $sheet->setCellValue("G{$row}", $fb->catatan ?? '-');

            if ($index % 2 === 0) {
                $sheet->getStyle("A{$row}:G{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F4F6');
            }
            $row++;
        }

        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $sheet->getStyle("A2:G{$row}")->applyFromArray($dataStyle);
        $sheet->getStyle("A2:A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setWidth(50);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setWidth(40);

        $writer = new Xlsx($spreadsheet);
        $filename = 'saran_' . date('Y-m-d_His') . '.xlsx';
        $tempFile = storage_path('app/' . $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
