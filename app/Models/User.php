<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
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
        'superuser',
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
            'superuser' => 'integer',
        ];
    }

    /**
     * Verifica si el usuario tiene un grupo Aimeos específico (ej. 'editor').
     * La relación es: users.email == mshop_customer.code
     *   -> mshop_customer_list (domain=group) -> mshop_group.code
     */
    public function hasAimeosGroup(string $groupCode): bool
    {
        return DB::table('mshop_customer as mc')
            ->join('mshop_customer_list as ml', 'ml.parentid', '=', 'mc.id')
            ->join('mshop_group as g', 'g.id', '=', 'ml.refid')
            ->where('mc.code', $this->email)
            ->where('ml.domain', 'group')
            ->where('g.code', $groupCode)
            ->exists();
    }
}
