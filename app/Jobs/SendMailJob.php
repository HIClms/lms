<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ZeptomailService;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $subject;
    protected $body;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $body)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ZeptomailService::sendMailZeptoMail($this->subject, $this->body, $this->email);
    }
}
