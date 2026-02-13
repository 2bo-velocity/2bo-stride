<?php

namespace Stride\Core\Deploy;

use Stride\Core\Support\ExitCode;
use Stride\Core\Support\Logger;
use Stride\Core\Config\Env;

class DeployMode
{
    /**
     * デプロイモードが有効か確認
     * 
     * @return bool
     */
    public static function enabled(): bool
    {
        return Env::get('APP_SAFE_MODE') === 'true';
    }

    /**
     * デプロイモードガード（終了処理付き）
     */
    public static function guardExit(): void
    {
        if (!self::enabled()) {
            return;
        }

        echo "[Stride] Deployment safe mode enabled. Execution skipped.\n";
        Logger::notice("Execution skipped (safe mode)");
        exit(ExitCode::SAFE_MODE);
    }
}
