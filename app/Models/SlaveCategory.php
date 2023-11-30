<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class SlaveCategory extends Model
{
    use HasFactory;

    protected $table = 'slave_category';

    protected $fillable = [
        'name',
        'description',
        'parent'
    ];

    private $name;
    private $description;
    private $parent;

    public function slave(): BelongsTo
    {
        return $this->belongsTo(Slave::class);
    }
}
