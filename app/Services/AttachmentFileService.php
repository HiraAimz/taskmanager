<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

class AttachmentFileService
{
    private const INLINE_MIME_TYPES = [
        'image/avif',
        'image/bmp',
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/webp',
        'application/pdf',
        'text/plain',
        'text/csv',
    ];

    public function download(?string $path, ?string $name): StreamedResponse
    {
        $relativePath = $this->resolvePath($path);

        return Storage::disk('local')->download($relativePath, $this->safeName($name), [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function preview(?string $path, ?string $name, bool $thumbnail = false): ?Response
    {
        $relativePath = $this->resolvePath($path);
        $disk = Storage::disk('local');
        $fullPath = $disk->path($relativePath);
        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($fullPath) ?: 'application/octet-stream';

        if (! in_array($mimeType, self::INLINE_MIME_TYPES, true)) {
            return null;
        }

        $safeName = $this->safeName($name);
        if ($thumbnail && str_starts_with($mimeType, 'image/')) {
            $thumbnailResponse = $this->thumbnail($fullPath, $mimeType);
            if ($thumbnailResponse !== null) {
                return $thumbnailResponse;
            }

            return response('', 415, [
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store',
            ]);
        }

        return $disk->response($relativePath, $safeName, [
            'Content-Type' => $mimeType,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ], 'inline');
    }

    private function resolvePath(?string $path): string
    {
        if (! $path || str_contains($path, "\0")) {
            abort(404, 'File not found');
        }

        $relativePath = str_replace('\\', '/', $path);
        if (str_starts_with($relativePath, '/') || preg_match('~(^|/)\.{1,2}(/|$)~', $relativePath)) {
            abort(404, 'File not found');
        }

        $disk = Storage::disk('local');
        $root = realpath($disk->path(''));
        $fullPath = realpath($disk->path($relativePath));

        if (! $root || ! $fullPath || ! is_file($fullPath) || ! str_starts_with($fullPath, $root.DIRECTORY_SEPARATOR)) {
            abort(404, 'File not found');
        }

        return $relativePath;
    }

    private function safeName(?string $name): string
    {
        $name = basename(str_replace('\\', '/', $name ?? 'attachment'));
        $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?: 'attachment';

        return mb_substr($name, 0, 180);
    }

    private function thumbnail(string $path, string $mimeType): ?Response
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagecopyresampled')) {
            return null;
        }

        $imageInfo = @getimagesize($path);
        if (! $imageInfo || ! isset($imageInfo[0], $imageInfo[1]) || $imageInfo[0] * $imageInfo[1] > 4000000) {
            return null;
        }

        $bytes = @file_get_contents($path);
        if ($bytes === false) {
            return null;
        }

        $source = @imagecreatefromstring($bytes);
        unset($bytes);
        if ($source === false) {
            return null;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $scale = min(1, 480 / $sourceWidth, 320 / $sourceHeight);
        $width = max(1, (int) round($sourceWidth * $scale));
        $height = max(1, (int) round($sourceHeight * $scale));
        $thumbnail = imagecreatetruecolor($width, $height);

        if (in_array($mimeType, ['image/png', 'image/gif', 'image/webp', 'image/avif'], true)) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
            imagefill($thumbnail, 0, 0, $transparent);
        }

        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
        ob_start();
        $encoded = function_exists('imagewebp')
            ? imagewebp($thumbnail, null, 78)
            : imagejpeg($thumbnail, null, 78);
        $thumbnailBytes = ob_get_clean();
        imagedestroy($source);
        imagedestroy($thumbnail);

        if (! $encoded || $thumbnailBytes === false) {
            return null;
        }

        return response($thumbnailBytes, 200, [
            'Content-Type' => function_exists('imagewebp') ? 'image/webp' : 'image/jpeg',
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
