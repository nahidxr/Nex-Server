<?php
namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Content;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;
use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\Log;




class FileUploadController extends Controller
{
    public function index() {
        return view('backend.pages.upload-file.index');
    }

    // public function uploadLarge(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:mp4', // Limit file size to 20MB
    //     ]);

    //     $file = $request->file('file');
    //     $path = $file->store('videos', 'public'); // Store the file in the 'public/videos' directory

    //     return response()->json(['path' => Storage::url($path)]);
    //     }

    //     // public function uploadLarge(Request $request)
    //     // {
    //     //     $request->validate([
    //     //         'file' => 'required|file|mimes:mp4|max:20480', // Limit file size to 20MB
    //     //     ]);

    //     //     $file = $request->file('file');
    //     //     $filePath = $file->store('videos', 'public'); // Store the file in the 'public/videos' directory
    //     //     $fullPath = Storage::path($filePath); // Get the full path for generating video details

    //     //     // Generate video details
    //     //     $videoDetails = $this->generateVideoDetails($fullPath);
            
    //     //     // Decode the JSON response from generateVideoDetails
    //     //     // $videoDetailsArray = json_decode($videoDetails, true);
    //     //     $staticVideoDetails = [
    //     //         'duration' => '00:00:30', // Example duration
    //     //         'resolution' => '1920x1080', // Example resolution
    //     //         'bitrate' => '4500kbps', // Example bitrate
    //     //         'codec' => 'H.264', // Example codec
    //     //         'fps' => 30, // Frames per second
    //     //         // Add other static fields as needed
    //     //     ];
    //     //     // Create a new Content record
    //     //     try {
    //     //         Content::create([
    //     //             'file_name' => $file->getClientOriginalName(), // Original file name
    //     //             'file_path' => $filePath, // Path stored in the filesystem
    //     //             'folder' => 'videos', // Specify the folder or use as needed
    //     //             'file_id' => null, // You can assign a value or logic here if needed
    //     //             'media_details' => $staticVideoDetails, // Store video details as JSON
    //     //         ]);
    //     //     } catch (\Exception $e) {
    //     //         Log::error('Error saving video details: ' . $e->getMessage());
    //     //         return response()->json(['error' => 'Failed to save video details.'], 500);
    //     //     }

    //     //     return response()->json(['path' => Storage::url($filePath)]);
    //     // }
    //     public function generateVideoDetails($filePath)
    //     {
    //     try {
    //         // Ensure the path is constructed correctly
    //         $fullPath = $filePath;
    
    //         $ffmpeg = FFMpeg::create();
    //         $video = $ffmpeg->open($fullPath);
    
    //         // Get video streams
    //         $streams = $video->getStreams();
    
    //         // Get the first video stream
    //         $videoStream = $streams->videos()->first();
    
    //         // Get video dimensions
    //         $videoDimensions = $videoStream->getDimensions();
    //         $width = $videoDimensions->getWidth();
    //         $height = $videoDimensions->getHeight();
    
    //         // File size (in bytes)
    //         $fileSize = filesize($fullPath);
    
    //         // Duration (in seconds)
    //         $duration = $video->getFormat()->get('duration');
    
    //         // Aspect ratio
    //         $aspectRatio = $this->calculateAspectRatio($width, $height);
    
    //         // Video codec
    //         $videoCodec = $videoStream->get('codec_name');
    
    //         // Video bitrate (in bits per second)
    //         $videoBitrate = $videoStream->get('bit_rate');
    
    //         // Frame rate (frames per second)
    //         $frameRate = $videoStream->get('r_frame_rate');
    
    //         // Get the first audio stream
    //         $audioStream = $streams->audios()->first();
    
    //         // Audio codec
    //         $audioCodec = $audioStream ? $audioStream->get('codec_name') : 'No audio stream';
    //         // Audio bitrate (in bits per second)
    //         $audioBitrate = $audioStream ? $audioStream->get('bit_rate') : 'No audio stream';
    
    //         // Sample rate (in Hz)
    //         $sampleRate = $audioStream ? $audioStream->get('sample_rate') : 'No audio stream';
    
    //         // Structure the details
    //         $fileDetails = [
    //             'width' => (string)$width,
    //             'height' => (string)$height,
    //             'file_size' => (string)$fileSize,
    //             'duration' => number_format((float)$duration, 2, '.', ''),
    //             'aspect_ratio' => $aspectRatio,
    //             'video_codec' => strtoupper($videoCodec), // Capitalize the codec name
    //             'video_bitrate' => (string)round($videoBitrate / 1000), // Convert to Kbps
    //             'frame_rate' => (string)$frameRate,
    //             'audio_codec' => strtoupper($audioCodec), // Capitalize the codec name
    //             'audio_bitrate' => (string)round($audioBitrate / 1000), // Convert to Kbps
    //             'sample_rate' => (string)$sampleRate,
    //             'all_details' => [
    //                 'width' => [
    //                     'title' => 'Width',
    //                     'value' => (string)$width,
    //                     'unit' => 'Pixel',
    //                     'display' => "{$width} Pixel",
    //                 ],
    //                 'height' => [
    //                     'title' => 'Height',
    //                     'value' => (string)$height,
    //                     'unit' => 'Pixel',
    //                     'display' => "{$height} Pixel",
    //                 ],
    //                 'dimensions' => [
    //                     'title' => 'Dimensions',
    //                     'value' => null,
    //                     'unit' => 'Pixel',
    //                     'display' => "{$width}x{$height}",
    //                 ],
    //                 'file_size' => [
    //                     'title' => 'File Size',
    //                     'value' => (string)$fileSize,
    //                     'unit' => 'MB',
    //                     'display' => number_format($fileSize / 1048576, 1) . " MB", // Convert bytes to MB
    //                 ],
    //                 'duration' => [
    //                     'title' => 'Duration',
    //                     'value' => number_format((float)$duration, 1), // Format duration to 1 decimal place
    //                     'unit' => 'sec',
    //                     'display' => number_format((float)$duration, 1) . " sec",
    //                 ],
    //                 'aspect_ratio' => [
    //                     'title' => 'Aspect Ratio',
    //                     'value' => $aspectRatio,
    //                     'unit' => '',
    //                     'display' => $aspectRatio,
    //                 ],
    //                 'video_codec' => [
    //                     'title' => 'Video Codec',
    //                     'value' => strtoupper($videoCodec),
    //                     'unit' => '',
    //                     'display' => strtoupper($videoCodec),
    //                 ],
    //                 'video_bitrate' => [
    //                     'title' => 'Video Bitrate',
    //                     'value' => (string)round($videoBitrate / 1000), // Convert bps to Kbps
    //                     'unit' => 'Kbps',
    //                     'display' => number_format($videoBitrate / 1000) . " Kbps",
    //                 ],
    //                 'frame_rate' => [
    //                     'title' => 'Frame Rate',
    //                     'value' => (string)$frameRate,
    //                     'unit' => 'fps',
    //                     'display' => (string)$frameRate . " fps",
    //                 ],
    //                 'audio_codec' => [
    //                     'title' => 'Audio Codec',
    //                     'value' => strtoupper($audioCodec),
    //                     'unit' => '',
    //                     'display' => strtoupper($audioCodec),
    //                 ],
    //                 'audio_bitrate' => [
    //                     'title' => 'Audio Bitrate',
    //                     'value' => (string)round($audioBitrate / 1000),
    //                     'unit' => 'Kbps',
    //                     'display' => number_format($audioBitrate / 1000) . " Kbps",
    //                 ],
    //                 'sample_rate' => [
    //                     'title' => 'Sample Rate',
    //                     'value' => (string)number_format($sampleRate / 1000, 1), // Convert Hz to kHz
    //                     'unit' => 'kHz',
    //                     'display' => number_format($sampleRate / 1000, 1) . " kHz",
    //                 ],
    //             ],
    //         ];
    
    //         // Return as JSON string, ensuring it's not double-encoded
    //         return json_encode($fileDetails);
    //     } catch (\Exception $e) {
    //         Log::error('Error generating video details: ' . $e->getMessage());
    //         return null;
    //     }
    // }
    
    
    // Define calculateAspectRatio as a method
    private function calculateAspectRatio($width, $height)
    {
        // Find the greatest common divisor of width and height
        $gcd = $this->gcd($width, $height);

        // Divide both width and height by their GCD to get the ratio
        $aspectRatioWidth = $width / $gcd;
        $aspectRatioHeight = $height / $gcd;

        // Return the aspect ratio as a string
        return $aspectRatioWidth . ':' . $aspectRatioHeight;
    }

    // Define gcd as a method
    private function gcd($a, $b)
    {
        // Euclidean algorithm to find the greatest common divisor
        return ($b == 0) ? $a : $this->gcd($b, $a % $b);
    }

    public function store(Request $request)
    {
        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            return $this->saveFile($save->getFile());
        }

        // we are in chunk mode, lets send the current progress
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    /**
     * Saves the file to S3 server
     *
     * @param UploadedFile $file
     *
     * @return JsonResponse
     */
    protected function saveFileToS3($file)
    {
        $fileName = $this->createFilename($file);
        $disk = Storage::disk('s3');

        // It's better to use streaming (laravel 5.4+)
        $disk->putFileAs('photos', $file, $fileName);

        // for older laravel
        // $disk->put($fileName, file_get_contents($file), 'public');

        $mime = str_replace('/', '-', $file->getMimeType());

        // We need to delete the file when uploaded to s3
        unlink($file->getPathname());

        return response()->json([
            'path' => $disk->url($fileName),
            'name' => $fileName,
            'mime_type' => $mime
        ]);
    }

    /**
     * Saves the file
     *
     * @param UploadedFile $file
     *
     * @return JsonResponse
     */
    protected function saveFile(UploadedFile $file)
    {
        $fileName = $this->createFilename($file);

        // Group files by mime type
        $mime = str_replace('/', '-', $file->getMimeType());

        // Group files by the date (week
        $dateFolder = date("Y-m-W");

        // Build the file path
        $filePath = "upload/{$mime}/{$dateFolder}";
        $finalPath = storage_path("app/public/" . $filePath);

        // move the file name
        $file->move($finalPath, $fileName);

        return response()->json([
            'path' => asset('storage/public/' . $filePath),
            'name' => $fileName,
            'mime_type' => $mime
        ]);
    }

    /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */
    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace("." . $extension, "", $file->getClientOriginalName()); // Filename without extension

        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;

        return $filename;
    }




}
