<?php
namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord; // Importă clasa LogRecord
use Illuminate\Support\Facades\DB;

class CustomDatabaseLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('db');
        $logger->pushHandler(new class extends AbstractProcessingHandler {
            protected function write(LogRecord $record): void // Ajustează tipul aici
            {
                DB::table('activity_logs')->insert([
                    'user_id' => $record->context['user_id'] ?? null,
                    'performed_by' => $record->context['performed_by'] ?? null,
                    'email' => $record->context['email'] ?? null,
                    'event_type_id' => $record->context['event_type_id'] ?? null,
                    'ip_address' => $record->context['ip_address'] ?? request()->ip(),
                    'message' => $record->message,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return $logger;
    }
}
