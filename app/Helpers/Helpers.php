<?php

use Illuminate\Support\Str;

function stringLimit($text, $limit = 50, $suffix = '...')
{
    return Str::limit($text, $limit, $suffix);
}

function multiLineText($text)
{
    $lines = explode("\n", $text);

    return implode('<br>', array_map('e', $lines));
}

if (! function_exists('saveImage')) {
    /**
     * @param  \Illuminate\Http\UploadedFile  $file
     */
    function saveImage($file, ?string $folder = 'images', ?string $filename = null): string
    {
        if (! $file || ! $file->isValid()) {
            throw new \InvalidArgumentException('Invalid file provided');
        }

        $folder = $folder ?? 'images';

        $extension = $file->getClientOriginalExtension();

        $finalName = ($filename ? Str::slug($filename) : Str::random(20)).'.'.$extension;

        $path = $file->storeAs($folder, $finalName, 'public');

        return $path;
    }
}

if (! function_exists('saveAudio')) {
    /**
     * Save base64 audio to storage
     *
     * @param  string|null  $filename  Optional filename without extension
     * @return string Public path
     */
    function saveAudio(
        string $base64,
        ?string $folder = null,
        ?string $filename = null
    ): string {
        // Get configuration with fallbacks
        $config = app('audio')->config();

        $folder = $folder ?? $config['default_folder'].'/audio';
        $disk = $config['disk'];

        // Extract base64 data and original format
        if (preg_match('/^data:audio\/(\w+);base64,/', $base64, $type)) {
            $base64 = substr($base64, strpos($base64, ',') + 1);
            $extension = strtolower($type[1]);
        } else {
            throw new InvalidArgumentException('Invalid base64 audio format');
        }

        $binary = base64_decode($base64);
        if ($binary === false) {
            throw new InvalidArgumentException('Invalid base64 data');
        }

        // Generate filename
        $filename = $filename ? $filename.'.'.$extension : uniqid().'.'.$extension;

        // Ensure directory exists
        $storagePath = 'app/'.$disk.'/'.$folder;
        $fullPath = storage_path($storagePath);
        if (! is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // Save audio
        $filePath = $fullPath.'/'.$filename;

        if (file_put_contents($filePath, $binary) === false) {
            throw new RuntimeException('Failed to save audio to storage');
        }

        return $folder.'/'.$filename;
    }
}

if (! function_exists('saveFile')) {
    /**
     * Save base64 file to storage (for any file type)
     *
     * @param  string|null  $mimeType  Optional MIME type if not in base64
     * @param  string|null  $filename  Optional filename without extension
     * @return string Public path
     */
    function saveFile(
        string $base64,
        ?string $folder = null,
        ?string $mimeType = null,
        ?string $filename = null
    ): string {
        // Get configuration with fallbacks
        $config = app('file')->config();

        $folder = $folder ?? $config['default_folder'].'/files';
        $disk = $config['disk'];

        $extension = 'bin'; // Default extension

        // Extract base64 data and detect MIME type
        if (preg_match('/^data:([\w\/]+);base64,/', $base64, $type)) {
            $mimeType = $type[1];
            $base64 = substr($base64, strpos($base64, ',') + 1);
        }

        // Determine extension from MIME type
        if ($mimeType) {
            $extension = mimeToExtension($mimeType);
        }

        $binary = base64_decode($base64);
        if ($binary === false) {
            throw new InvalidArgumentException('Invalid base64 data');
        }

        // Generate filename
        $filename = $filename ? $filename.'.'.$extension : uniqid().'.'.$extension;

        // Ensure directory exists
        $storagePath = 'app/'.$disk.'/'.$folder;
        $fullPath = storage_path($storagePath);
        if (! is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // Save file
        $filePath = $fullPath.'/'.$filename;

        if (file_put_contents($filePath, $binary) === false) {
            throw new RuntimeException('Failed to save file to storage');
        }

        return $folder.'/'.$filename;
    }
}

if (! function_exists('saveVideo')) {
    /**
     * Save base64 video to storage with large file support (up to 10GB)
     *
     * @param  string|null  $filename  Optional filename without extension
     * @param  int  $maxSizeGB  Maximum file size in GB (default: 10 for 10GB)
     * @return string Public path
     */
    function saveVideo(
        string $base64,
        ?string $folder = null,
        ?string $filename = null,
        int $maxSizeGB = 10
    ): string {
        // Set maximum execution time and memory limit
        set_time_limit(0); // No time limit
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1'); // No memory limit

        // Get configuration with fallbacks
        $config = app('video')->config();

        $folder = $folder ?? $config['default_folder'].'/videos';
        $disk = $config['disk'];

        // Extract base64 data and original format
        if (preg_match('/^data:video\/(\w+);base64,/', $base64, $type)) {
            $base64 = substr($base64, strpos($base64, ',') + 1);
            $extension = strtolower($type[1]);
        } else {
            throw new InvalidArgumentException('Invalid base64 video format');
        }

        // Calculate file size before decoding
        $fileSize = (int) (strlen($base64) * 3 / 4); // Approximate binary size
        $maxSizeBytes = $maxSizeGB * 1024 * 1024 * 1024;

        if ($fileSize > $maxSizeBytes) {
            throw new InvalidArgumentException(
                "Video file too large. Maximum allowed: {$maxSizeGB}GB"
            );
        }

        // Generate filename
        $filename = $filename ? $filename.'.'.$extension : uniqid().'.'.$extension;

        // Ensure directory exists
        $storagePath = 'app/'.$disk.'/'.$folder;
        $fullPath = storage_path($storagePath);
        if (! is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        // Save video with stream handling
        $filePath = $fullPath.'/'.$filename;

        // Always use streaming for videos to handle large files
        if (! saveLargeBase64FileStreamed($base64, $filePath)) {
            // Clean up partial file on failure
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            throw new RuntimeException('Failed to save video file. Please try again.');
        }

        return $folder.'/'.$filename;
    }
}

if (! function_exists('saveLargeBase64FileStreamed')) {
    /**
     * Save large base64 files using streaming with progress tracking
     *
     * @param  int  $chunkSize  Chunk size in bytes (default: 2MB)
     */
    function saveLargeBase64FileStreamed(
        string $base64Data,
        string $filePath,
        int $chunkSize = 2097152 // 2MB chunks
    ): bool {
        $totalLength = strlen($base64Data);
        $position = 0;

        $outputStream = fopen($filePath, 'w');
        if ($outputStream === false) {
            return false;
        }

        // Set stream buffer to 0 for direct writing
        stream_set_write_buffer($outputStream, 0);

        // Process in chunks to avoid memory issues
        while ($position < $totalLength) {
            // Prevent timeout by sending keep-alive headers in web context
            if (connection_aborted()) {
                fclose($outputStream);
                unlink($filePath);

                return false;
            }

            // Get chunk of base64 data
            $chunk = substr($base64Data, $position, $chunkSize);

            // Ensure we have complete base64 blocks (multiple of 4)
            $chunkLength = strlen($chunk);
            $remaining = $chunkLength % 4;

            if ($remaining > 0 && ($position + $chunkLength) < $totalLength) {
                // Adjust chunk to be multiple of 4 for proper base64 decoding
                $chunk = substr($base64Data, $position, $chunkLength - $remaining);
                $position += $chunkLength - $remaining;
            } else {
                $position += $chunkLength;
            }

            // Decode and write chunk
            $decodedChunk = base64_decode($chunk, true);
            if ($decodedChunk === false) {
                fclose($outputStream);
                unlink($filePath);

                return false;
            }

            if (fwrite($outputStream, $decodedChunk) === false) {
                fclose($outputStream);
                unlink($filePath);

                return false;
            }

            // Force output flush in web context to prevent timeouts
            if (php_sapi_name() !== 'cli') {
                flush();
                ob_flush();
            }

            // Free memory
            unset($chunk, $decodedChunk);
        }

        fclose($outputStream);

        return true;
    }
}

if (! function_exists('saveVideoFromChunks')) {
    /**
     * Save video from chunked uploads for very large files (10GB+)
     *
     * @param  array  $chunks  Array of base64 chunks
     * @return string Public path
     */
    function saveVideoFromChunks(
        array $chunks,
        string $filename,
        ?string $folder = null
    ): string {
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $config = app('image-cropper')->config();
        $folder = $folder ?? $config['default_folder'].'/videos';
        $disk = $config['disk'];

        // Ensure directory exists
        $storagePath = 'app/'.$disk.'/'.$folder;
        $fullPath = storage_path($storagePath);
        if (! is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        $filePath = $fullPath.'/'.$filename;
        $outputStream = fopen($filePath, 'w');

        if ($outputStream === false) {
            throw new RuntimeException('Cannot create video file');
        }

        stream_set_write_buffer($outputStream, 0);

        try {
            foreach ($chunks as $index => $chunkBase64) {
                // Check if connection is still alive
                if (connection_aborted()) {
                    throw new RuntimeException('Upload cancelled by user');
                }

                // Remove data URL prefix if present
                if (preg_match('/^data:video\/(\w+);base64,/', $chunkBase64, $type)) {
                    $chunkBase64 = substr($chunkBase64, strpos($chunkBase64, ',') + 1);
                }

                $decodedChunk = base64_decode($chunkBase64, true);
                if ($decodedChunk === false) {
                    throw new RuntimeException("Invalid base64 data in chunk {$index}");
                }

                if (fwrite($outputStream, $decodedChunk) === false) {
                    throw new RuntimeException("Failed to write chunk {$index}");
                }

                // Flush output and free memory
                if (php_sapi_name() !== 'cli') {
                    flush();
                    ob_flush();
                }

                unset($decodedChunk, $chunkBase64);

                // Optional: Add small delay to prevent server overload
                usleep(100000); // 0.1 second
            }

            fclose($outputStream);

            return $folder.'/'.$filename;

        } catch (Exception $e) {
            fclose($outputStream);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            throw $e;
        }
    }
}

if (! function_exists('checkServerLimits')) {
    /**
     * Check if server can handle large file uploads
     */
    function checkServerLimits(): array
    {
        return [
            'max_upload_size' => ini_get('upload_max_filesize'),
            'max_post_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'max_input_time' => ini_get('max_input_time'),
        ];
    }
}

if (! function_exists('increasePHPLimits')) {
    /**
     * Increase PHP limits for large file processing
     */
    function increasePHPLimits(): void
    {
        // Time limits
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        ini_set('max_input_time', 0);

        // Memory limits
        ini_set('memory_limit', '-1');

        // File upload limits
        ini_set('upload_max_filesize', '10240M'); // 10GB
        ini_set('post_max_size', '10240M'); // 10GB

        // Other limits
        ini_set('max_file_uploads', '100');
        ini_set('default_socket_timeout', '0');
    }
}

if (! function_exists('mimeToExtension')) {
    /**
     * Convert MIME type to file extension
     */
    function mimeToExtension(string $mimeType): string
    {
        $mimeMap = [
            'video/mp4' => 'mp4',
            'video/mpeg' => 'mpeg',
            'video/ogg' => 'ogv',
            'video/webm' => 'webm',
            'video/avi' => 'avi',
            'video/quicktime' => 'mov',
            'audio/mpeg' => 'mp3',
            'audio/wav' => 'wav',
            'audio/ogg' => 'oga',
            'audio/webm' => 'weba',
            'audio/aac' => 'aac',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/zip' => 'zip',
            'text/plain' => 'txt',
            'text/csv' => 'csv',
        ];

        return $mimeMap[$mimeType] ?? 'bin';
    }
}
