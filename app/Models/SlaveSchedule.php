<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class SlaveSchedule extends Model
{
    use HasFactory;

    protected $table = 'slave_schedule';

    protected $fillable = [
        'start',
        'end',
        'hour_start',
        'hour_end',
    ];

    private $slave_id;
    private $start;
    private $end;

    public function slave(): hasMany
    {
        return $this->hasMany(Slave::class, 'id');
    }

    public function checkInter($start, $end, $hour_start, $hour_end)
    {
        if($start < $hour_end && $hour_start < $end) {
            return true;
        } else {
            return false;
        }
    }
}
