<?php
namespace App\Logging;

class LogInjector
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {

        $logger->pushProcessor(new AddInstructionsProcessor);
        
    }
}