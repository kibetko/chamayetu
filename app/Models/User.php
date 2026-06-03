<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function groups(): BelongsToMany
{
    return $this->belongsToMany(
        Group::class,
        'group_members'
    )
    ->withPivot('role', 'status', 'joined_at')
    ->withTimestamps();
}
public function joinRequests()
{
    return $this->hasMany(GroupJoinRequest::class);
}

public function invitationsSent()
{
    return $this->hasMany(
        GroupInvitation::class,
        'invited_by'
    );
}
public function notifications()
{
    return $this->hasMany(Notification::class);
}
}
