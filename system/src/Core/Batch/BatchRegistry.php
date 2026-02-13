<?php

namespace Stride\Core\Batch;

use RuntimeException;

class BatchRegistry
{
    private array $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * バッチ名からクラス名を解決
     * 
     * @param string $name バッチ名
     * @return string バッチクラスのFQCN
     * @throws RuntimeException バッチが見つからない場合
     */
    public function resolve(string $name): string
    {
        if (!isset($this->map[$name])) {
            throw new RuntimeException("Batch not found: " . $name);
        }
        return $this->map[$name];
    }

    /**
     * 登録済みバッチ一覧を取得
     * 
     * @return array バッチ名の配列
     */
    public function list(): array
    {
        return array_keys($this->map);
    }
}
