<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StopQueueListeners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kill the queue:listen processes for this project.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pids = $this->getQueueListenerPids();

        if (count($pids) === 0) {
            $this->info('No queue listeners are currently running.');
            return;
        }

        $this->stopQueueListeners($pids);
        $this->info('All done.');
    }

    /**
     * Get the PID's of queue listeners for the current project.
     *
     * @return array
     */
    private function getQueueListenerPids()
    {
        $command = 'ps -u forge -eo pid,user,command | grep $(pwd) | grep "queue:listen" | grep -v grep | awk \'{print $1}\'';
        exec($command, $pids);

        return (array) $pids;
    }

    /**
     * Kill queue listener processes.
     *
     * @param array $pids
     *
     * @return void
     */
    private function stopQueueListeners(array $pids)
    {
        foreach ($pids as $pid) {
            $this->info('Killing queue listener with PID #' . $pid);
            exec('kill -TERM ' . $pid);
        }
    }
}