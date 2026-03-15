<?php
/**
 * Utility script to search logs for AI disable events
 * Usage: php search_ai_disable_logs.php [log_file_path] [date_filter]
 */

// Get log file path from command line or use default
$log_file = isset($argv[1]) ? $argv[1] : 'application/logs/log-' . date('Y-m-d') . '.php';
$date_filter = isset($argv[2]) ? $argv[2] : null;

if (!file_exists($log_file)) {
    echo "Log file not found: $log_file\n";
    exit(1);
}

echo "Searching for AI disable events in: $log_file\n";
if ($date_filter) {
    echo "Date filter: $date_filter\n";
}
echo str_repeat("=", 80) . "\n\n";

$handle = fopen($log_file, 'r');
if (!$handle) {
    echo "Could not open log file: $log_file\n";
    exit(1);
}

$patterns = [
    'DEVICE AI TOGGLE' => 'Device-level AI toggle (CRITICAL)',
    'manage_conversation DISABLE_AI' => 'manage_conversation disable_ai function',
    'manage_conversation.*CRITICAL ERROR' => 'Device AI changed during lead update (BUG)',
    'TRIGGER DISABLE_AI' => 'Message trigger disable_ai',
    'Desligou a IA do Device' => 'Device AI turned off',
    'Ligou a IA do Device' => 'Device AI turned on',
    'IA desabilitada para esta conversa' => 'Lead AI disabled (conversation)',
    'gpt_status.*1' => 'gpt_status set to 1',
    'action_disable_ai' => 'trigger action_disable_ai',
    'disable_ai.*true' => 'disable_ai = true',
];

$results = [];
$line_number = 0;
$current_entry = '';
$in_entry = false;

while (($line = fgets($handle)) !== false) {
    $line_number++;
    
    // Check if this is a new log entry
    if (preg_match('/^(ERROR|DEBUG|INFO|WARNING) - (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $line, $matches)) {
        // Save previous entry if it matched
        if ($in_entry && !empty($current_entry)) {
            foreach ($patterns as $pattern => $label) {
                if (preg_match('/' . $pattern . '/i', $current_entry)) {
                    $results[] = [
                        'line' => $line_number - substr_count($current_entry, "\n"),
                        'label' => $label,
                        'entry' => $current_entry
                    ];
                    break;
                }
            }
        }
        
        // Start new entry
        $current_entry = $line;
        $in_entry = true;
        
        // Check date filter
        if ($date_filter && isset($matches[2]) && strpos($matches[2], $date_filter) === false) {
            $in_entry = false;
            $current_entry = '';
        }
    } elseif ($in_entry) {
        $current_entry .= $line;
    }
}

// Check last entry
if ($in_entry && !empty($current_entry)) {
    foreach ($patterns as $pattern => $label) {
        if (preg_match('/' . $pattern . '/i', $current_entry)) {
            $results[] = [
                'line' => $line_number - substr_count($current_entry, "\n"),
                'label' => $label,
                'entry' => $current_entry
            ];
            break;
        }
    }
}

fclose($handle);

// Display results
if (empty($results)) {
    echo "No AI disable events found.\n";
} else {
    echo "Found " . count($results) . " AI disable event(s):\n\n";
    
    foreach ($results as $index => $result) {
        echo "Event #" . ($index + 1) . " - Line {$result['line']} - {$result['label']}\n";
        echo str_repeat("-", 80) . "\n";
        echo $result['entry'];
        echo "\n" . str_repeat("=", 80) . "\n\n";
    }
    
    // Summary by type
    $summary = [];
    foreach ($results as $result) {
        $summary[$result['label']] = ($summary[$result['label']] ?? 0) + 1;
    }
    
    echo "Summary by type:\n";
    foreach ($summary as $label => $count) {
        echo "  - $label: $count\n";
    }
}
