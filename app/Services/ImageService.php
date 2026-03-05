<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    /**
     * Process an uploaded image: crop to a given aspect ratio, 
     * compress, and save to storage.
     *
     * @param string  $sourcePath Absolute path to the source image
     * @param string  $directory  e.g. 'buses/photos'
     * @param string  $disk       e.g. 'public'
     * @param int     $width      Target width in pixels (height derived from ratio)
     * @param int     $height     Target height in pixels
     * @param int     $quality    JPEG quality 1-100
     * @return string             Stored file path relative to disk root
     */
    public function cropAndCompress(
        string $sourcePath,
        string $directory,
        string $disk = 'public',
        int $width = 1400,
        int $height = 1000,
        int $quality = 82
    ): string {
        $manager = new ImageManager(new Driver());

        // Read the local file path
        $image = $manager->read($sourcePath);

        // Cover-crop: fills the target dimensions by smart center-crop
        $image->cover($width, $height);

        // Encode to JPEG with specified quality
        $encoded = $image->toJpeg($quality);

        // Generate a unique filename
        $filename = $directory . '/' . Str::uuid() . '.jpg';

        // Store the processed image
        Storage::disk($disk)->put($filename, $encoded->toString());

        return $filename;
    }
}
