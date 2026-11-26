<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageUploader
{
    
    public static function replace(?UploadedFile $file, string $directory, ?string $existingUrl = null): ?string
    {
        if (! $file) {
            return $existingUrl;
        }

        self::delete($existingUrl);
        $path = Storage::disk('uploads')->putFile($directory, $file);

        return '/uploads/'.ltrim((string) $path, '/');
    }

   
    public static function delete(?string $url): void
    {
        if (! $url || ! str_starts_with($url, '/uploads/')) {
            return;
        }

        $relativePath = substr($url, strlen('/uploads/'));
        if ($relativePath !== '' && Storage::disk('uploads')->exists($relativePath)) {
            Storage::disk('uploads')->delete($relativePath);
        }
    }
}
