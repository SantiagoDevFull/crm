<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupFieldHaveComment extends Model
{
    use SoftDeletes;
    protected $table = 'groups_fields_have_comment';
    protected $dates = ['deleted_at'];
}