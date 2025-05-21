<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StateGroup extends Model
{
    use SoftDeletes;
    protected $table = 'state_groups';
    protected $dates = ['deleted_at'];
}