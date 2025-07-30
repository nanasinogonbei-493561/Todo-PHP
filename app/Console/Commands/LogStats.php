<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LogStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:stats 
                            {--file=structured.log : Log file to analyze}
                            {--days=7 : Number of days to analyze}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show statistics for structured log files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logFile = $this->option('file');
        $days = (int) $this->option('days');

        $logPath = storage_path("logs/{$logFile}");
        
        if (!File::exists($logPath)) {
            $this->error("Log file not found: {$logPath}");
            return 1;
        }

        $this->info("Analyzing {$logFile} for the last {$days} days...");
        
        $stats = $this->analyzeLogFile($logPath, $days);
        $this->displayStats($stats);
        
        return 0;
    }

    /**
     * Analyze the log file and return statistics
     */
    private function analyzeLogFile(string $logPath, int $days): array
    {
        $stats = [
            'total_entries' => 0,
            'error_count' => 0,
            'user_actions' => 0,
            'database_operations' => 0,
            'performance_logs' => 0,
            'unique_users' => [],
            'top_actions' => [],
            'average_response_time' => 0,
            'response_times' => []
        ];

        $cutoffDate = now()->subDays($days);
        
        $handle = fopen($logPath, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $data = json_decode($line, true);
                if (!$data) continue;

                $logDate = \Carbon\Carbon::parse($data['timestamp'] ?? '');
                if ($logDate->lt($cutoffDate)) continue;

                $stats['total_entries']++;

                // Count by level
                if (isset($data['level_name'])) {
                    if (strtoupper($data['level_name']) === 'ERROR') {
                        $stats['error_count']++;
                    }
                }

                // Count by message type
                $message = $data['message'] ?? '';
                if (strpos($message, 'User action:') === 0) {
                    $stats['user_actions']++;
                    $action = str_replace('User action: ', '', $message);
                    $stats['top_actions'][$action] = ($stats['top_actions'][$action] ?? 0) + 1;
                } elseif (strpos($message, 'Database:') === 0) {
                    $stats['database_operations']++;
                } elseif (strpos($message, 'Performance:') === 0) {
                    $stats['performance_logs']++;
                    if (isset($data['duration_ms'])) {
                        $stats['response_times'][] = $data['duration_ms'];
                    }
                }

                // Track unique users
                if (isset($data['user_id']) && $data['user_id'] !== null) {
                    $stats['unique_users'][$data['user_id']] = true;
                }
            }
            fclose($handle);
        }

        // Calculate average response time
        if (!empty($stats['response_times'])) {
            $stats['average_response_time'] = array_sum($stats['response_times']) / count($stats['response_times']);
        }

        $stats['unique_users'] = count($stats['unique_users']);
        arsort($stats['top_actions']);

        return $stats;
    }

    /**
     * Display statistics in a formatted table
     */
    private function displayStats(array $stats): void
    {
        $this->info("\n📊 Log Statistics Summary");
        $this->line(str_repeat('=', 50));

        $summaryData = [
            ['Total Entries', $stats['total_entries']],
            ['Errors', $stats['error_count']],
            ['User Actions', $stats['user_actions']],
            ['Database Operations', $stats['database_operations']],
            ['Performance Logs', $stats['performance_logs']],
            ['Unique Users', $stats['unique_users']],
            ['Avg Response Time (ms)', round($stats['average_response_time'], 2)],
        ];

        $this->table(['Metric', 'Count'], $summaryData);

        if (!empty($stats['top_actions'])) {
            $this->info("\n🔥 Top User Actions");
            $this->line(str_repeat('-', 30));
            
            $actionData = [];
            $count = 0;
            foreach ($stats['top_actions'] as $action => $frequency) {
                if ($count++ >= 10) break; // Show top 10
                $actionData[] = [$action, $frequency];
            }
            
            $this->table(['Action', 'Frequency'], $actionData);
        }

        if ($stats['error_count'] > 0) {
            $errorRate = ($stats['error_count'] / $stats['total_entries']) * 100;
            $this->warn("\n⚠️  Error Rate: " . round($errorRate, 2) . "%");
        }

        if ($stats['average_response_time'] > 1000) {
            $this->warn("\n🐌 Slow Response Time: " . round($stats['average_response_time'], 2) . "ms");
        }
    }
} 