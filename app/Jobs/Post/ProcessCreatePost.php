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
use Illuminate\Http\File;
use Notification;
use App\Notifications\NewPost;
use App\Models\User;
use App\Jobs\Post\ProcessCachePost;
use App\Services\PostService;
use MeiliSearch\Client;
use App\Jobs\Media\ProcessImage;
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
    protected $imageFileTmp;
    protected $locale;
    protected $ip;
    protected $userAgent;
    protected $searchIndex = 'posts';

    //construct post model
    public function __construct($userId, $request, $locale, $imageFileTmp, $ip, $userAgent)
    {
        $this->userId = $userId;
        $this->request = $request;
        $this->imageFileTmp = $imageFileTmp;
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
                
            ];
    
            $post = Post::create($dataCreate);

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

                ProcessImage::dispatch($this->imageFileTmp, $this->userId, 'post', $post->id, $this->ip, $this->userAgent);

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
