<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LogViewerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $logPath = storage_path('logs');
        $files = [];

        // 1. Get all log files
        if (File::exists($logPath)) {
            $files = collect(File::files($logPath))
                ->map(function ($file) {
                    return [
                        'name' => $file->getFilename(),
                        'date' => $this->extractDate($file->getFilename()),
                        'size' => $this->formatBytes($file->getSize()),
                        'modified' => $file->getMTime(),
                    ];
                })
                ->filter(function ($file) {
                    return Str::startsWith($file['name'], 'laravel-');
                })
                ->sortByDesc('modified')
                ->values()
                ->toArray();
        }

        // 2. Determine selected file
        $selectedFileName = $request->input('file');

        if (! $selectedFileName) {
            $todayLog = 'laravel-'.now()->format('Y-m-d').'.log';
            $selectedFileName = collect($files)->firstWhere('name', $todayLog)
                ? $todayLog
                : ($files[0]['name'] ?? null);
        }

        // 3. Read and parse content
        $logs = [];
        $currentLog = null;

        if ($selectedFileName && File::exists($logPath.'/'.$selectedFileName)) {
            $content = File::get($logPath.'/'.$selectedFileName);
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

            $pattern = '/^\[(?<date>.*)\] (?<env>\w+)\.(?<level>\w+): (?<message>.*)/m';

            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER, 0);

            $logs = [];
            foreach ($matches as $match) {
                $logs[] = [
                    'timestamp' => $match['date'],
                    'env' => $match['env'],
                    'level' => strtolower($match['level']),
                    'message' => trim($match['message']),
                ];
            }

            $logs = array_reverse($logs);

            $currentLog = [
                'name' => $selectedFileName,
                'size' => $this->formatBytes(File::size($logPath.'/'.$selectedFileName)),
            ];
        }

        return response()->json([
            'files' => $files,
            'currentFile' => $currentLog,
            'logs' => $logs,
        ]);
    }

    private function extractDate(string $filename): ?string
    {
        preg_match('/laravel-(\d{4}-\d{2}-\d{2})\.log/', $filename, $matches);

        return $matches[1] ?? null;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
