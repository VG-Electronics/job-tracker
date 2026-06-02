<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'url', 'offer_url_part', 'js_render'])]
class Website extends Model
{
}
