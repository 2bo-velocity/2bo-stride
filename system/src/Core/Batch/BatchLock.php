<?php

namespace Stride\Core\Batch;

class BatchLock
{
    private $fp;

    /**
     * Lockを取得（非ブロッキング）
     * 
     * @param string $name バッチ識別名
     * @return self|null Lock取得成功時はインスタンス、失敗時はnull
     */
    public static function acquire(string $name): ?self
    {
        $file = sys_get_temp_dir() . '/stride_batch_' . md5($name) . '.lock';

        $fp = fopen($file, 'c');
        if (!$fp) {
            return null;
        }

        // 非ブロッキングで排他ロック取得
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            fclose($fp);
            return null;
        }

        $lock = new self();
        $lock->fp = $fp;

        return $lock;
    }

    /**
     * Lockを解放
     */
    public function release(): void
    {
        if ($this->fp) {
            flock($this->fp, LOCK_UN);
            fclose($this->fp);
        }
    }
}
