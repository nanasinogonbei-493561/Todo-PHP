<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LogSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:search 
                            {query : The search query}
                            {--file=structured.log : Log file to search in}
                            {--limit=50 : Number of results to show}
                            {--json : Output in JSON format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search through structured log files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = $this->argument('query');
        $logFile = $this->option('file');
        $limit = (int) $this->option('limit');
        $jsonOutput = $this->option('json');

        $logPath = storage_path("logs/{$logFile}");
        
        if (!File::exists($logPath)) {
            $this->error("Log file not found: {$logPath}");
            return 1;
        }

        $this->info("Searching for '{$query}' in {$logFile}...");
        
        $results = [];
        $count = 0;
        
        $handle = fopen($logPath, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false && $count < $limit) {
                if (stripos($line, $query) !== false) {
                    $results[] = $this->formatLogLine($line, $jsonOutput);
                    $count++;
                }
            }
            fclose($handle);
        }

        if (empty($results)) {
            $this->warn("No results found for '{$query}'");
            return 0;
        }

        if ($jsonOutput) {
            $this->output->write(json_encode($results, JSON_PRETTY_PRINT));
        } else {
            $this->displayResults($results);
        }

        $this->info("\nFound {$count} results");
        return 0;
    }

    /**
     * Format a log line for display
     */
    private function formatLogLine(string $line, bool $jsonOutput): array|string
    {
        $data = json_decode($line, true);
        
        if ($jsonOutput) {
            return $data ?: ['raw' => trim($line)];
        }

        if ($data) {
            $timestamp = $data['timestamp'] ?? 'N/A';
            $level = $data['level_name'] ?? 'INFO';
            $message = $data['message'] ?? 'No message';
            $userId = $data['user_id'] ?? 'N/A';
            
            return "[{$timestamp}] [{$level}] [User: {$userId}] {$message}";
        }

        return trim($line);
    }

    /**
     * Display search results in a table format
     */
    private function displayResults(array $results): void
    {
        $tableData = [];
        
        foreach ($results as $result) {
            if (is_array($result)) {
                $tableData[] = [
                    $result['timestamp'] ?? 'N/A',
                    $result['level_name'] ?? 'INFO',
                    $result['user_id'] ?? 'N/A',
                    $result['message'] ?? 'No message',
                    $result['url'] ?? 'N/A'
                ];
            } else {
                $tableData[] = ['N/A', 'N/A', 'N/A', $result, 'N/A'];
            }
        }

        $this->table(
            ['Timestamp', 'Level', 'User ID', 'Message', 'URL'],
            $tableData
        );
    }
} 