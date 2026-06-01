<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class EmployeeImport
{
    protected int $imported = 0;
    protected int $skipped = 0;
    protected array $errors = [];

    public function import(string $filePath): self
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        // Skip header row (row 1)
        $isFirstRow = true;
        foreach ($rows as $rowIndex => $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            $badge = trim($row['A'] ?? '');
            $name = trim($row['B'] ?? '');

            // Skip empty rows
            if (empty($badge) && empty($name)) {
                continue;
            }

            // Validate required fields
            if (empty($badge)) {
                $this->errors[] = "Row {$rowIndex}: Badge cannot be empty.";
                $this->skipped++;
                continue;
            }

            if (empty($name)) {
                $this->errors[] = "Row {$rowIndex}: Name cannot be empty.";
                $this->skipped++;
                continue;
            }

            // Check for duplicate badge
            if (Employee::where('badge', $badge)->exists()) {
                $this->errors[] = "Row {$rowIndex}: Badge '{$badge}' is already registered, data skipped.";
                $this->skipped++;
                continue;
            }

            try {
                $employee = Employee::create([
                    'badge' => $badge,
                    'name' => $name,
                    'department' => !empty(trim($row['C'] ?? '')) ? trim($row['C']) : null,
                    'line' => !empty(trim($row['D'] ?? '')) ? trim($row['D']) : null,
                    'position' => !empty(trim($row['E'] ?? '')) ? trim($row['E']) : null,
                    'join_date' => $this->parseDate($row['F'] ?? ''),
                    'end_date' => $this->parseDate($row['G'] ?? ''),
                    'birth_place' => !empty(trim($row['H'] ?? '')) ? trim($row['H']) : null,
                    'birth_date' => $this->parseDate($row['I'] ?? ''),
                    'address' => !empty(trim($row['J'] ?? '')) ? trim($row['J']) : null,
                ]);

                // Create user account
                User::firstOrCreate(
                    ['badge' => $badge],
                    [
                        'name' => $name,
                        'password' => Hash::make('P4ssword'),
                        'role' => 'user',
                    ]
                );

                $this->imported++;
            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowIndex}: Failed to save data - {$e->getMessage()}";
                $this->skipped++;
            }
        }

        return $this;
    }

    protected function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);

        // Handle Excel numeric date format
        if (is_numeric($value)) {
            try {
                $date = ExcelDate::excelToDateTimeObject((int) $value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Handle dd/mm/yyyy format
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $value, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            return "{$year}-{$month}-{$day}";
        }

        // Handle yyyy-mm-dd format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        return null;
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
