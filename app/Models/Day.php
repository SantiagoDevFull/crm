<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Day extends Model
{

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }
}
