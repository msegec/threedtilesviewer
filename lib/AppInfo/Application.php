<?php
/**
 * Three Tiles Viewer App for Nextcloud
 *
 * @author Mark Segec
 * @copyright 2025 Mark Segec and the respective contributors
 * @license AGPL-3.0-or-later
 */

namespace OCA\ThreeDTilesViewer\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Util;
use OCA\ThreeDTilesViewer\Viewer\FileViewer;

class Application extends App implements IBootstrap {
	public const APP_ID = 'ThreeDTilesViewer';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		// Register the viewer as a file viewer
		$context->registerFileViewer(FileViewer::class);
	}

	public function boot(IBootContext $context): void {
		// Add script and style files
		Util::addScript(self::APP_ID, 'viewer');
		Util::addStyle(self::APP_ID, 'viewer');
		
		// Register MIME type for tileset.json files
		Util::addScript(self::APP_ID, 'mime');
	}
} 