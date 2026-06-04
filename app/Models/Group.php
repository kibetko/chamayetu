<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unique_code',
        'description',
        'created_by',
        'active'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(
            User::class,
            'group_members'
        )
        ->withPivot('role', 'status', 'joined_at')
        ->withTimestamps();
    }

    public function settings()
    {
        return $this->hasOne(GroupSetting::class);
    }

    public function joinRequests()
    {
        return $this->hasMany(GroupJoinRequest::class);
    }

    public function invitations()
    {
        return $this->hasMany(GroupInvitation::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    
}