<?php

namespace App\Jobs;

use Illuminate\Support\Carbon;
use App\Army;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReviveArmies implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $armies;
    protected $starting_units;

    /**
     * Create a new job instance.
     *
     * @param $armies
     * @param $starting_units
     */
    public function __construct($armies, $starting_units)
    {
        $this->armies = $armies;
        $this->starting_units = $starting_units;
    }

    /**
     * Execute the job.
     *
     * @param $armies
     * @return void
     */
    public function handle()
    {


            foreach ($this->armies as $army) {
//                if ($army->number_of_units < $this->starting_units[$army->id]) {
                $army->update(['number_of_units' => ($army->number_of_units + 1)]);
//                }
                config(['logging.channels.single.path' => storage_path(sprintf('logs/game-%s.log', $this->starting_units[0]))]);
                Log::channel('single')->info('Units revived');



        }
    }

    public function retryUntil()
    {
        return now()->addSeconds(20);
    }
}
