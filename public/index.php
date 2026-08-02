<?php
/**
 * ToolzyNet - Front Controller
 * All public requests are routed through this single entry point.
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', __DIR__);
define('STORAGE_PATH', BASE_PATH . '/storage');

require BASE_PATH . '/app/Core/bootstrap.php';

use App\Core\App;

$app = new App();
$app->run();
