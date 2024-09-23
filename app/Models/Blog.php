<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        "deleted_by",
        "isDeleted",
        "photo"
    ];

    public function users()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function deletedBy(){
        return $this->belongsTo(User::class,'deleted_by');
    }
}
