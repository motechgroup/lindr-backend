<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class GitController extends Controller
{
    private function runCmd($cmd)
    {
        $output = [];
        $returnVar = 1;
        $workDir = base_path();

        $disabled = array_map('trim', explode(',', ini_get('disable_functions') ?: ''));

        if (!function_exists('exec') || in_array('exec', $disabled)) {
            if (function_exists('shell_exec') && !in_array('shell_exec', $disabled)) {
                $fullCmd = "cd " . \escapeshellarg($workDir) . " && " . $cmd . " 2>&1";
                $res = @\shell_exec($fullCmd);
                return [
                    'success' => !empty($res),
                    'output' => $res ?: 'Command completed with empty output.',
                    'code' => 0,
                ];
            }

            return [
                'success' => false,
                'output' => 'Shell command execution (exec / shell_exec) is restricted on this shared host.',
                'code' => 1,
            ];
        }

        $fullCmd = "cd " . \escapeshellarg($workDir) . " && " . $cmd . " 2>&1";
        try {
            @\exec($fullCmd, $output, $returnVar);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'output' => 'Execution error: ' . $e->getMessage(),
                'code' => 1,
            ];
        }

        return [
            'success' => $returnVar === 0,
            'output' => implode("\n", $output),
            'code' => $returnVar,
        ];
    }

    public function index()
    {
        $repoUrl = 'https://github.com/motechgroup/lindr-backend.git';
        
        // Branch check
        $branchRes = $this->runCmd('git rev-parse --abbrev-ref HEAD');
        $currentBranch = $branchRes['success'] ? trim($branchRes['output']) : 'main';

        // Remote URL
        $remoteRes = $this->runCmd('git remote get-url origin');
        if ($remoteRes['success'] && !empty($remoteRes['output'])) {
            $repoUrl = trim($remoteRes['output']);
        }

        // Current HEAD Commit & Recent Commits
        $currentCommit = null;
        $recentCommits = [];
        $logsRes = $this->runCmd('git log -n 12 --pretty=format:"%h|%s|%an|%cr"');

        if ($logsRes['success'] && !empty($logsRes['output'])) {
            $lines = explode("\n", trim($logsRes['output']));
            foreach ($lines as $index => $line) {
                if (strpos($line, '|') !== false) {
                    $p = explode('|', $line, 4);
                    $commitObj = [
                        'hash' => $p[0] ?? '',
                        'message' => $p[1] ?? '',
                        'author' => $p[2] ?? '',
                        'time' => $p[3] ?? '',
                    ];
                    if ($index === 0) $currentCommit = $commitObj;
                    $recentCommits[] = $commitObj;
                }
            }
        } else {
            // Fallback to GitHub API when shell_exec is restricted on shared hosting
            $apiCommits = $this->fetchGitHubCommitsViaApi();
            if (!empty($apiCommits)) {
                $currentCommit = $apiCommits[0];
                $recentCommits = $apiCommits;
            }
        }

        // Git Status (Modified files)
        $statusRes = $this->runCmd('git status -s');
        $gitStatus = $statusRes['success'] ? trim($statusRes['output']) : 'Shared Hosting (Native PHP Zip Sync Active)';

        // Pending Migrations Check
        $pendingMigrations = [];
        try {
            if (!Schema::hasTable('migrations')) {
                Artisan::call('migrate:install');
            }

            $ranMigrations = DB::table('migrations')->pluck('migration')->toArray();
            $migrationFiles = File::files(database_path('migrations'));
            
            foreach ($migrationFiles as $file) {
                $filename = str_replace('.php', '', $file->getFilename());
                if (!in_array($filename, $ranMigrations)) {
                    // Check if corresponding table already exists in MySQL
                    $tableName = null;
                    if (str_contains($filename, 'create_users')) $tableName = 'users';
                    elseif (str_contains($filename, 'create_cache')) $tableName = 'cache';
                    elseif (str_contains($filename, 'create_jobs')) $tableName = 'jobs';
                    elseif (str_contains($filename, 'create_transactions')) $tableName = 'transactions';
                    elseif (str_contains($filename, 'create_withdrawals')) $tableName = 'withdrawals';
                    elseif (str_contains($filename, 'create_call_sessions')) $tableName = 'call_sessions';
                    elseif (str_contains($filename, 'create_chat_messages')) $tableName = 'chat_messages';
                    elseif (str_contains($filename, 'create_user_tasks')) $tableName = 'user_tasks';
                    elseif (str_contains($filename, 'create_system_settings')) $tableName = 'system_settings';
                    elseif (str_contains($filename, 'create_token_packages')) $tableName = 'token_packages';

                    if ($tableName && Schema::hasTable($tableName)) {
                        DB::table('migrations')->insertOrIgnore([
                            'migration' => $filename,
                            'batch' => 1
                        ]);
                    } else {
                        $pendingMigrations[] = $filename;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Table might not exist yet
        }

        return view('admin.git', compact(
            'repoUrl',
            'currentBranch',
            'currentCommit',
            'recentCommits',
            'gitStatus',
            'pendingMigrations'
        ));
    }

    private function pullViaZipArchive($branch = 'main')
    {
        $zipUrl = "https://github.com/motechgroup/lindr-backend/archive/refs/heads/{$branch}.zip";
        $tempZipPath = storage_path("app/latest_repo.zip");
        
        // Download zip using cURL or file_get_contents
        $zipData = null;
        if (function_exists('curl_init')) {
            $ch = curl_init($zipUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Lindr-App-Updater/1.0');
            $zipData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                $zipData = null;
            }
        }

        if (empty($zipData)) {
            $opts = [
                'http' => [
                    'header' => "User-Agent: Lindr-App-Updater/1.0\r\n"
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ];
            $zipData = @file_get_contents($zipUrl, false, stream_context_create($opts));
        }

        if (empty($zipData)) {
            return [
                'success' => false,
                'output' => 'Failed to download GitHub source zip package.'
            ];
        }

        File::put($tempZipPath, $zipData);

        if (!class_exists('ZipArchive')) {
            return [
                'success' => false,
                'output' => 'PHP ZipArchive extension is not enabled on this host.'
            ];
        }

        $zip = new \ZipArchive();
        if ($zip->open($tempZipPath) !== true) {
            return [
                'success' => false,
                'output' => 'Failed to open downloaded source zip package.'
            ];
        }

        $extractedCount = 0;
        $basePath = base_path();

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $entryName = $stat['name'];

            // Remove top-level directory in GitHub zip (e.g. lindr-backend-main/)
            $relativePath = preg_replace('/^[^\/]+\//', '', $entryName);

            if (empty($relativePath)) continue;

            // Skip protected paths
            if (
                str_starts_with($relativePath, '.env') ||
                str_starts_with($relativePath, 'storage/') ||
                str_starts_with($relativePath, 'database/database.sqlite')
            ) {
                continue;
            }

            $targetPath = $basePath . '/' . $relativePath;

            if (str_ends_with($entryName, '/')) {
                if (!File::exists($targetPath)) {
                    File::makeDirectory($targetPath, 0755, true, true);
                }
            } else {
                $dir = dirname($targetPath);
                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0755, true, true);
                }
                $content = $zip->getFromIndex($i);
                if ($content !== false) {
                    File::put($targetPath, $content);
                    $extractedCount++;
                }
            }
        }

        $zip->close();
        @File::delete($tempZipPath);

        return [
            'success' => true,
            'output' => "Successfully updated {$extractedCount} files directly via GitHub Zip Sync (PHP Native)."
        ];
    }

    private function fetchGitHubCommitsViaApi()
    {
        $url = "https://api.github.com/repos/motechgroup/lindr-backend/commits?per_page=12";
        $json = null;

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Lindr-App-Updater/1.0');
            $json = curl_exec($ch);
            curl_close($ch);
        }

        if (empty($json)) {
            $opts = [
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: Lindr-App-Updater/1.0\r\n"
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ];
            $json = @file_get_contents($url, false, stream_context_create($opts));
        }

        $commits = [];
        if (!empty($json)) {
            $data = json_decode($json, true);
            if (is_array($data)) {
                foreach ($data as $c) {
                    $commits[] = [
                        'hash' => substr($c['sha'] ?? '', 0, 7),
                        'message' => strtok($c['commit']['message'] ?? '', "\n"),
                        'author' => $c['commit']['author']['name'] ?? 'GitHub',
                        'time' => isset($c['commit']['author']['date']) ? date('M d, Y H:i', strtotime($c['commit']['author']['date'])) : '',
                    ];
                }
            }
        }
        return $commits;
    }

    public function pull(Request $request)
    {
        $branchRes = $this->runCmd('git rev-parse --abbrev-ref HEAD');
        $branch = $branchRes['success'] ? trim($branchRes['output']) : 'main';

        $res = $this->runCmd("git pull origin " . \escapeshellarg($branch));

        if ($res['success']) {
            return back()->with('success', '⚡ Git Pull Successful! New code updated from GitHub. Output: ' . $res['output']);
        }

        // Fallback to Native PHP Zip sync when shell_exec is restricted
        $zipRes = $this->pullViaZipArchive($branch);
        if ($zipRes['success']) {
            return back()->with('success', '⚡ Code Updated Successfully from GitHub! (PHP Zip Fallback Engine). Log: ' . $zipRes['output']);
        }

        return back()->with('error', '❌ Update Failed: ' . $zipRes['output']);
    }

    public function migrate(Request $request)
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            return back()->with('success', '🗄️ Database Migrations Executed Successfully! ' . trim($output));
        } catch (\Throwable $e) {
            return back()->with('error', '❌ Database Migration Failed: ' . $e->getMessage());
        }
    }

    public function clearCache(Request $request)
    {
        try {
            if (File::exists(base_path('bootstrap/cache'))) {
                foreach (File::files(base_path('bootstrap/cache')) as $f) {
                    if ($f->getFilename() !== '.gitignore') {
                        @File::delete($f->getPathname());
                    }
                }
            }
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            return back()->with('success', '🧹 All View, Config, Route & Application Caches Cleared Successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', '❌ Cache Flush Error: ' . $e->getMessage());
        }
    }

    public function deploy(Request $request)
    {
        $log = [];

        // 1. Git Pull or Native PHP Zip Sync
        $branchRes = $this->runCmd('git rev-parse --abbrev-ref HEAD');
        $branch = $branchRes['success'] ? trim($branchRes['output']) : 'main';
        $pullRes = $this->runCmd("git pull origin " . \escapeshellarg($branch));

        if ($pullRes['success']) {
            $log[] = "--- GIT PULL (CLI) ---";
            $log[] = $pullRes['output'];
        } else {
            $zipRes = $this->pullViaZipArchive($branch);
            $log[] = "--- GITHUB SYNC (PHP Native Zip Engine) ---";
            $log[] = $zipRes['output'];
        }

        // 2. Migrate
        $log[] = "\n--- DATABASE MIGRATIONS ---";
        try {
            Artisan::call('migrate', ['--force' => true]);
            $log[] = Artisan::output();
        } catch (\Throwable $e) {
            $log[] = "Migration Error: " . $e->getMessage();
        }

        // 3. Clear Caches & Delete Compiled Route/Config Cache Files
        $log[] = "\n--- CACHE FLUSH ---";
        try {
            if (File::exists(base_path('bootstrap/cache'))) {
                foreach (File::files(base_path('bootstrap/cache')) as $f) {
                    if ($f->getFilename() !== '.gitignore') {
                        @File::delete($f->getPathname());
                    }
                }
            }
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            $log[] = "View, Config, Route & Application caches cleared cleanly.";
        } catch (\Throwable $e) {
            $log[] = "Cache Error: " . $e->getMessage();
        }

        return back()->with('deploy_log', implode("\n", $log))->with('success', '🚀 1-Click Auto-Deployment Complete!');
    }
}
