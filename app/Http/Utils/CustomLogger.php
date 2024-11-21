<?php

namespace App\Http\Utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class CustomLogger
{
    protected $logger;

    public function __invoke()
    {
        $logger = new Logger('ticket_cai');

        $date = Carbon::now()->format('Y-m-d');

        $handler = new StreamHandler(
            storage_path('logs/'.$date.'.log'),
            Logger::DEBUG
        );

        $formatter = new LineFormatter(
            "[TICKET_CAI - %datetime%] -  %context%: %message%\n",
            "H:i:s"
        );

        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);

        return $logger;
    }



    public static function _write(string $msg,?string $type): void
    {
        $context = [];
        if(!is_null($type))
            $context = self::_context($type);
       Log::channel('ticket_cai')->getLogger()->info($msg,[$context]);
    }

    protected static function _context(string $tpe): string
    {
        return "trace:".Str::uuid()->toString()."~type:".$tpe;
    }

}
