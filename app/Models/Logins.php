<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logins extends Model
{

    use SoftDeletes;
    protected $table = 'logins';
    protected $dates = ['deleted_at'];
}