<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['company_id', 'name', 'description', 'price', 'pricing_type', 'is_shared'];
}
