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

        // Current HEAD Commit
        $headRes = $this->runCmd('git log -1 --pretty=format:"%h|%s|%an|%cr"');
        $currentCommit = null;
        if ($headRes['success'] && strpos($headRes['output'], '|') !== false) {
            $parts = explode('|', $headRes['output'], 4);
            $currentCommit = [
                'hash' => $parts[0] ?? '',
                'message' => $parts[1] ?? '',
                'author' => $parts[2] ?? '',
                'time' => $parts[3] ?? '',
            ];
        }

        // Recent Commits Log
        $logsRes = $this->runCmd('git log -n 12 --pretty=format:"%h|%s|%an|%cr"');
        $recentCommits = [];
        if ($logsRes['success'] && !empty($logsRes['output'])) {
            $lines = explode("\n", trim($logsRes['output']));
            foreach ($lines as $line) {
                if (strpos($line, '|') !== false) {
                    $p = explode('|', $line, 4);
                    $recentCommits[] = [
                        'hash' => $p[0] ?? '',
                        'message' => $p[1] ?? '',
                        'author' => $p[2] ?? '',
                        'time' => $p[3] ?? '',
                    ];
                }
            }
        }

        // Git Status (Modified files)
        $statusRes = $this->runCmd('git status -s');
        $gitStatus = $statusRes['success'] ? trim($statusRes['output']) : '';

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

    public function pull(Request $request)
    {
        $branchRes = $this->runCmd('git rev-parse --abbrev-ref HEAD');
        $branch = $branchRes['success'] ? trim($branchRes['output']) : 'main';

        $res = $this->runCmd("git pull origin " . \escapeshellarg($branch));

        if ($res['success']) {
            return back()->with('success', '⚡ Git Pull Successful! New code updated from GitHub repository. Log: ' . $res['output']);
        }

        return back()->with('error', '❌ Git Pull Failed: ' . $res['output']);
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
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            return back()->with('success', '🧹 All View, Config & Application Caches Cleared Successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', '❌ Cache Flush Error: ' . $e->getMessage());
        }
    }

    public function deploy(Request $request)
    {
        $log = [];

        // 1. Git Pull
        $branchRes = $this->runCmd('git rev-parse --abbrev-ref HEAD');
        $branch = $branchRes['success'] ? trim($branchRes['output']) : 'main';
        $pullRes = $this->runCmd("git pull origin " . \escapeshellarg($branch));
        $log[] = "--- GIT PULL ---";
        $log[] = $pullRes['output'];

        // 2. Migrate
        $log[] = "\n--- DATABASE MIGRATIONS ---";
        try {
            Artisan::call('migrate', ['--force' => true]);
            $log[] = Artisan::output();
        } catch (\Throwable $e) {
            $log[] = "Migration Error: " . $e->getMessage();
        }

        // 3. Clear Caches
        $log[] = "\n--- CACHE FLUSH ---";
        try {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            $log[] = "View & Cache cleared cleanly.";
        } catch (\Throwable $e) {
            $log[] = "Cache Error: " . $e->getMessage();
        }

        return back()->with('deploy_log', implode("\n", $log))->with('success', '🚀 1-Click Auto-Deployment Complete!');
    }
}
