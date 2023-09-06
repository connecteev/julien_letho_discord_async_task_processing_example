<?php

namespace App\Console\Commands\Videos;

use FFMpeg\Format\Video\X264;
use Illuminate\Console\Command;
use ProtoneMedia\LaravelFFMpeg\Filters\TileFactory;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class ProcessMP4VideoForHLSStreaming extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:process-mp4-video-for-hls-streaming';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert a MP4 video to HLS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo "Processing Video...\n";
        //exec(storage_path("scripts/create_video_thumbnails_sprite_and_vtt.sh"));

        $videoFileName = 'input_video_12345.mp4';
        $this->addWatermarkToVideo($videoFileName);
        $this->createThumbnailsTilesSpriteAndVTTFile($videoFileName);

        $fullPathToWatermarkedVideo = $this->getFullPathToWatermarkedVideo($videoFileName);
        $filenameWithoutExtension = substr($videoFileName, 0, strrpos($videoFileName, "."));

        $this->convertVideoToHLS($fullPathToWatermarkedVideo, $filenameWithoutExtension);
    }

    private function getFullPathToWatermarkedVideo($videoFileName): string
    {
        $filenameWithoutExtension = substr($videoFileName, 0, strrpos($videoFileName, "."));
        return $filenameWithoutExtension . "/" . $filenameWithoutExtension . "_watermarked.mp4";
    }

    // Add Watermark to Video
    private function addWatermarkToVideo($videoFileName)
    {
        echo "Adding Watermark to Video\n";
        $fullPathToWatermarkedVideo = $this->getFullPathToWatermarkedVideo($videoFileName);

        // From https://github.com/protonemedia/laravel-ffmpeg/issues/155#issuecomment-678102480
        $aspectRatio = 1.5;
        $watermarkWidth = 40;
        $watermarkHeight = round($watermarkWidth * $aspectRatio);
        Image::load(storage_path('app/images/logo/keenbrain_logo_circle_black_border.png'))
            ->fit(Manipulations::FIT_STRETCH, $watermarkWidth, $watermarkHeight)
//            ->width(25)
//            ->height(250)
            ->save(storage_path('app/images/logo/tmp_updated_logo.png'));

        //$media = FFMpeg::fromDisk('videos')->open($videoFileName);
        $media = FFMpeg::open($videoFileName);
        $media
            ->addWatermark(function(WatermarkFactory $watermark) use ($watermarkWidth, $watermarkHeight) {
                $watermark
                    //->fromDisk('local')
                    ->open('images/logo/tmp_updated_logo.png')
                    //->horizontalAlignment(WatermarkFactory::RIGHT, 70)
                    //->verticalAlignment(WatermarkFactory::BOTTOM, 80)
                    ->right(20)
                    ->bottom(20)
//                    ->width($watermarkWidth)
//                    ->height($watermarkHeight)
//                    ->width(50)
//                    ->height(89)
//                    ->greyscale()
                ;
            })
            ->export()
            /*
            ->onProgress(function ($percentage, $remaining, $rate) {
                echo "{$percentage}% transcoded. {$remaining} seconds left at rate: {$rate}\n";
            })
            */
            ->onProgress(function ($percentage){
                echo "{$percentage}% transcoded\n";
            })
            ->inFormat(new \FFMpeg\Format\Video\X264('libmp3lame','libx264')) // tell the MediaExporter to which disk and in which format we want to export...
            ->save($fullPathToWatermarkedVideo)
        ;
    }

    private function createThumbnailsTilesSpriteAndVTTFile($videoFileName)
    {
        echo "Creating video thumbnail tiles sprite and thumbnails.vtt file\n";
        $filenameWithoutExtension = substr($videoFileName, 0, strrpos($videoFileName, "."));

        //$media = FFMpeg::fromDisk('videos')->open($videoFileName);
        $media = FFMpeg::open($videoFileName);
        $durationInSeconds = $media->getDurationInSeconds(); // returns an int

        // Create Tile of Thumbnails, and Thumbnails.vtt file, which shows video stills / previews as you scrub across the timeline
        $tileExportIntervalInSeconds = 1.0; // float
        $numTiles = $durationInSeconds / $tileExportIntervalInSeconds;
        $gridNumColumns = 6;
        $gridNumRows = ceil($numTiles / $gridNumColumns);
        $media
            ->exportTile(function (TileFactory $factory) use ($tileExportIntervalInSeconds, $gridNumColumns, $gridNumRows, $filenameWithoutExtension) {
                $factory->interval($tileExportIntervalInSeconds)
                    ->scale(320, 180)
                    ->grid($gridNumColumns, $gridNumRows)
                    ->generateVTT($filenameWithoutExtension . '/thumbnails.vtt')
                ;
            })
            ->onProgress(function ($percentage){
                echo "{$percentage}% thumbnails processed\n";
            })
            ->save($filenameWithoutExtension . '/video_thumbnails/tile_%05d.jpg')
        ;
    }

    private function convertVideoToHLS($fullPathToWatermarkedVideo, $filenameWithoutExtension)
    {
        echo "Converting Watermarked MP4 video to HLS\n";

//        $lowBitrate = (new X264)->setKiloBitrate(250);
//        $midBitrate = (new X264)->setKiloBitrate(500);
//        $highBitrate = (new X264)->setKiloBitrate(1000);
        $superBitrate = (new X264)->setKiloBitrate(2000);

        FFMpeg::open($fullPathToWatermarkedVideo)
            ->exportForHLS()
            ->setSegmentLength(10) // optional
            ->setKeyFrameInterval(48) // optional

//            ->addFormat($lowBitrate)
//            ->addFormat($midBitrate)
//            ->addFormat($highBitrate)
//            ->addFormat($superBitrate)
//            ->addFormat($lowBitrate, function($media) {
//                $media->addFilter('scale=640:480');
//            })
//            ->addFormat($midBitrate, function($media) {
//                $media->scale(960, 720);
//            })
//            ->addFormat($highBitrate, function ($media) {
//                $media->addFilter(function ($filters, $in, $out) {
//                    $filters->custom($in, 'scale=1920:1200', $out); // $in, $parameters, $out
//                });
//            })
//            ->addFormat($superBitrate, function($media) {
//                $media->addLegacyFilter(function ($filters) {
//                    $filters->resize(new \FFMpeg\Coordinate\Dimension(2560, 1920));
//                });
//            })
            ->addFormat($superBitrate)
            ->onProgress(function ($percentage){
                echo "{$percentage}% video converted to HLS\n";
            })
            ->save($filenameWithoutExtension . '/hls_converted_files/hls_' . $filenameWithoutExtension . '.m3u8');
    }
}
