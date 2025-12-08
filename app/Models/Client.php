<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //


    public $timestamps = false;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'sync_status',
    ];


    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
