<?php

declare(strict_types=1);

use OCP\Util;

Util::addScript(OCA\ThreeDTilesViewer\AppInfo\Application::APP_ID, OCA\ThreeDTilesViewer\AppInfo\Application::APP_ID . '-main');
Util::addStyle(OCA\ThreeDTilesViewer\AppInfo\Application::APP_ID, OCA\ThreeDTilesViewer\AppInfo\Application::APP_ID . '-main');

?>

<div id="ThreeDTilesViewer"></div>
