<?php

namespace App\Console;


use App\Jobs\ReviveArmies;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */

        public function schedule(Schedule $schedule)
    {
        $schedule->call(function () {

            $dt = Carbon::now();

            $x=60/0.01;

            do{

                $schedule->job(new ReviveArmies());
dd($dt->addSeconds(0.01)->timestamp);
                time_sleep_until($dt->addSeconds(0.01)->timestamp);

            } while($x-- > 0);

        })->everyMinute();



    }



    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
