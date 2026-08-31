<?php

namespace App\Modules\Kurssit\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CourseRegistration extends Model
{
    use BelongsToCompany;

        protected $fillable = [
        'company_id',
        'course_id',
        'name',
        'email',
        'phone',
        'status',
        'payment_deadline',
    ];

    protected $casts = [
        'payment_deadline' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}