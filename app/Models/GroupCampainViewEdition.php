<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupCampainViewEdition extends Model
{

    use SoftDeletes;
    protected $table = 'groups_campains_view_edition';
    protected $dates = ['deleted_at'];
}