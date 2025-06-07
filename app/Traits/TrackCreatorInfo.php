<?php 

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait TrackCreatorInfo
{
    public static function bootTrackCreatorInfo()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by_id = Auth::id();
            }

            $model->created_by_ip = Request::ip();
            $model->created_by_agent = Request::header('User-Agent');
        });
    }
}
