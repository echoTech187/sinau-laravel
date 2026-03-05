<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProcessImageJob implements ShouldQueue
{
    use Queueable;

    public string $modelClass;
    public int $modelId;
    public string $sourcePath;
    public string $directory;
    public string $disk;

    /**
     * Create a new job instance.
     */
    public function __construct(string $modelClass, int $modelId, string $sourcePath, string $directory, string $disk = 'public')
    {
        $this->modelClass = $modelClass;
        $this->modelId = $modelId;
        $this->sourcePath = $sourcePath;
        $this->directory = $directory;
        $this->disk = $disk;
    }

    /**
     * Execute the job.
     */
    public function handle(ImageService $imageService): void
    {
        try {
            // Check if source file actually exists
            if (!file_exists($this->sourcePath)) {
                Log::error("ProcessImageJob: Source file not found: {$this->sourcePath}");
                return;
            }

            // Process image using ImageService
            $processedPath = $imageService->cropAndCompress(
                sourcePath: $this->sourcePath,
                directory: $this->directory,
                disk: $this->disk
            );

            // Fetch the model
            $model = $this->modelClass::find($this->modelId);
            
            if ($model) {
                // Delete old photo if it exists and we're maintaining photo_path
                if ($model->photo_path) {
                    Storage::disk($this->disk)->delete($model->photo_path);
                }

                // Update the model with the new photo path
                $model->update(['photo_path' => $processedPath]);
            }

            // Delete the raw temporary file
            @unlink($this->sourcePath);

        } catch (\Exception $e) {
            Log::error("ProcessImageJob: Failed processing image for {$this->modelClass}: {$e->getMessage()}");
            // If it fails, also try to clean up the temp file
            @unlink($this->sourcePath);
        }
    }
}
