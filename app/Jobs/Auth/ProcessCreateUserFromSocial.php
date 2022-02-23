<?php

namespace App\Jobs\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Storage;
use App\Jobs\Media\ProcessImage;
use Image;
use Str;
class ProcessCreateUserFromSocial implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $urlAvatar;
    protected $userId;
    protected $ip;
    protected $userAgent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($urlAvatar, $userId, $ip, $userAgent)
    {
        $this->urlAvatar = $urlAvatar;
        $this->userId = $userId;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contents = file_get_contents($this->urlAvatar);
        $name = Str::random(16) . '.jpg';
        $tmpFile = Storage::disk('local')->put('tmp/images/' . $name, $contents);
        if ($tmpFile) {
            $fileName = 'tmp/images/' . $name;
            ProcessImage::dispatch($fileName, $this->userId, 'user', $this->userId, $this->ip, $this->userAgent);
        }



    }
}
