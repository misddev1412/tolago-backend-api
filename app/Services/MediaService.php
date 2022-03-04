<?php

namespace App\Services;
use Cache;
use App\Models\Post;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Image;
use App\Models\Image as ImageModel;
use App\Models\Video as VideoModel;
use Auth;
use Storage;
use Illuminate\Http\File;
use App\Models\ImagePost;
use App\Models\ImageMessage;
use App\Models\ImageUser;
use App\Models\ImageHotel;
use App\Models\ImageRoom;
use App\Models\ImageUtility;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Video;
use App\Models\Utility;

class MediaService
{
    public static function commonImage($imageFile, $userId, $table, $objectId, $mainImage = true): ImageModel
    {
        $imagePath  = storage_path() . '//app/' . $imageFile;
        $extension = pathinfo($imageFile, PATHINFO_EXTENSION);
        
        $directory = storage_path() . '//app/public/images/' . $userId . '/';

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $fileName = md5(uniqid(rand(), true));

        $default    = Image::make($imagePath);
        $default->resize(null, 200, function ($constraint) {
            $constraint->aspectRatio();
        });
        $default->encode('jpg', 75);
        $default->save($directory . $fileName . '.' . $extension);

        $thumbnail = Image::make($imagePath);
        $thumbnail->resize(null, 100, function ($constraint) {
            $constraint->aspectRatio();
        });
        $thumbnail->encode('jpg', 75);
        $thumbnail->save($directory . $fileName . '_thumb' . '.' . $extension);

        $medium = Image::make($imagePath);
        $medium->resize(null, 300, function ($constraint) {
            $constraint->aspectRatio();
        });
        $medium->encode('jpg', 75);
        $medium->save($directory . $fileName . '_medium' . '.' . $extension);;

        $large = Image::make($imagePath);
        $large->resize(null, 600, function ($constraint) {
            $constraint->aspectRatio();
        });
        $large->encode('jpg', 75);
        $large->save($directory . $fileName . '_large' . '.' . $extension);;

        $original = Image::make($imagePath);
        $original->encode('jpg', 75);
        $original->save($directory .  $fileName . '_original' . '.' . $extension);;
        

        $imageToStorage = [
            'default' =>  $fileName . '.' . $extension,
            'thumbnail' => $fileName . '_thumb' . '.' . $extension,
            'medium' => $fileName . '_medium' . '.' . $extension,
            'large' => $fileName . '_large' . '.' . $extension,
            'original' => $fileName . '_original' . '.' . $extension,
        ];

        $fileNames = [];
        foreach ($imageToStorage as $key => $value) {
            // \Log::info($value);
            $file = Storage::disk('Wasabi')->putFile(env('UPLOAD_IMAGE_PATH') . '/' . $userId, new File($directory . $value), $value);     
            if ($file) {
                $fileNames[$key] = $file;
                $delete = Storage::disk('local')->delete('public/images/' . $userId . '/' . $value);
            }
        }

        $fileNames['user_id'] = $userId;
        $image = ImageModel::create($fileNames);
        if ($image) {
            switch ($table) {
                case 'post':
                    ImagePost::create([
                        'image_id' => $image->id,
                        'post_id' => $objectId,
                    ]);
                    Post::where('id', $objectId)->update(['image_id' => $image->id]);
                    break;
                case 'message':
                    ImageMessage::create([
                        'image_id' => $image->id,
                        'message_id' => $objectId,
                    ]);
                    break;
                case 'user':
                    ImageUser::create([
                        'image_id' => $image->id,
                        'user_id' => $objectId,
                    ]);
                    User::where('id', $objectId)->update(['image_id' => $image->id]);
                    break;
                case 'hotel':
                    ImageHotel::create([
                        'image_id' => $image->id,
                        'hotel_id' => $objectId,
                    ]);
                    if ($mainImage) {
                        Hotel::where('id', $objectId)->update(['image_id' => $image->id]);
                    }
                    break;
                case 'room':
                    ImageRoom::create([
                        'image_id' => $image->id,
                        'room_id' => $objectId,
                    ]);
                    if ($mainImage) {
                        Room::where('id', $objectId)->update(['image_id' => $image->id]);
                    }
                    break;
                case 'utility':
                    ImageUtility::create([
                        'image_id' => $image->id,
                        'utility_id' => $objectId,
                    ]);
                    if ($mainImage) {
                        Utility::where('id', $objectId)->update(['image_id' => $image->id]);
                    }
                    break;
            }   
        }
        return $image;
    }

    public static function commonVideo($videoFile, $userId, $table, $objectId, $mainVideo = true): VideoModel
    {
        $videoPath  = storage_path() . '//app/' . $videoFile;
        $extension = pathinfo($videoFile, PATHINFO_EXTENSION);
        
        $directory = storage_path() . '//app/public/videos/' . $userId . '/';

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $file = Storage::disk('Wasabi')->putFile(env('UPLOAD_VIDEO_PATH') . '/' . $userId, new File($videoPath), $videoPath); 
        if ($file) {
            $delete = Storage::disk('local')->delete('public/videos/' . $userId . '/' . $videoFile);
        
        }   
        $video = Video::create([
            'original_url' => $file,
            'user_id' => $userId,
        ]); 

        if ($video) { 
            switch ($table) {
                case 'post':
                    
                    Post::where('id', $objectId)->update(['video_id' => $video->id]);
                    break;
            }
        }

        return $video;
    }

}