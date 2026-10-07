<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\LRiotSim;
use Carbon\Carbon;

class RiotSimImport implements ToModel, WithHeadingRow
{

    public static $importedCount = 0; // Static property to keep track of inserted rows
    public function model(array $row)
    {

        // Check if the row is empty (all fields are empty or missing)
        if (empty(trim($row['simno'])) && empty(trim($row['simphonenumber'])) && empty(trim($row['simoperator']))) {
            return null; // Skip this row
        }


        // Trim and sanitize the input
        $simNo = trim($row['simno']);
        $simPhoneNumber = trim($row['simphonenumber']);
        $simOperator = trim($row['simoperator']);

        // Check for duplicates in simPhoneNumber or simOperator
        if (LRiotSim::where('simPhoneNumber', $simPhoneNumber)->exists()) {
            throw new \Exception('Duplicate SIM Phone number found: ' . $simPhoneNumber);
        }

        if (LRiotSim::where('simNo', $simNo)->exists()) {
            throw new \Exception('Duplicate SIM IMEI found: ' . $simNo);
        }

        try {
            $data = [
                'simStatus' => 3,
                'simNo' => $simNo,
                'simPhoneNumber' => $simPhoneNumber,
                'simOperator' => $simOperator,
                'created_by' => auth()->user()->id, // Assuming user authentication is used
                'created_at' => Carbon::now()
            ];

            // Insert or update based on simNo uniqueness
            LRiotSim::updateOrCreate(['simNo' => $data['simNo']], $data);

            // Increment imported count
            self::$importedCount++;

        } catch (\Exception $e) {
            // Handle any errors during import
            throw new \Exception('Error importing row: ' . $e->getMessage());
        }
    }

    public function getRowCount() { 
        return self::$importedCount;
     }
}
