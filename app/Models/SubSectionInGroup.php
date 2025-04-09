<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubSectionInGroup extends Model
{

    use SoftDeletes;
    protected $table = 'sub_sections_in_groups';
    protected $dates = ['deleted_at'];
}
