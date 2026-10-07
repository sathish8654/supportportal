<?php

namespace App\Imports;

use App\Models\imei_registration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImeiImport implements ToModel, WithHeadingRow
{
    public static $importedCount = 0;
    public static $skippedCount = 0;
    public static $errors = [];

    public function __construct()
    {
        self::$importedCount = 0;
        self::$skippedCount = 0;
        self::$errors = [];
    }

    public function model(array $row)
    {
        $imei = null;
        $note = '';

        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(trim((string)$key));
            if ($normalizedKey === 'imei') {
                $imei = trim((string)$value);
            } elseif (in_array($normalizedKey, ['note', 'notes', 'description'])) {
                $note = trim((string)$value);
            }
        }

        // If heading wasn't matched by name, check first non-empty cell
        if ($imei === null && !empty($row)) {
            $firstVal = reset($row);
            if (!empty($firstVal)) {
                $imei = trim((string)$firstVal);
            }
        }

        if (empty($imei)) {
            return null;
        }

        // Check for duplicate in database
        if (imei_registration::where('imei', $imei)->exists()) {
            self::$skippedCount++;
            return null;
        }

        try {
            self::$importedCount++;

            $currentUserId = Auth::check() ? Auth::user()->id : 1;

            return new imei_registration([
                'imei' => $imei,
                'note' => $note,
                'status' => 'active',
                'created_by' => $currentUserId,
                'created_at' => Carbon::now(),
                'updated_by' => $currentUserId,
                'updated_at' => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            self::$errors[] = 'Error importing IMEI ' . $imei . ': ' . $e->getMessage();
            return null;
        }
    }

    public function getRowCount()
    {
        return self::$importedCount;
    }

    public function getSkippedCount()
    {
        return self::$skippedCount;
    }

    public function getErrors()
    {
        return self::$errors;
    }
}
