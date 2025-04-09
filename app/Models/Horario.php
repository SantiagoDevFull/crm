<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model
{

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function days()
    {
        return $this->hasMany(Day::class, 'horario_id');
    }
}
