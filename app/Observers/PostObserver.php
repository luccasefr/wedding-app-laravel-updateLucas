<?php

namespace App\Observers;

use App\Post;
use App\Guest;
use App\User;

class PostObserver
{
    public function creating(Post $post)
    {
        $guest = Guest::find($post->guest_id);
        $user = User::find($guest->user_id);
        if($user->publications_should_be_aproved==1){
            $post->aproved=false;
        }else {
            $post->aproved=true;
        }
    }
}
