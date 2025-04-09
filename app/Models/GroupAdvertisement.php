<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupAdvertisement extends Model
{

    use SoftDeletes;
    protected $table = 'groups_advertisements';
    protected $dates = ['deleted_at'];
}