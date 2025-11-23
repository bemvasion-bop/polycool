<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',

        'given_name',
        'middle_name',
        'last_name',
        'phone_number',
        'gender',
        'date_of_birth',
        'date_hired',
        'street_address',
        'city',
        'province',
        'postal_code',
        'position_title',
        'employee_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth'     => 'date',
        'date_hired'        => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        if ($this->given_name || $this->last_name) {
            return trim(
                $this->given_name.' '.
                ($this->middle_name ? $this->middle_name.' ' : '').
                $this->last_name
            );
        }

        return $this->name ?? '';
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
