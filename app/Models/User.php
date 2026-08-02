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
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'phone_no', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory,HasApiTokens, Notifiable;

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
    'last_seen_at' => 'datetime',
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

}
