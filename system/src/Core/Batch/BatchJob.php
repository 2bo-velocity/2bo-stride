<?php

namespace Stride\Core\Batch;

interface BatchJob
{
    /**
     * バッチ処理実行
     * 
     * @return int Exit code (0=成功, 1=エラー)
     */
    public function handle(): int;
}
