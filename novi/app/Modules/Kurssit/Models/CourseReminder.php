<?php

namespace App\Modules\Kurssit\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CourseReminder extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'course_id',
        'title',
        'due_at',
        'done_at',
        'created_by',
        'done_by',
    ];

    protected $casts = [
        'due_at' => 'date',
        'done_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function doneBy()
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    public function isDone(): bool
    {
        return $this->done_at !== null;
    }

    public function scopeOpen($query)
    {
        return $query->whereNull('done_at');
    }
}