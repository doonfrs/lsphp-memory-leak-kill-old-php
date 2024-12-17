# Kill Long-Running lsphp Processes

This script identifies and terminates any `lsphp` processes that have been running for longer than a specified duration (default: 5 minutes). It's designed to help manage resource usage on servers running OpenLiteSpeed by killing idle or long-running PHP processes.

## Features
- Scans all `lsphp` processes on the server.
- Kills processes running longer than a configurable time limit.
- Gracefully terminates processes first, then forcefully kills if necessary.
- Logs all actions with timestamps for clarity.

## Prerequisites
- PHP must be installed on the server.
- The script must be run with sufficient privileges to kill processes.
- `ps` command must be available (commonly present in Linux environments).

## Installation
1. Save the script as `kill_lsphp.php` on your server.
2. Ensure the script is executable and accessible to your PHP environment.
   ```bash
   chmod +x kill_lsphp.php
   ```

## Usage
Run the script using the command line:
```bash
php kill_lsphp.php
```

### How It Works
1. The script retrieves all `lsphp` processes using the `ps` command.
2. It calculates the elapsed time of each process.
3. Processes running longer than the defined time limit (default: 5 minutes) are terminated:
   - First attempt: Sends a `SIGTERM` signal (soft kill).
   - Second attempt: Waits 5 seconds and force kills (`SIGKILL`) any remaining processes.
4. Actions are logged to the console with timestamps.

## Configuration
- The time limit can be modified by changing the `$timeLimit` variable in the script:
   ```php
   $timeLimit = 300; // Time limit in seconds (default: 5 minutes)
   ```

## Example Log Output
```
[2024-06-17 12:00:00] Checking for lsphp processes running longer than 5 minutes...
[2024-06-17 12:00:01] Killing process owned by user1 - PID: 1234, Elapsed Time: 06:12, Command: lsphp
[2024-06-17 12:00:06] Force killing process - PID: 1234
[2024-06-17 12:00:06] Done.
```

## Notes
- The script uses `exec()` to execute shell commands. Ensure it is permitted in your PHP configuration.
- Proper user permissions are required to kill processes. Use `sudo` if necessary.
- Use caution when running this script to avoid unintended termination of critical processes.

## License
This project is licensed under the MIT License.

## Contributing
Pull requests and feature suggestions are welcome. Please ensure that changes are tested before submitting.
