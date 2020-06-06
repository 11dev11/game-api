<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Army;
class Game extends Model
{
    protected $fillable = [
        'status'
    ];
    public function armies()
    {
        return $this->hasMany('App\Army');
    }

}
