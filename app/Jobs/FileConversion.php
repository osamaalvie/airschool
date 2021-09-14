<?php

namespace App\Jobs;

use App\User;
use App\Video;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use SebastianBergmann\CodeCoverage\Report\PHP;


class FileConversion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $video;


    /**
     * FileConversion constructor.
     * @param Video $video
     * @param User $user
     */
    public function __construct($video, $user)
    {
        $this->user = $user;
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $path = 'uploads/' . $this->user->id . '/' . $this->video->url;
            //Log::error($path);
            $m3u8 = 'uploads/' . $this->user->id . '/' . $this->video->name . '.m3u8';
            $thumb = 'uploads/' . $this->user->id . '/' . $this->video->name . '.jpg';
            $highBitrateFormat = (new X264('aac'))->setKiloBitrate(3000);

            FFMpeg::fromDisk('public')
                ->open($path)
                ->exportForHLS()
                ->toDisk('public')
                ->addFormat($highBitrateFormat)
                ->save($m3u8);

            FFMpeg::fromDisk('public')
                ->open($path)
                ->getFrameFromSeconds(1)
                ->export()
                ->toDisk('public')
                ->save($thumb);

            $this->video->update([
                'status' => 'converted',
                'url' => $this->video->name . '.m3u8',
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }


}
