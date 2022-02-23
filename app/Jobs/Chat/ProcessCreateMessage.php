<?php

namespace App\Jobs\Chat;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\MessagePosted;
use App\Models\Message;
use App\Models\User;
use File;
use Storage;
use App\Jobs\Media\ProcessImage;
use App\Helpers\Helper;

class ProcessCreateMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $dataCreate;
    private $files;
    private $ip;
    private $userAgent;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dataCreate, $files, $ip, $userAgent)
    {
        //
        $this->dataCreate = $dataCreate;
        $this->files = $files;
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
        $message = Message::create($this->dataCreate);

        foreach ($this->files as $file) {
            if ($file) {
                ProcessImage::dispatch($file, $this->dataCreate['sender_id'], 'message', $message->id, $this->ip, $this->userAgent);

            }
        }
    

        $user = User::findOrFail($message->sender_id);
        event(new MessagePosted($message, $user, $message->recipient_id));
    }
}
