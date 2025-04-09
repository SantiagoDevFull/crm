<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupCampainUploadMassiveSold extends Model
{

    use SoftDeletes;
    protected $table = 'groups_campains_upload_massive_sold';
    protected $dates = ['deleted_at'];
}