<?php
/**
 * 3D Tiles Viewer App for Nextcloud
 *
 * @author Mark Segec
 * @copyright 2025 Mark Segec and the respective contributors
 * @license AGPL-3.0-or-later
 */

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Util;

	return [
	'resources' => [
			'ThreeDTilesViewer' => [
		'url' => '/apps/ThreeDTilesViewer/',
			'icon' => 'icon-3d-tiles',
			'name' => '3D Tiles Viewer',
			'order' => 10,
		],
	],
]; 