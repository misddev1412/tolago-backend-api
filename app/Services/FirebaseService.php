<?php

namespace App\Services;

use Exception;
use Kreait\Laravel\Firebase\Facades\Firebase;


class FirebaseService
{
    //constructor firebase service
    public function __construct()
    {
        try {
            // $this->firebase = app('firebase');
            $this->database = app('firebase.database');


        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    //realtime database connection firebase
    public function database()
    {
        $postData = [
            'sadasdasd' => 'sadasdasd',
            'sadasdasd' => 'sadasdasd',
            'identifier' => 'sadasdasd',
        ];
        // dd('sss');
        $this->database->getReference('posts')->push($postData);
        dd('s');

        dd($this->database->getReference('todos/tasks')->getSnapshot());
        dd($this->database->getReference('todos/tasks')->set('New Task Name 2'));
        return $this->database;
    }
}
