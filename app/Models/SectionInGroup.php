<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionInGroup extends Model
{

    use SoftDeletes;
    protected $table = 'sections_in_groups';
    protected $dates = ['deleted_at'];
}
