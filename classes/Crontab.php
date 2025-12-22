<?php

class CronTab {

    public static function remove (string $command, string $schedule='' ) {
        // Get the current crontab file
        $cmd = 'crontab -l';
        $current_crontab = shell_exec($cmd);
        //echo "<pre>Current crontab:\n ".htmlspecialchars($current_crontab) . "</pre>";

        // Modify the crontab file (e.g. remove a specific line)
        if ($schedule == '') { //match command only with any schedule
            $pattern = '/^(\S+\s+\S+\s+\S+\s+\S+\s+\S+\s+).*' . preg_quote($command, '/') . '.*$/m';
            //echo "<pre>Pattern: " . htmlspecialchars($pattern) . "</pre>";

            $new_crontab = preg_replace($pattern, '', $current_crontab);
        } else {
            $new_crontab = str_replace($schedule.' '.$command, '', $current_crontab);
        }
        // Remove double or trailing SHELL lines
        $new_crontab = preg_replace('/^SHELL="[^"]*"\n(?=SHELL=)/m', '', $new_crontab); // Remove SHELL followed by another SHELL
        $new_crontab = preg_replace('/^SHELL="[^"]*"\n*$/m', '', $new_crontab); // Remove SHELL at end of file
        
        //echo "<pre>New crontab:\n" . htmlspecialchars($new_crontab) . "</pre>";

        if ($current_crontab === $new_crontab) {
            echo "<strong>WARNING: No changes detected!</strong><br>";
        }

        // Save the modified crontab file to a temporary file
        $temp_file = tempnam(sys_get_temp_dir(), 'crontab_');
        file_put_contents($temp_file, $new_crontab);
        // Install the temporary file as the new crontab
        $cmd = 'crontab '.$temp_file;
        $output = shell_exec($cmd);
        // Clean up the temporary file
        unlink($temp_file);
    }

    public static function view() {
        $cron_output = shell_exec('crontab -l');
        return $cron_output;
    }

    public static function add(string $schedule, string $command) {
        $output = shell_exec('crontab -l | { cat; echo "'.$schedule.' '.$command.'"; } | crontab - 2>&1');
    }

    public static function replace($commandToRemove, $commandToAdd, $scheduleToAdd, $scheduleToRemove='') {
        Self::remove($commandToRemove,$scheduleToRemove);
        Self::add($scheduleToAdd, $commandToAdd);        
    }

    public static function getScheduleForCommand($command)
    {
        // Get the current crontab file
        $currentCrontab = shell_exec('crontab -l');
        
        // Split the crontab into individual lines
        $lines = explode("\n", trim($currentCrontab));
        
        // Iterate through each line and find the schedule for the given command
        foreach ($lines as $line) {
            // Remove leading/trailing spaces and tabs
            $line = trim($line);
            
            // Skip empty lines or comments (lines starting with '#')
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }
            
            // Split the line into schedule and command parts
            $parts = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
            $schedule = implode(' ', array_slice($parts, 0, 5));
            $commandPart = implode(' ', array_slice($parts, 5));
            
            // Compare the command to check for a match
            if ($commandPart === $command) {
                return $schedule;
            }
        }
        
        return null; // Command not found in the crontab
    }
}