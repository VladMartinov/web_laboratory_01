<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disciplines extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'discipline_name',
    ];

    public function records()
    {
        return $this->belongsToMany(Record::class, 'records', 'discipline_id', 'id');
    }
}
