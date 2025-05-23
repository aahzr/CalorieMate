<?php
// app/Models/FoodLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'date', 'meal_type', 'food_name', 'calories', 'carbohydrate', 'protein', 'fat'];
    protected $casts = ['date' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}