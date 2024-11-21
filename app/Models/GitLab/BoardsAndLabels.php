<?php

namespace App\Models\GitLab;

use Illuminate\Database\Eloquent\Model;

class BoardsAndLabels extends Model
{
    protected $table="boards_and_labels";
    protected $fillable = [
        "id_boards",
        "id_labels"
    ];
}
