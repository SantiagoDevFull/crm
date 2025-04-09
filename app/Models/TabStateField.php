<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TabStateField extends Model
{
    use SoftDeletes;
    protected $table = 'tab_states_fields';
    protected $dates = ['deleted_at'];
}