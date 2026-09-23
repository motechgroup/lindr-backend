<?php
/**
 * Standalone Lindr Web Installer & Database Configurator
 * Zero framework dependency — runs cleanly on any shared host without 500 errors.
 * URL: https://lindrapp.top/setup.php
 */

// Enable error reporting to prevent white-screen 500 errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = '127.0.0.1';
$port = '3306';
$dbname = 'vrmthdfl_lindrdbmysql';
$username = 'vrmthdfl_lindradminmysql';
$password = '!;NKLFT)8gahGWz~';

// Read .env if present
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    $envFile = __DIR__ . '/../.env';
}

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim(trim($val), '"\'');
            if ($key === 'DB_HOST') $host = $val;
            if ($key === 'DB_PORT') $port = $val;
            if ($key === 'DB_DATABASE') $dbname = $val;
            if ($key === 'DB_USERNAME') $username = $val;
            if ($key === 'DB_PASSWORD') $password = $val;
        }
    }
}

$action = $_GET['action'] ?? 'index';
$messages = [];
$pdo = null;
$dbConnected = false;

// Test MySQL Connection
try {
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $dbConnected = true;
} catch (\Exception $e) {
    $messages[] = ['type' => 'error', 'text' => 'MySQL Connection Error: ' . $e->getMessage()];
}

// Perform Installation / SQL Import
if ($action === 'install' && $dbConnected && $pdo) {
    $sqlFile = __DIR__ . '/lindr_database.sql';
    if (!file_exists($sqlFile)) {
        $sqlFile = __DIR__ . '/../lindr_database.sql';
    }

    if (file_exists($sqlFile)) {
        try {
            $sql = file_get_contents($sqlFile);
            $pdo->exec($sql);
            $messages[] = ['type' => 'success', 'text' => '✅ Successfully imported database tables and seeded Admin account & settings!'];
        } catch (\Exception $e) {
            $messages[] = ['type' => 'error', 'text' => 'SQL Import Exception: ' . $e->getMessage()];
        }
    } else {
        $messages[] = ['type' => 'error', 'text' => 'lindr_database.sql file not found in package!'];
    }

    // Ensure storage and cache directories exist with full permissions
    $requiredDirs = [
        __DIR__ . '/storage',
        __DIR__ . '/storage/app',
        __DIR__ . '/storage/app/public',
        __DIR__ . '/storage/framework',
        __DIR__ . '/storage/framework/views',
        __DIR__ . '/storage/framework/sessions',
        __DIR__ . '/storage/framework/cache',
        __DIR__ . '/storage/framework/cache/data',
        __DIR__ . '/storage/logs',
        __DIR__ . '/bootstrap/cache',
    ];

    foreach ($requiredDirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        @chmod($dir, 0777);
    }

    // Clear runtime view and session caches (preserving bootstrap manifests)
    $purgeDirs = [
        __DIR__ . '/storage/framework/views',
        __DIR__ . '/storage/framework/sessions',
        __DIR__ . '/storage/framework/cache/data',
    ];

    foreach ($purgeDirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitignore') {
                    @unlink($file);
                }
            }
        }
    }
    $messages[] = ['type' => 'success', 'text' => '✅ Application storage directories verified, permissions configured (0777) & view/session caches flushed!'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lindr Web Setup & Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0F0C20; color: #E0E0EC; }
        .glass-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.12); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-2xl w-full glass-card p-8 rounded-3xl shadow-2xl space-y-6">
        <div class="flex items-center gap-4 pb-6 border-b border-white/10">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#FF2D55] to-[#9C27B0] flex items-center justify-center text-3xl shadow-lg">
                🔥
            </div>
            <div>
                <h1 class="text-2xl font-black text-white">Lindr Shared Hosting Installer</h1>
                <p class="text-xs text-purple-300">Standalone Web Setup Tool for lindrapp.top</p>
            </div>
        </div>

        <!-- Output Messages -->
        <?php foreach ($messages as $msg): ?>
            <div class="p-4 rounded-xl <?= $msg['type'] === 'success' ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400' : 'bg-red-500/10 border border-red-500/20 text-red-400' ?> text-sm font-semibold">
                <?= htmlspecialchars($msg['text']) ?>
            </div>
        <?php endforeach; ?>

        <!-- System Requirements Diagnostic -->
        <div class="space-y-4">
            <h2 class="text-sm font-bold text-purple-300 uppercase tracking-wider">System Diagnostic Check</h2>
            
            <div class="grid grid-cols-2 gap-4 text-xs font-semibold">
                <div class="p-3 rounded-xl bg-white/5 flex items-center justify-between">
                    <span>PHP Version: <strong><?= PHP_VERSION ?></strong></span>
                    <span class="text-emerald-400 font-bold">Passed</span>
                </div>
                <div class="p-3 rounded-xl bg-white/5 flex items-center justify-between">
                    <span>PDO MySQL: <strong><?= extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled' ?></strong></span>
                    <span class="<?= extension_loaded('pdo_mysql') ? 'text-emerald-400' : 'text-red-400' ?> font-bold">
                        <?= extension_loaded('pdo_mysql') ? 'Passed' : 'Failed' ?>
                    </span>
                </div>
                <div class="p-3 rounded-xl bg-white/5 flex items-center justify-between">
                    <span>MySQL Connection:</span>
                    <span class="<?= $dbConnected ? 'text-emerald-400' : 'text-red-400' ?> font-bold">
                        <?= $dbConnected ? 'Connected ✅' : 'Failed ❌' ?>
                    </span>
                </div>
                <div class="p-3 rounded-xl bg-white/5 flex items-center justify-between">
                    <span>Database Name:</span>
                    <span class="text-yellow-400 font-mono"><?= htmlspecialchars($dbname) ?></span>
                </div>
            </div>

            <!-- Environment Details -->
            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-400 font-bold">DB Host:</span>
                    <span class="font-mono text-white"><?= htmlspecialchars($host) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400 font-bold">DB User:</span>
                    <span class="font-mono text-white"><?= htmlspecialchars($username) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400 font-bold">DB Database:</span>
                    <span class="font-mono text-yellow-400"><?= htmlspecialchars($dbname) ?></span>
                </div>
            </div>

            <!-- Admin Credentials -->
            <div class="p-4 rounded-2xl bg-purple-500/10 border border-purple-500/20 text-xs space-y-1">
                <div class="font-bold text-purple-300">👑 Admin Sign-In Credentials (after setup):</div>
                <div class="text-gray-300">URL: <a href="/access" class="text-blue-400 underline font-mono">https://<?= $_SERVER['HTTP_HOST'] ?>/access</a></div>
                <div class="text-gray-300">Email: <strong class="text-white font-mono">admin@lindr.app</strong></div>
                <div class="text-gray-300">Password: <strong class="text-white font-mono">Admin@Lindr2026!</strong></div>
            </div>

            <!-- Actions -->
            <div class="pt-4 flex flex-col sm:flex-row gap-3">
                <a href="setup.php?action=install" class="flex-1 py-3.5 rounded-xl bg-gradient-to-r from-[#FF2D55] to-[#9C27B0] text-center font-bold text-white text-xs shadow-xl hover:opacity-95 transition">
                    ⚡ Run Database Setup & SQL Import Now
                </a>
                <a href="/access" class="py-3.5 px-6 rounded-xl bg-gradient-to-r from-[#00FF87] to-[#60EFFF] text-center font-black text-gray-950 text-xs shadow-lg hover:opacity-95 transition">
                    Go to Admin Portal (/access) →
                </a>
            </div>
        </div>
    </div>
</body>
</html>
