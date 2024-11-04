<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use Carbon\Carbon;

class SchedulePostActivation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:post-activation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate or deactivate posts based on their scheduled start and end dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        Post::where('start_date', '<=', $now)
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', $now);
            })
            ->update(['status' => 'active']);

        Post::where('end_date', '<=', $now)
            ->update(['status' => 'inactive']);

        $this->info('Post status updated successfully.');
    }
}
