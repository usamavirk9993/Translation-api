<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = ['name'];

    public function translations()
    {
        return $this->belongsToMany(Translation::class);
    }
}
