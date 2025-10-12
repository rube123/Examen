<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    // --- Relaciones / Helpers ---

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
        // Si prefieres FQCN:
        // return $this->belongsTo(\App\Models\Role::class);
    }

    public function isRole(string $name): bool
    {
        return optional($this->role)->name === $name;
    }

    public function customer()
{
    return $this->belongsToMany(
        Customer::class,
        'customer_user',
        'user_id',
        'customer_id'
    )->withPivot([]);
}

}
