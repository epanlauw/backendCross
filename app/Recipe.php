<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = 'recipe';

    protected $fillable = [
        'id_type', 'name', 'difficulty', 'image_url', 'steps', 'id_user', 'ingredient'
    ];
}
