<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RangeDate extends Model
{

    use SoftDeletes;
    protected $table = 'range_dates';
    protected $dates = ['deleted_at'];
}
