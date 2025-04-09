<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleInGroup extends Model
{

    use SoftDeletes;
    protected $table = 'modules_in_groups';
    protected $dates = ['deleted_at'];
}
