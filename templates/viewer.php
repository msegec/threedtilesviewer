<?php
/**
 * 3D Tiles Viewer Template
 *
 * @author Your Name
 * @copyright 2024 Your Name
 * @license AGPL-3.0-or-later
 */

use OCP\Util;

// Add required scripts and styles
Util::addScript('3d_tiles_viewer', 'viewer');
Util::addStyle('3d_tiles_viewer', 'viewer');
?>

<div id="3d-tiles-viewer" class="nc-3d-tiles-viewer">
    <!-- Nextcloud Header Integration -->
    <div class="nc-header">
        <div class="nc-header-left">
            <h2><?php p($l->t('3D Tiles Viewer')); ?></h2>
        </div>
        <div class="nc-header-right">
            <button id="file-selector" class="primary">
                <?php p($l->t('Select Tileset')); ?>
            </button>
        </div>
    </div>

    <!-- File Browser Integration -->
    <div id="file-browser" class="nc-file-browser" style="display: none;">
        <div class="file-browser-header">
            <h3><?php p($l->t('Select a tileset.json file')); ?></h3>
            <button id="close-browser" class="icon-close"></button>
        </div>
        <div id="file-list" class="file-list">
            <!-- Files will be loaded here -->
        </div>
    </div>

    <!-- Main Viewer Container -->
    <div id="viewer-container" class="nc-viewer-container">
        <!-- Loading overlay -->
        <div id="loading" class="nc-loading">
            <div class="loading-content">
                <h3><?php p($l->t('Loading 3D Tiles...')); ?></h3>
                <div class="progress-bar">
                    <div class="progress-fill" id="progress"></div>
                </div>
                <p><?php p($l->t('Initializing WGS84 viewer with KTX2/DRACO support...')); ?></p>
            </div>
        </div>

        <!-- Error overlay -->
        <div id="error" class="nc-error" style="display: none;">
            <div class="error-content">
                <h3><?php p($l->t('Error Loading Tiles')); ?></h3>
                <p id="error-message"></p>
                <button onclick="location.reload()" class="primary">
                    <?php p($l->t('Retry')); ?>
                </button>
            </div>
        </div>

        <!-- Canvas container -->
        <div id="canvas-container" class="nc-canvas-container"></div>

        <!-- Info panel -->
        <div id="info" class="nc-panel mobile-optimized">
            <div class="panel-header">
                <h3><?php p($l->t('3D Tiles Viewer')); ?></h3>
                <span class="toggle-icon">▼</span>
            </div>
            <div class="panel-content">
                <p id="file-info"><strong><?php p($l->t('Data:')); ?></strong> <span id="data-name">-</span></p>
                <p><strong><?php p($l->t('Format:')); ?></strong> Cesium 3D Tiles 1.1</p>
                <p><strong><?php p($l->t('Coordinate System:')); ?></strong> WGS84</p>
                <p id="coordinates"></p>
                <p id="stats"></p>
            </div>
        </div>

        <!-- Rotation controls -->
        <div id="rotation-controls" class="nc-panel mobile-optimized">
            <div class="panel-header">
                <div class="tooltip">
                    <strong><?php p($l->t('Rotation Controls')); ?></strong>
                    <span class="tooltiptext"><?php p($l->t('Adjust model rotation to compensate for coordinate system. X-axis rotation is pre-set to -45° to level the model.')); ?></span>
                </div>
                <span class="toggle-icon">▼</span>
            </div>
            <div class="panel-content">
                <div class="control">
                    <label><?php p($l->t('Enable:')); ?></label>
                    <input type="checkbox" id="rotation-enabled" checked>
                </div>
                <div class="control">
                    <label><?php p($l->t('X Rotation:')); ?></label>
                    <input type="range" id="rotation-x" min="-180" max="180" step="1" value="35">
                    <input type="number" id="rotation-x-value" min="-180" max="180" step="1" value="35">
                </div>
                <div class="control">
                    <label><?php p($l->t('Y Rotation:')); ?></label>
                    <input type="range" id="rotation-y" min="-180" max="180" step="1" value="-21">
                    <input type="number" id="rotation-y-value" min="-180" max="180" step="1" value="-21">
                </div>
                <div class="control">
                    <label><?php p($l->t('Z Rotation:')); ?></label>
                    <input type="range" id="rotation-z" min="-180" max="180" step="1" value="-11">
                    <input type="number" id="rotation-z-value" min="-180" max="180" step="1" value="-11">
                </div>
                <div class="control">
                    <button id="reset-rotation"><?php p($l->t('Reset Rotation')); ?></button>
                </div>
            </div>
        </div>

        <!-- Controls panel -->
        <div id="controls" class="nc-panel mobile-optimized">
            <div class="panel-header">
                <strong><?php p($l->t('Controls')); ?></strong>
                <span class="toggle-icon">▼</span>
            </div>
            <div class="panel-content">
                • <?php p($l->t('Left click + drag: Rotate')); ?><br>
                • <?php p($l->t('Right click + drag: Pan')); ?><br>
                • <?php p($l->t('Mouse wheel: Zoom')); ?><br>
                • <?php p($l->t('Press \'R\' key: Reset view')); ?><br>
                • <?php p($l->t('Touch: Pinch/drag to navigate')); ?>
            </div>
        </div>

        <!-- Perspective controls -->
        <div id="perspective-controls" class="nc-panel mobile-optimized">
            <div class="panel-header">
                <strong><?php p($l->t('Perspective Controls')); ?></strong>
                <span class="toggle-icon">▼</span>
            </div>
            <div class="panel-content">
                <div class="control">
                    <label><?php p($l->t('FOV:')); ?></label>
                    <input type="range" id="fov-slider" min="10" max="120" step="1" value="45">
                    <input type="number" id="fov-value" min="10" max="120" step="1" value="45">
                </div>
            </div>
        </div>

        <!-- Offset controls -->
        <div id="offset-controls" class="nc-panel mobile-optimized">
            <div class="panel-header">
                <strong><?php p($l->t('Offset Controls')); ?></strong>
                <span class="toggle-icon">▼</span>
            </div>
            <div class="panel-content">
                <div class="control">
                    <label><?php p($l->t('X Offset:')); ?></label>
                    <input type="range" id="offset-x" min="-500" max="500" step="1" value="0">
                    <input type="number" id="offset-x-value" min="-500" max="500" step="1" value="0">
                </div>
                <div class="control">
                    <label><?php p($l->t('Y Offset:')); ?></label>
                    <input type="range" id="offset-y" min="-500" max="500" step="1" value="0">
                    <input type="number" id="offset-y-value" min="-500" max="500" step="1" value="0">
                </div>
                <div class="control">
                    <label><?php p($l->t('Z Offset:')); ?></label>
                    <input type="range" id="offset-z" min="-500" max="500" step="1" value="0">
                    <input type="number" id="offset-z-value" min="-500" max="500" step="1" value="0">
                </div>
                <div class="control">
                    <button id="reset-offset"><?php p($l->t('Reset Offset')); ?></button>
                </div>
            </div>
        </div>

        <!-- Lighting controls -->
        <div id="lighting-controls" class="nc-panel mobile-optimized">
            <div class="panel-header">
                <strong><?php p($l->t('Lighting Controls')); ?></strong>
                <span class="toggle-icon">▼</span>
            </div>
            <div class="panel-content">
                <div class="light-control">
                    <label><?php p($l->t('Ambient:')); ?></label>
                    <input type="range" id="ambient-intensity" min="0" max="2" step="0.1" value="2">
                    <input type="number" id="ambient-value" min="0" max="2" step="0.1" value="2">
                </div>
                <div class="light-control">
                    <label><?php p($l->t('Directional:')); ?></label>
                    <input type="range" id="directional-intensity" min="0" max="3" step="0.1" value="0">
                    <input type="number" id="directional-value" min="0" max="3" step="0.1" value="0">
                </div>
                <div class="light-control">
                    <label><?php p($l->t('Soft Shadows:')); ?></label>
                    <input type="checkbox" id="soft-shadows" checked>
                </div>
                <div class="light-control">
                    <label><?php p($l->t('Fog:')); ?></label>
                    <input type="range" id="fog-density" min="0" max="0.01" step="0.001" value="0.0003">
                    <input type="number" id="fog-value" min="0" max="0.01" step="0.001" value="0.0003">
                </div>
            </div>
        </div>

        <!-- Click tool -->
        <div id="click-tool" class="nc-panel mobile-optimized">
            <div class="panel-header">
                <strong><?php p($l->t('Click Tool')); ?></strong>
                <span class="toggle-icon">▼</span>
            </div>
            <div class="panel-content">
                <button id="toggle-click"><?php p($l->t('Enable Click to Center')); ?></button><br>
                <span id="click-status"><?php p($l->t('Click tool disabled')); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Click instructions overlay -->
<div class="click-instructions" id="click-instructions" style="display: none;">
    <h3><?php p($l->t('Click to Center Tool Active')); ?></h3>
    <p><?php p($l->t('Click anywhere on the 3D model to set that point as the center.')); ?></p>
    <p><?php p($l->t('The camera will reposition to focus on your selected point.')); ?></p>
    <button onclick="disableClickTool()"><?php p($l->t('Cancel')); ?></button>
</div>

<!-- Light visualization overlay -->
<div class="light-visualization" id="light-visualization" style="display: none;">
    <h3><?php p($l->t('Light Positioning Tool Active')); ?></h3>
    <p><?php p($l->t('Click anywhere on the 3D model to position the light at that point.')); ?></p>
    <p><?php p($l->t('Use the offset controls to fine-tune the light position.')); ?></p>
    <button onclick="disableLightPositioning()"><?php p($l->t('Cancel')); ?></button>
</div>

<script>
// Nextcloud integration variables
window.NC_3D_TILES_VIEWER = {
    appName: '<?php p($appName); ?>',
    userId: '<?php p($userId ?? ''); ?>',
    fileId: '<?php p($fileId ?? ''); ?>',
    downloadUrl: '<?php p($downloadUrl ?? ''); ?>',
    canRead: <?php echo json_encode($canRead ?? false); ?>,
    translations: {
        'Select Tileset': '<?php p($l->t('Select Tileset')); ?>',
        'Loading 3D Tiles...': '<?php p($l->t('Loading 3D Tiles...')); ?>',
        'Error Loading Tiles': '<?php p($l->t('Error Loading Tiles')); ?>',
        'Retry': '<?php p($l->t('Retry')); ?>',
        '3D Tiles Viewer': '<?php p($l->t('3D Tiles Viewer')); ?>',
        'Data:': '<?php p($l->t('Data:')); ?>',
        'Format:': '<?php p($l->t('Format:')); ?>',
        'Coordinate System:': '<?php p($l->t('Coordinate System:')); ?>',
        'Rotation Controls': '<?php p($l->t('Rotation Controls')); ?>',
        'Enable:': '<?php p($l->t('Enable:')); ?>',
        'X Rotation:': '<?php p($l->t('X Rotation:')); ?>',
        'Y Rotation:': '<?php p($l->t('Y Rotation:')); ?>',
        'Z Rotation:': '<?php p($l->t('Z Rotation:')); ?>',
        'Reset Rotation': '<?php p($l->t('Reset Rotation')); ?>',
        'Controls': '<?php p($l->t('Controls')); ?>',
        'Left click + drag: Rotate': '<?php p($l->t('Left click + drag: Rotate')); ?>',
        'Right click + drag: Pan': '<?php p($l->t('Right click + drag: Pan')); ?>',
        'Mouse wheel: Zoom': '<?php p($l->t('Mouse wheel: Zoom')); ?>',
        'Press \'R\' key: Reset view': '<?php p($l->t('Press \'R\' key: Reset view')); ?>',
        'Touch: Pinch/drag to navigate': '<?php p($l->t('Touch: Pinch/drag to navigate')); ?>',
        'Perspective Controls': '<?php p($l->t('Perspective Controls')); ?>',
        'FOV:': '<?php p($l->t('FOV:')); ?>',
        'Offset Controls': '<?php p($l->t('Offset Controls')); ?>',
        'X Offset:': '<?php p($l->t('X Offset:')); ?>',
        'Y Offset:': '<?php p($l->t('Y Offset:')); ?>',
        'Z Offset:': '<?php p($l->t('Z Offset:')); ?>',
        'Reset Offset': '<?php p($l->t('Reset Offset')); ?>',
        'Lighting Controls': '<?php p($l->t('Lighting Controls')); ?>',
        'Ambient:': '<?php p($l->t('Ambient:')); ?>',
        'Directional:': '<?php p($l->t('Directional:')); ?>',
        'Soft Shadows:': '<?php p($l->t('Soft Shadows:')); ?>',
        'Fog:': '<?php p($l->t('Fog:')); ?>',
        'Click Tool': '<?php p($l->t('Click Tool')); ?>',
        'Enable Click to Center': '<?php p($l->t('Enable Click to Center')); ?>',
        'Click tool disabled': '<?php p($l->t('Click tool disabled')); ?>',
        'Click to Center Tool Active': '<?php p($l->t('Click to Center Tool Active')); ?>',
        'Click anywhere on the 3D model to set that point as the center.': '<?php p($l->t('Click anywhere on the 3D model to set that point as the center.')); ?>',
        'The camera will reposition to focus on your selected point.': '<?php p($l->t('The camera will reposition to focus on your selected point.')); ?>',
        'Cancel': '<?php p($l->t('Cancel')); ?>',
        'Light Positioning Tool Active': '<?php p($l->t('Light Positioning Tool Active')); ?>',
        'Click anywhere on the 3D model to position the light at that point.': '<?php p($l->t('Click anywhere on the 3D model to position the light at that point.')); ?>',
        'Use the offset controls to fine-tune the light position.': '<?php p($l->t('Use the offset controls to fine-tune the light position.')); ?>'
    }
};
</script> 