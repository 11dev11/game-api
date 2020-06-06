<?php

namespace App\Http\Controllers;

use App\Console;
use App\Jobs\ReviveArmies;
use Illuminate\Http\Request;
use App\Game;
use App\Army;
use App\Http\Resources\Game as GameResource;
use Illuminate\Support\Facades\Log;
class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //creating Game
        $game = Game::create();
        $game_id = $game->id;

        //creating path to log file and log file by ID --every game has its own log file
        config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $game_id))]);

        Log::channel('single')->info('Game has been created.');
        return redirect(route('current_game', $game_id));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //parameter check
        if($request->army_name && $request->units_number && $request->strategy){
            if($request->units_number<=100 && $request->units_number>=80) {
                if($request->strategy == "random" || $request->strategy == "weakest" || $request->strategy == "strongest") {

                    //creating string/array for restart of the game
                    $starting_values = $request->army_name.','.$request->units_number.','.$request->strategy.',';
                    $input = ['name' => $request->army_name,
                        'number_of_units' => $request->units_number,
                        'attack_strategy' => $request->strategy,
                        'property_of' => $request->id,
                        'starting_values' => $starting_values,
                    ];

                    //creating army with given parameters
                    $army = Army::create($input);

                    //adding into existing
                    config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $request->id))]);
                    Log::channel('single')->info('Army '.$army->name.' had been created with '.$army->number_of_units.'
                     units and '.$army->attack_strategy.' attacking strategy.');
                }else{
                    $message = "Armies only know strategies with name random, weakest or strongest";
                    return redirect(route('error_message', $message));
                }
            }else{
                $message = "Number of units must be from 80 to 100 units.";
                return redirect(route('error_message', $message));
            }




        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($request->id);

        $game->update(['status' => 0]);

        config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $request->id))]);
        Log::channel('single')->info('Game was restarted. All units were resurrected and sent to starting positions
        to start dying again.');

        $armies = Army::where('property_of', $game->id)->get();

        foreach ($armies as $army){
            $starting_values = explode(",",$army->starting_values);

            $input = ['name'=>$starting_values[0],
                    'number_of_units'=>$starting_values[1],
                    'attack_strategy'=>$starting_values[2],
                ];
            $army->update($input);

            }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    function list() {
        //error Undefined property: Illuminate\Database\Query\Builder::$map ==not fixed
//        $games = Game::all();
//        return GameResource::collection($games);

        //poorer way of displaying games with data
        return new GameResource(Game::all());
    }

    function run(Request $request){

        $game = Game::findOrFail($request->id);

        //checking status and army count
        if($game->status == 0){
            $armies = Army::where('property_of', $game->id)->get();

            if($armies->count() >= 5){
                $game->update(['status' => 1]);
                echo "run attack success";
            }else{
                return "not enough armies.. need at least 5";
            }
        }else{
            echo "run attack success";
        }
    }

    public function select(){
        $games = Game::all();
        $select = [];
        foreach($games as $game){
            $select[$game->id] = $game->id;
        }
        return view('game', compact('select'));
    }

    public function add(Request $request){
        //parameter check
        if($request->army_name && $request->units_number && $request->strategy){
            if($request->units_number<=100 && $request->units_number>=80) {
                if($request->strategy == "random" || $request->strategy == "weakest" || $request->strategy == "strongest") {

                    //creating string/array for restart of the game
                    $starting_values = $request->army_name.','.$request->units_number.','.$request->strategy.',';
                    $input = ['name' => $request->army_name,
                        'number_of_units' => $request->units_number,
                        'attack_strategy' => $request->strategy,
                        'property_of' => $request->game,
                        'starting_values' => $starting_values,
                    ];

                    //creating army with given parameters
                    $army = Army::create($input);

                    //adding into existing
                    config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $request->game))]);
                    Log::channel('single')->info('Army '.$army->name.' had been created with '.$army->number_of_units.'
                     units and '.$army->attack_strategy.' attacking strategy.');
                }else{
                    $message = "Armies only know strategies with name random, weakest or strongest";
                    return redirect(route('error_message', $message));
                }
            }else{
                $message = "Number of units must be from 80 to 100 units.";
                return redirect(route('error_message', $message));
            }
        }
        return redirect()->back();
    }


    function attack(Request $request){
        $game = Game::findOrFail($request->game);
        $game->update(['status' => 0]);
        //checking status and army count
        if($game->status == 0){
            $armies = Army::where('property_of', $game->id)->get();

            if($armies->count() >= 10){
                $game->update(['status' => 1]);


                $startingNumberOfArmies = $armies->count();
                config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $game->id))]);
                Log::channel('single')->info('Battle has started! Clash of '.$startingNumberOfArmies.' brave armies.');
                //should be faster with parameter check then while(Army::all()->count() > 1)
                $deadArmies = 0;

                foreach ($armies as $army) {
                    $starting_units[$army->id] = $army->number_of_units;
                }
                $starting_units[0] = $game->id;


                $d = new ReviveArmies($armies, $starting_units);
                $this->dispatchNow($d);

                while($deadArmies <= ($startingNumberOfArmies - 1)) {

                    foreach ($armies as $army) {
                        //will attack first strongest or weakest army in armies db where armies are property of chosen game id
                        $strongest = Army::orderBy('number_of_units', 'desc')->first();
                        $weakest = Army::orderBy('number_of_units', 'asc')->first();
                        
                        $random = $armies->random();
                        $chance = rand(1, 100);
                        $dmg = $army->number_of_units * 0.5;
                        $strategy = $army->attack_strategy;
                        if ($chance <= $army->number_of_units) {
                            if ($strategy == 'strongest') {
                                config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $game->id))]);
                                Log::channel('single')->info($army->name.' is attacking '.$strongest->name.'.');

                                $survived_units = $strongest->number_of_units - $dmg;

                                $strongest->update(['number_of_units' => $survived_units]);

                                config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $game->id))]);
                                Log::channel('single')->info('Attack was successful. Damage '.$strongest->name.' received is '.$dmg.'Army '.$strongest->name.' has '.
                                    $survived_units.' units left.');

                            } elseif ($strategy == 'weakest') {
                                config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $game->id))]);
                                Log::channel('single')->info($army->name.' is attacking '.$weakest->name.'.');

                                $survived_units = $weakest->number_of_units - $dmg;

                                $weakest->update(['number_of_units' => $survived_units]);
                                config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $game->id))]);
                                Log::channel('single')->info('Attack was successful. Damage '.$weakest->name.' received is '.$dmg.'Army '.$weakest->name.' has '.
                                    $survived_units.' units left.');
                            } else {

                                config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $game->id))]);
                                Log::channel('single')->info($army->name . ' is attacking ' . $random->name . '.');

                                $survived_units = $random->number_of_units - $dmg;
                                $random->update(['number_of_units' => $survived_units]);

                                Log::channel('single')->info('Attack was successful. Damage ' . $random->name . ' received is ' . $dmg . 'Army ' . $random->name . ' has ' .
                                    $survived_units . ' units left.');
                            }
                            if ($survived_units <= 0){

                                config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $game->id))]);
                                Log::channel('single')->info('Army '.$strongest->name.' is defeated and removed from the game.');
                                if ($strategy == 'strongest') {
                                    $strongest->delete();
                                }elseif ($strategy == 'weakest') {
                                    $weakest->delete();
                                }else {
                                    $random->delete();
                                }
                                $deadArmies+=1;
                            }

                        } else {
                            config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $game->id))]);
                            Log::channel('single')->info('Attack failed.');
                        }

                    }
                }
                config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $game->id))]);
                Log::channel('single')->info('Game has finished.');
            }else{
                return "not enough armies.. need at least 10";
            }
        }else{
            return "game already started";
        }
    }

    //for personally easier use of Routes
    public function noMethod(){}

}
