<?php

namespace App\Logging;

use Exception;
use Illuminate\Support\Facades\Auth;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Monolog\LogRecord;

class LogstashHttpLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('logstash');

        $logger->pushHandler(new class extends AbstractProcessingHandler {
            protected function write(LogRecord $record): void
            {
                try {
                    $method = request()->method() ?? null;
                    $ip = request()->ip() ?? null;
                    $uri = request()->getRequestUri() ?? null;
                    $userId = Auth::user()?->id ?? null;
                    $userAgent = request()->header('User-Agent');
                    $traceId = request()->header('X-Request-ID', uniqid());

                    // Tambahkan default context ke context log asli
                    $context = array_merge([
                        'method'        => $method,
                        'ip'            => $ip,
                        'uri'           => $uri,
                        'user_id'       => $userId,
                        'user_agent'    => $userAgent,
                        'trace_id'      => $traceId
                    ], $record['context']);

                    Http::asJson()
                        ->withBasicAuth(env('LOGSTASH_USER'), env('LOGSTASH_PASS'))
                        ->timeout(2)
                        ->post(env('LOGSTASH_URL'), [
                            'project' => 'CRM',
                            'message' => $record['message'],
                            'level' => $record['level_name'],
                            'context' => $context,
                            'channel' => $record['channel'],
                            'datetime' => $record['datetime']->format('Y-m-d H:i:s')
                        ]);
                } catch (Exception $e) {
                    Log::error('error :' . $e->getMessage());
                }
            }
        });

        return $logger;
    }
}
