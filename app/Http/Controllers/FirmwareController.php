<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Finder\Finder;

class FirmwareController extends Controller
{
    private $firmwarePath;
    private $uploadPath;

    public function __construct()
    {
        $this->middleware('auth');
        $this->firmwarePath = public_path('firmware');
        $this->uploadPath = public_path('firmware/upload');

        if (!File::exists($this->firmwarePath)) {
            File::makeDirectory($this->firmwarePath, 0755, true);
        }
        if (!File::exists($this->uploadPath)) {
            File::makeDirectory($this->uploadPath, 0755, true);
        }
    }

    public function index()
    {
        $imeis = DB::table('user_imei')->where('user_id', Auth::id())->pluck('device_name', 'imei');
        
        $firmwareList = [];
        if (File::exists($this->firmwarePath)) {
            $finder = new Finder();
            $files = $finder->files()->in($this->firmwarePath)->name('*.bin')->depth(0)->sortByModifiedTime();

            foreach ($files as $file) {
                $filename = $file->getFilename();
                $baseName = pathinfo($filename, PATHINFO_FILENAME);
                
                $parts = explode('_', $baseName);
                $imei = $parts[0] ?? 'N/A';
                $version = $parts[2] ?? 'N/A';
                $size = 'N/A';
                $crc = 'N/A';

                $metaPath = $this->firmwarePath . '/' . $baseName . '.json';
                $note = '';
                if (File::exists($metaPath)) {
                    $meta = json_decode(File::get($metaPath), true);
                    $note = $meta['note'] ?? '';
                    $version = $meta['ver'] ?? $version;
                    $size = $meta['size'] ?? 'N/A';
                    $crc = $meta['crc'] ?? 'N/A';
                }

                $crcPath = $this->firmwarePath . '/' . $baseName . '.txt';
                if (File::exists($crcPath)) {
                    $txtContent = trim(File::get($crcPath));
                    $txtParts = explode('@', $txtContent);
                    if (count($txtParts) >= 3) {
                        $size = $txtParts[1]; // Full value: 130516
                        $crc = $txtParts[2];  // CRC only: 0x6A55FC09
                    }
                }

                $firmwareList[] = [
                    'filename' => $filename,
                    'baseName' => $baseName,
                    'imei' => $imei,
                    'crc_file' => $baseName . '.txt',
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                    'version' => $version,
                    'size' => $size,
                    'crc' => $crc,
                    'note' => $note,
                    'timestamp' => $file->getMTime()
                ];
            }
        }

        $firmwareList = collect($firmwareList)->sortByDesc('timestamp')->values()->all();

        $uploadedFiles = [];
        if (File::exists($this->uploadPath)) {
            $uploadedFiles = File::files($this->uploadPath);
        }

        return view('firmware', compact('imeis', 'firmwareList', 'uploadedFiles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'imei' => 'required',
            'notes' => 'nullable|max:100',
            'firmware_file' => 'required|file'
        ]);

        $file = $request->file('firmware_file');
        $fileNameWithoutExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Validate filename matches IMEI
        if ($fileNameWithoutExt !== $request->imei) {
            return redirect()->back()->with('error', 'Uploaded file IMEI (' . $fileNameWithoutExt . ') does not match selected IMEI (' . $request->imei . ').');
        }

        // Extract version from file content (looking for FW_VER_ pattern)
        $content = file_get_contents($file->getRealPath());
        $version = 'unknown';
        if (preg_match('/FW_VER_([0-9.a-zA-Z_-]+)/', $content, $matches)) {
            $version = $matches[1];
        }

        // Check if this IMEI and Version combination already exists
        $finder = new Finder();
        $existingFiles = $finder->files()->in($this->firmwarePath)->name("{$request->imei}_*_{$version}.bin")->depth(0);
        if ($existingFiles->hasResults()) {
            return redirect()->back()->with('error', "Firmware version {$version} for IMEI {$request->imei} is already available in store.");
        }

        $date = now()->format('YmdHis');
        $baseName = "{$request->imei}_{$date}_{$version}";
        $binName = "{$baseName}.bin";
        $txtName = "{$baseName}.txt";
        $jsonName = "{$baseName}.json";

        $file->move($this->firmwarePath, $binName);
        $binPath = $this->firmwarePath . '/' . $binName;

        $size = File::size($binPath);
        $crc = strtoupper(hash_file('crc32b', $binPath));
        $crcContent = "@{$size}@0x{$crc}@";
        File::put($this->firmwarePath . '/' . $txtName, $crcContent);

        File::put($this->firmwarePath . '/' . $jsonName, json_encode([
            'imei' => $request->imei,
            'crc' => "0x{$crc}",
            'size' => $size,
            'ver' => $version,
            'note' => $request->notes
        ]));

        return redirect()->back()->with('success', 'Firmware uploaded successfully.');
    }

    public function deploy($baseName)
    {
        File::cleanDirectory($this->uploadPath);

        $parts = explode('_', $baseName);
        $imei = $parts[0] ?? 'unknown';

        $extensions = ['bin', 'txt', 'json'];
        $copied = false;

        foreach ($extensions as $ext) {
            $src = $this->firmwarePath . '/' . $baseName . '.' . $ext;
            $dest = $this->uploadPath . '/' . $imei . '.' . $ext;

            if (File::exists($src)) {
                File::copy($src, $dest);
                $copied = true;
            }
        }

        if ($copied) {
            return redirect()->back()->with('success', 'Firmware files copied and renamed in upload folder.');
        }

        return redirect()->back()->with('error', 'Source files not found.');
    }

    public function clearUpload()
    {
        File::cleanDirectory($this->uploadPath);
        return redirect()->back()->with('success', 'Upload folder cleared.');
    }

    public function destroy($baseName)
    {
        File::delete($this->firmwarePath . '/' . $baseName . '.bin');
        File::delete($this->firmwarePath . '/' . $baseName . '.txt');
        File::delete($this->firmwarePath . '/' . $baseName . '.json');

        return redirect()->back()->with('success', 'Firmware deleted.');
    }
}