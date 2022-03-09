<?php

namespace App\Jobs\Post;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;
use Storage;
use Log;
use File;
use Notification;
use App\Notifications\NewPost;
use App\Models\User;
use App\Jobs\Post\ProcessCachePost;
use App\Services\PostService;
use MeiliSearch\Client;
use App\Jobs\Media\ProcessImage;
use App\Jobs\Media\ProcessVideo;
use App\Models\UserCount;
use App\Enum\PostType;
use App\Services\ActivityLogService;

class ProcessCreatePost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    //construct post model
    protected $post;
    protected $request;
    protected $userId;
    protected $files;
    protected $locale;
    protected $ip;
    protected $userAgent;
    protected $searchIndex = 'posts';

    //construct post model
    public function __construct($userId, $request, $locale, $files, $ip, $userAgent)
    {
        $this->userId = $userId;
        $this->request = $request;
        $this->files = $files;
        $this->locale = $locale;
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
        //
        try {
            $dataCreate = [
                'title' => $this->request['title'],
                'body' => $this->request['body'],
                'user_id' => $this->userId,
                'category_id' => $this->request['category_id'],
                'status' => $this->request['status'] ?? 1,
                'meta_title' => $this->request['meta_title'] ?? '',
                'meta_description' => $this->request['meta_description'] ?? '',
                'meta_keywords' => $this->request['meta_keywords']  ?? '',
                'featured' => $this->request['featured'] ?? 0,
                'type' => $this->request['type'] ?? 'user_post',
                'main_id' => 0

            ];

            $post = Post::create($dataCreate);
            $post->postCount()->create([]);

            if ($post) {

                $activityLogService = new ActivityLogService();
                $activityLogService->createActivityLog($post->user_id, 'create_post', $post->id, 'posts', 'success', $this->request, $this->ip, $this->userAgent);

                $userCount = UserCount::where('user_id', $this->userId)->first();
                if ($userCount) {
                    $userCount->total_posts = $userCount->total_posts + 1;
                    $userCount->total_post_uploaded = $userCount->total_post_uploaded + 1;
                    if ($this->request['status'] == 1) {
                        $userCount->total_post_published = $userCount->total_post_published + 1;
                    }

                    $userCount->save();
                }
                $postChildren = [];

                foreach ($this->files as $file) {
                    $dataCreate['body'] = '';
                    $dataCreate['main_id'] = $post->id;

                    $postChildCreate = Post::create($dataCreate);
                    $postChildCreate->postCount()->create([]);
                    $postChildren[] = $postChildCreate->id;
                }

                $i = 0;
                foreach ($this->files as $file) {
                    if (File::extension($file) == 'jpg' || File::extension($file) == 'jpeg' || File::extension($file) == 'png' || File::extension($file) == 'gif') {
                        ProcessImage::dispatch($file, $this->userId, 'post', $postChildren[$i], $this->ip, $this->userAgent);
                    } else if (File::extension($file) == 'mp4' || File::extension($file) == 'webm' || File::extension($file) == 'ogg') {
                        ProcessVideo::dispatch($file, $this->userId, 'post', $postChildren[$i], $this->ip, $this->userAgent);
                    }
                    $i++;
                }

                $postService = new PostService($post);
                $postService->cacheSinglePost();

                Log::info(User::first()->fullname . ' created a new post: ' . $post->title);
                Notification::send(User::first(), new NewPost($post));

                $this->request['locale'] = $this->locale;

                $postService->createTranslation($post->id, $this->request);

                $this->initIndexMeiliSearchEngine();
                $this->addSortAbleToSearchEngine();
                $this->addFilterAbleToSearchEngine();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

    }

    protected function initIndexMeiliSearchEngine()
    {
        $client = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        try {
            $index = $client->index($this->searchIndex)->fetchRawInfo();

        } catch (\MeiliSearch\Exceptions\ApiException $e) {
            if ($e->getCode() == 404) {
                $client->createIndex($this->searchIndex, ['primaryKey' => 'user_id']);
            }
        }



    }

    protected function addSortAbleToSearchEngine()
    {
        $client = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        $index = $client->getIndex($this->searchIndex);
        $index->updateSortableAttributes([
            'created_at'
        ]);

    }


    protected function addFilterAbleToSearchEngine()
    {
        $client = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        $index = $client->getIndex($this->searchIndex);

        $index->updateFilterableAttributes([
            'user_id',
            'status',
            'featured'
        ]);

    }
}
