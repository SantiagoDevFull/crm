<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupInBack extends Model
{

    use SoftDeletes;
    protected $table = 'sups_in_backs';
    protected $dates = ['deleted_at'];
}