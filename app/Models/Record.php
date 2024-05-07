<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'course',
        'group_id',
        'discipline_id',
    ];

    public function groups()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public function disciplines()
    {
        return $this->belongsTo(Disciplines::class, 'discipline_id', 'id');
    }

    public function classTypes()
    {
        return $this->belongsToMany(ClassType::class, 'class_type_records', 'record_id', 'class_type_id');
    }
}
