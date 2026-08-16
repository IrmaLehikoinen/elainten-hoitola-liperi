<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'industry', 'email', 'phone', 'settings', 'slug', 'primary_color', 'secondary_color', 'font_family', 'logo_path'];

    protected $casts = [
        'settings' => 'array',
    ];
}