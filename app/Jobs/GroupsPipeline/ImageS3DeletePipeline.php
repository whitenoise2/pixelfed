<?php

namespace App\Jobs\GroupsPipeline;

use App\Models\GroupMedia;
use App\Util\Media\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Storage;
use Illuminate\Http\File;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Aws\S3\Exception\S3Exception;
use GuzzleHttp\Exception\ConnectException;
use League\Flysystem\UnableToWriteFile;

class ImageS3DeletePipeline implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $media;
    static $attempts = 1;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(GroupMedia $media)
    {
        $this->media = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $media = $this->media;

        if(!$media || (bool) config_cache('pixelfed.cloud_storage') === false) {
            return;
        }

        $fs = Storage::disk(config('filesystems.cloud'));

        if(!$fs) {
            return;
        }

        if($fs->exists($media->media_path)) {
            $fs->delete($media->media_path);
        }
    }
}
