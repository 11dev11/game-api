<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Army extends Model
{
    protected $fillable = [
        'name', 'number_of_units', 'attack_strategy', 'property_of', 'starting_values'
    ];
    public function game()
    {
        return $this->belongsTo('App\Game');
    }
    public static function revive(array $data)
    {
        return Army::update();
    }

}
