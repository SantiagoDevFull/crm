<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubSection extends Model
{

    use SoftDeletes;
    protected $table = 'sub_sections';
    protected $dates = ['deleted_at'];
}
