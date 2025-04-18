<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{

    use SoftDeletes;
    protected $table = 'modules';
    protected $dates = ['deleted_at'];
}
