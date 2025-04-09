<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sup extends Model
{

    use SoftDeletes;
    protected $table = 'sups';
    protected $dates = ['deleted_at'];
}