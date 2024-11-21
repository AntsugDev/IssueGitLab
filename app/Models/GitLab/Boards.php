<?php

namespace App\Models\GitLab;

use Illuminate\Database\Eloquent\Model;

class Boards extends Model
{
    protected $table="boards";

    protected $fillable =[
        "id",
        "name",
        "hide_backlog_list",
        "hide_closed_list"  ,
        "lists"
    ];

    public function labels(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Labels::class,
            BoardsAndLabels::class,
            'id_boards',
            'id',
        );
    }
}
