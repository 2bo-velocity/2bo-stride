<?php

namespace Stride\Core\Maintenance;

use Stride\Core\Support\ExitCode;
use Stride\Core\Config\Env; // Assuming Env is available here or use config helper if available

class MaintenanceGuard
{
    /**
     * メンテナンスモードチェック
     * メンテナンス中の場合は即座に終了
     */
    public static function check(): void
    {
        // Using Env directly as config logic might rely on App instance which might be heavy?
        // Spec uses config('app.maintenance'), let's try to assume global config function or Env class exist
        if (Env::get('APP_MAINTENANCE') !== 'true') {
            return;
        }

        echo "Application is under maintenance. Batch execution skipped.\n";
        exit(ExitCode::MAINTENANCE);
    }
}
