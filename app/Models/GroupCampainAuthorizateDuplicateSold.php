<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupCampainAuthorizateDuplicateSold extends Model
{

    use SoftDeletes;
    protected $table = 'groups_campains_authorizate_duplicate_solds';
    protected $dates = ['deleted_at'];
}