<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ActivityService;

class ActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $subject;
    protected $body;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subject, $body, $user_id)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ActivityService::saveActivity($this->subject, $this->body, $this->user_id);
    }
}
