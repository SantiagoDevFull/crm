<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupFieldView extends Model
{
    use SoftDeletes;
    protected $table = 'groups_fields_view';
    protected $dates = ['deleted_at'];
}