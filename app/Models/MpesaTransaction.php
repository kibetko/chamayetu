<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaTransaction extends Model
{
    protected $guarded = [];

    public function contribution()
    {
        return $this->hasOne(
            Contribution::class
        );
    }

    public function loan()
    {
        return $this->belongsTo(
            Loan::class
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function group()
    {
        return $this->belongsTo(
            Group::class
        );
    }
}