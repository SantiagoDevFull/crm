<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logouts extends Model
{

    use SoftDeletes;
    protected $table = 'logouts';
    protected $dates = ['deleted_at'];
}