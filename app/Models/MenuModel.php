<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuModel extends Model
{

    protected $table = 'menu';

    protected $fillable = [
        "name",
        "route_name",
        "icon",
        "child",
        "is_general"
    ];

}
