<?php

namespace App\Exports;

use App\Models\Anggota;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MemberExport
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
        $sortDirection = $sort === 'asc' ? 'asc' : 'desc';

        return Anggota::with(['karyawan', 'jabatan'])
            ->when($q, function($query, $q) {
                $query->whereHas('karyawan', function($q2) use ($q) {
                    $q2->where('nama', 'like', "%{$q}%")
                       ->orWhere('badge', 'like', "%{$q}%");
                });
            })
            ->when($status && $status !== 'Semua Status', function($query) use ($status) {
                $statusMap = [
                    'Anggota Terdaftar' => 'registered',
                    'Menunggu Verifikasi' => 'pending',
                    'Tidak Aktif' => 'inactive'
                ];
                if (isset($statusMap[$status])) {
                    if ($statusMap[$status] === 'inactive') {
                        $query->where(function($q) {
                            $q->where('status', 'inactive')
                              ->orWhereHas('karyawan', function($q2) {
                                  $q2->whereNotNull('tanggal_keluar')
                                     ->where('tanggal_keluar', '<=', today());
                              });
                        });
                    } else {
                        $query->where('status', $statusMap[$status])
                              ->whereHas('karyawan', function($q2) {
                                  $q2->where(function($q3) {
                                      $q3->whereNull('tanggal_keluar')
                                         ->orWhere('tanggal_keluar', '>', today());
                                  });
                              });
                    }
                }
            })
            ->orderBy('updated_at', $sortDirection);
    }

    public function export()
    {
        $members = self::buildQuery($this->filters)->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Anggota');

        // Headers
        $headers = ['No', 'Badge', 'Nama Karyawan', 'Departemen', 'Jabatan', 'Line', 'Jabatan Anggota', 'Status', 'Terdaftar Pada'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

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
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Data
        $row = 2;
        foreach ($members as $index => $member) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $member->karyawan->badge ?? '-');
            $sheet->setCellValue("C{$row}", $member->karyawan->nama ?? '-');
            $sheet->setCellValue("D{$row}", $member->karyawan->departemen ?? '-');
            $sheet->setCellValue("E{$row}", $member->karyawan->jabatan ?? '-');
            $sheet->setCellValue("F{$row}", $member->karyawan->line ?? '-');
            $sheet->setCellValue("G{$row}", $member->jabatan->nama ?? 'Member');
            $sheet->setCellValue("H{$row}", ucfirst($member->effective_status));
            $sheet->setCellValue("I{$row}", $member->created_at?->format('d/m/Y H:i'));

            if ($index % 2 === 0) {
                $sheet->getStyle("A{$row}:I{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F4F6');
            }
            $row++;
        }

        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $sheet->getStyle("A2:I{$row}")->applyFromArray($dataStyle);
        $sheet->getStyle("A2:A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'anggota_' . date('Y-m-d_His') . '.xlsx';
        $tempFile = storage_path('app/' . $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
