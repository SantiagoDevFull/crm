<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BackOffice extends Model
{

    use SoftDeletes;
    protected $table = 'back_offices';
    protected $dates = ['deleted_at'];
}