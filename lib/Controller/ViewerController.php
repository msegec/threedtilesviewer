<?php
/**
 * 3D Tiles Viewer Controller
 *
 * @author Your Name
 * @copyright 2024 Your Name
 * @license AGPL-3.0-or-later
 */

namespace OCA\ThreeDTilesViewer\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\ISession;
use OCP\IUserSession;
use Exception;

class ViewerController extends Controller {
	private $urlGenerator;
	private $rootFolder;
	private $session;
	private $userSession;

	public function __construct(
		$appName,
		IRequest $request,
		IURLGenerator $urlGenerator,
		IRootFolder $rootFolder,
		ISession $session,
		IUserSession $userSession
	) {
		parent::__construct($appName, $request);
		$this->urlGenerator = $urlGenerator;
		$this->rootFolder = $rootFolder;
		$this->session = $session;
		$this->userSession = $userSession;
	}

	/**
	 * Show the 3D tiles viewer
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 */
	public function index(): TemplateResponse {
		return new TemplateResponse('ThreeDTilesViewer', 'viewer', [
			'appName' => 'ThreeDTilesViewer',
			'urlGenerator' => $this->urlGenerator
		]);
	}

	/**
	 * Get tileset information
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 */
	public function getTilesetInfo(string $fileId): DataResponse {
		try {
			$node = $this->getFileNode($fileId);
			
			if (!$node) {
				return new DataResponse(['error' => 'File not found'], 404);
			}

			if (!$node->isReadable()) {
				return new DataResponse(['error' => 'Permission denied'], 403);
			}

			// Check if it's a tileset.json file
			if ($node->getName() !== 'tileset.json') {
				return new DataResponse(['error' => 'Not a valid tileset file'], 400);
			}

			$content = $node->getContent();
			$tilesetData = json_decode($content, true);

			if (!$tilesetData) {
				return new DataResponse(['error' => 'Invalid tileset JSON'], 400);
			}

			// Generate direct download URL for the tileset
			$downloadUrl = $this->urlGenerator->linkToRoute('files.viewcontroller.showFile', [
				'fileId' => $fileId
			]);

			return new DataResponse([
				'tileset' => $tilesetData,
				'downloadUrl' => $downloadUrl,
				'fileName' => $node->getName(),
				'fileSize' => $node->getSize()
			]);

		} catch (NotFoundException $e) {
			return new DataResponse(['error' => 'File not found'], 404);
		} catch (NotPermittedException $e) {
			return new DataResponse(['error' => 'Permission denied'], 403);
		} catch (Exception $e) {
			return new DataResponse(['error' => 'Server error'], 500);
		}
	}

	/**
	 * Get file download URL
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 */
	public function getFileUrl(string $fileId): DataResponse {
		try {
			$node = $this->getFileNode($fileId);
			
			if (!$node || !$node->isReadable()) {
				return new DataResponse(['error' => 'File not accessible'], 404);
			}

			$downloadUrl = $this->urlGenerator->linkToRoute('files.viewcontroller.showFile', [
				'fileId' => $fileId
			]);

			return new DataResponse([
				'url' => $downloadUrl,
				'fileName' => $node->getName()
			]);

		} catch (Exception $e) {
			return new DataResponse(['error' => 'Server error'], 500);
		}
	}

	/**
	 * Get file node - works for both logged-in users and public shares
	 */
	private function getFileNode(string $fileId): ?Node {
		$user = $this->userSession->getUser();
		
		if ($user) {
			// Logged-in user - try to get from user folder
			try {
				$userFolder = $this->rootFolder->getUserFolder($user->getUID());
				$nodes = $userFolder->getById($fileId);
				return $nodes[0] ?? null;
			} catch (\Exception $e) {
				// User folder not accessible, try public share
			}
		}

		// For public shares or when user folder fails, try to get by file ID directly
		// This works for public shared folders
		try {
			$nodes = $this->rootFolder->getById($fileId);
			return $nodes[0] ?? null;
		} catch (Exception $e) {
			return null;
		}
	}
} 