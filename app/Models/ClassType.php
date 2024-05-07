<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'class_type_name',
    ];

    public function records()
    {
        return $this->belongsToMany(Record::class, 'class_type_records', 'class_type_id', 'record_id');
    }
}
