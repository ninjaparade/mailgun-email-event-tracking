<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'delivered_at',
        'failed_at',
    ];

    public function sender()
    {
        return $this->morphTo();
    }
}
