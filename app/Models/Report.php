<?php
// app/Models/Report.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'calorie_summary',
        'weight_change',
        'favorite_foods',
    ];

    protected $casts = [
        'calorie_summary' => 'array',
        'favorite_foods' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}