<?php
// app/Models/Journal.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'content',
        'mood',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}