<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StateState extends Model
{
    use SoftDeletes;
    protected $table = 'states_states';
    protected $dates = ['deleted_at'];
}