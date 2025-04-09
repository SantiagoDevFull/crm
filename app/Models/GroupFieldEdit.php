<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupFieldEdit extends Model
{
    use SoftDeletes;
    protected $table = 'groups_fields_edit';
    protected $dates = ['deleted_at'];
}