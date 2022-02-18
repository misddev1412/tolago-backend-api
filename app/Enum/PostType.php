<?php
namespace App\Enum;

use App\Enum\Enum;

final class PostType extends Enum {
    const USER_POST = 'user_post';
    const SYSTEM_POST = 'system_post';
}