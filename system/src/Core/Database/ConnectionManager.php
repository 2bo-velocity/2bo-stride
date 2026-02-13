<?php

namespace Stride\Core\Database;

use PDO;
use RuntimeException;

class ConnectionManager
{
    private array $config;
    private ?PDO $masterConn = null;
    private ?PDO $slaveConn = null;
    private ?array $selectedSlave = null;
    private ?int $stickyMasterUntil = null;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function master(): PDO
    {
        if (!$this->masterConn) {
            $this->masterConn = $this->connect($this->config['master']);
        }
        return $this->masterConn;
    }

    public function slave(): PDO
    {
        if ($this->shouldUseMaster()) {
            return $this->master();
        }

        if (!$this->slaveConn) {
            $slaveConfig = $this->selectSlave();
            $this->slaveConn = $this->connect($slaveConfig);
        }
        return $this->slaveConn;
    }

    public function connection(): PDO
    {
        return $this->slave(); // Default to slave (or master if sticky/transaction)
    }

    public function markWrite(): void
    {
        $seconds = $this->config['sticky_master_seconds'] ?? 3;
        
        // Only set if not already set or expired, to extend (or not, per spec)
        // Spec says: "First time only set (do not extend)"
        if ($this->stickyMasterUntil === null || $this->stickyMasterUntil < time()) {
            $this->stickyMasterUntil = time() + $seconds;
            // Ideally store this in session or cookie for sticky session across requests
            // For now, in-memory for this request flow
            if (isset($_SESSION)) {
                 $_SESSION['sticky_master_until'] = $this->stickyMasterUntil;
            }
        }
    }

    private function shouldUseMaster(): bool
    {
        // Check transaction status on existing master connection
        if ($this->masterConn && $this->masterConn->inTransaction()) {
            return true;
        }

        // Check sticky session
        if (isset($_SESSION['sticky_master_until'])) {
            if ($_SESSION['sticky_master_until'] > time()) {
                return true;
            }
        }
        
        return false;
    }

    private function selectSlave(): array
    {
        if ($this->selectedSlave) {
            return $this->selectedSlave;
        }

        $slaves = $this->config['slaves'] ?? [];
        if (empty($slaves)) {
            return $this->config['master']; // Fallback to master if no slaves
        }

        $weights = array_column($slaves, 'weight');
        $totalWeight = array_sum($weights);
        $rand = mt_rand(1, $totalWeight);

        $cumulative = 0;
        foreach ($slaves as $slave) {
            $cumulative += $slave['weight'];
            if ($rand <= $cumulative) {
                $this->selectedSlave = $slave;
                return $slave;
            }
        }

        return $slaves[0];
    }

    private function connect(array $config): PDO
    {
        $dsn = "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
        
        try {
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return $pdo;
        } catch (\PDOException $e) {
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }
}
