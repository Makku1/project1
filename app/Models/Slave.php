<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slave extends Model
{
    use HasFactory;

    protected $table = 'slaves';

    protected $fillable = [
        'name',
        'gender',
        'age',
        'weight',
        'skin',
        'origin',
        'description',
        'hour_price',
        'price',
        'category_id',
    ];

    private $name;
    private $gender;
    private $age;
    private $weight;
    private $skin;
    private $origin;
    private $description;
    private $hour_price;
    private $price;
    private $category_id;

    public function category(): HasMany
    {
        return $this->hasMany(SlaveCategory::class, 'id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(SlaveSchedule::class);
    }

}
