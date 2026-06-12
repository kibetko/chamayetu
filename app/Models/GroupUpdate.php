<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupUpdate extends Model
{
    protected $guarded = [];

    public function group()
    {
        return $this->belongsTo(
            Group::class
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }
}
