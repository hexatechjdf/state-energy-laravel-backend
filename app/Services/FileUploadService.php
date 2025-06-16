<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload a file to a specified disk and path.
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string|null $disk
     * @return string The stored file path
     */
    public function upload(UploadedFile $file, string $path, string $disk = null): string
    {
        $disk = $disk ?? config('filesystems.default');
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($path, $filename, $disk);
    }

    /**
     * Delete a file from a disk.
     *
     * @param string $filePath
     * @param string|null $disk
     * @return bool
     */
    public function delete(string $filePath, string $disk = null): bool
    {
        $disk = $disk ?? config('filesystems.default');
        return Storage::disk($disk)->delete($filePath);
    }

    /**
     * Get the full URL of a file.
     *
     * @param string $filePath
     * @param string|null $disk
     * @return string
     */
    public function getUrl(string $filePath, string $disk = null): string
    {
        $disk = $disk ?? config('filesystems.default');

        return Storage::disk($disk)->url($filePath);
    }
}
