<?php

namespace App\Models\GitLab;

use Illuminate\Database\Eloquent\Model;

class Labels extends Model
{
    protected $table="labels";

    protected $fillable = [
        "id",
        "name",
        "description",
        "description_html",
        "text_color",
        "color",
        "subscribed",
        "priority",
        "is_project_label",
    ];
}
