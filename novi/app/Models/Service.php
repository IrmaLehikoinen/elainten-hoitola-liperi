<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'name', 'description', 'price', 'pricing_type', 'is_shared'];
}