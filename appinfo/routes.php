<?php
/**
 * 3D Tiles Viewer App for Nextcloud
 *
 * @author Mark Segec
 * @copyright 2025 Mark Segec and the respective contributors
 * @license AGPL-3.0-or-later
 */

return [
	'routes' => [
		// Viewer routes
		['name' => 'ThreeDTilesViewer#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'ThreeDTilesViewer#getTilesetInfo', 'url' => '/tileset/{fileId}', 'verb' => 'GET'],
		['name' => 'ThreeDTilesViewer#getFileUrl', 'url' => '/file/{fileId}', 'verb' => 'GET'],
	]
]; 