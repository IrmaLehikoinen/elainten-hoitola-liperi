<?php

namespace App\Modules\Lemmikkihoitola\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class ReminderType extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'slug', 'label', 'sort_order', 'show_in_ajanvaraus_calendar'];

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'type', 'slug');
    }
}