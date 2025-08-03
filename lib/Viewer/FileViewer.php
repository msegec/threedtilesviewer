<?php
/**
 * 3D Tiles File Viewer for Nextcloud
 *
 * @author Mark Segec
 * @copyright 2025 Mark Segec and the respective contributors
 * @license AGPL-3.0-or-later
 */

namespace OCA\ThreeDTilesViewer\Viewer;

use OCP\Files\File;
use OCP\Files\FileInfo;
use OCP\Files\IAppData;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\Files\SimpleFS\ISimpleFile;
use OCP\Files\SimpleFS\ISimpleFolder;
use OCP\IConfig;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserSession;
use OCP\Preview\IProviderV2;

class FileViewer implements IProviderV2 {
	private $rootFolder;
	private $urlGenerator;
	private $userSession;
	private $config;

	public function __construct(
		IRootFolder $rootFolder,
		IURLGenerator $urlGenerator,
		IUserSession $userSession,
		IConfig $config
	) {
		$this->rootFolder = $rootFolder;
		$this->urlGenerator = $urlGenerator;
		$this->userSession = $userSession;
		$this->config = $config;
	}

	/**
	 * Check if this provider can handle the file
	 */
	public function isAvailable(Node $node): bool {
		if (!$node instanceof File) {
			return false;
		}

		// Check if it's a tileset.json file
		if ($node->getName() !== 'tileset.json') {
			return false;
		}

		// Check file size (reasonable limit for tileset files)
		if ($node->getSize() > 10 * 1024 * 1024) { // 10MB limit
			return false;
		}

		return true;
	}

	/**
	 * Get the viewer template
	 */
	public function getViewerTemplate(Node $node): string {
		return 'viewer';
	}

	/**
	 * Get viewer parameters
	 */
	public function getViewerParameters(Node $node): array {
		$user = $this->userSession->getUser();
		if (!$user) {
			return [];
		}

		$fileId = $node->getId();
		$downloadUrl = $this->urlGenerator->linkToRoute('files.viewcontroller.showFile', [
			'fileId' => $fileId
		]);

		return [
			'fileId' => $fileId,
			'fileName' => $node->getName(),
			'fileSize' => $node->getSize(),
			'downloadUrl' => $downloadUrl,
			'canRead' => $node->isReadable(),
			'userId' => $user->getUID()
		];
	}

	/**
	 * Get the viewer URL
	 */
	public function getViewerUrl(Node $node): string {
		return $this->urlGenerator->linkToRoute('ThreeDTilesViewer.viewer.index', [
			'fileId' => $node->getId()
		]);
	}

	/**
	 * Get the viewer icon
	 */
	public function getViewerIcon(): string {
		return 'icon-3d-tiles';
	}

	/**
	 * Get the viewer name
	 */
	public function getViewerName(): string {
		return '3D Tiles Viewer';
	}

	/**
	 * Get the viewer description
	 */
	public function getViewerDescription(): string {
		return 'View 3D Tiles files with an interactive 3D viewer';
	}
} 