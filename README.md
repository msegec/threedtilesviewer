# 3D Tiles Viewer for Nextcloud

A Nextcloud app that allows users to view 3D Tiles files directly within their Nextcloud instance using an interactive 3D viewer.
PLEASE NOTE: currently only tested with metashape cesium 1.1 tiles.

## Features

- **File Browser Integration**: Browse and select tileset.json files from your Nextcloud storage
- **Interactive 3D Viewer**: Full-featured 3D viewer with camera controls, lighting, and model manipulation
- **Mobile Support**: Responsive design that works on desktop and mobile devices
- **Permission Integration**: Respects Nextcloud's file sharing and permission system
- **User Settings**: Store viewer preferences per user
- **WGS84 Coordinate System**: Support for geographic coordinate systems
- **KTX2/DRACO Support**: Advanced texture and geometry compression

## Screenshots

![3D Tiles Viewer in Nextcloud](screenshots/viewer.png)

## Requirements

- Nextcloud 31.x
- PHP 8.1+
- Node.js 16.0+
- npm 8.0+
- Modern web browser with WebGL support
- 3D Tiles files (tileset.json format)

## Installation

### Quick Setup (Recommended)

1. **Run the setup script**:
   ```bash
   # On Linux/macOS:
   ./setup.sh
   
   # On Windows:
   setup.bat
   ```

2. **Copy to Nextcloud apps directory**:
   ```bash
   cp -r /path/to/nc-3dtiles-vwr /var/www/nextcloud/apps/3d_tiles_viewer
   ```

3. **Set permissions**:
   ```bash
   chown -R www-data:www-data /var/www/nextcloud/apps/3d_tiles_viewer
   chmod -R 755 /var/www/nextcloud/apps/3d_tiles_viewer
   ```

4. **Enable the app** in Nextcloud Admin → Apps → 3D Tiles Viewer

### Manual Installation

For detailed manual installation instructions, see [INSTALLATION.md](INSTALLATION.md).

### Method 1: Manual Installation

1. Download the latest release from the [releases page](https://github.com/msegec/nc-3dtiles-vwr/releases)
2. Extract the archive to your Nextcloud `apps` directory:
   ```bash
   cd /path/to/nextcloud/apps
   unzip nc-3dtiles-vwr.zip
   ```
3. Rename the extracted folder to `3d_tiles_viewer`
4. Install dependencies:
   ```bash
   cd 3d_tiles_viewer
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```
5. Enable the app in Nextcloud Admin → Apps → 3D Tiles Viewer

### Method 2: Git Installation

```bash
cd /path/to/nextcloud/apps
git clone https://github.com/msegec/nc-3dtiles-vwr.git 3d_tiles_viewer
cd 3d_tiles_viewer
./setup.sh  # or setup.bat on Windows
```

### Method 3: App Store (Future)

The app will be available in the Nextcloud App Store once approved.

## Usage

### Basic Usage

1. **Upload 3D Tiles**: Upload your 3D Tiles files to your Nextcloud storage
2. **Navigate to App**: Go to the 3D Tiles Viewer app from the Nextcloud navigation
3. **Select Tileset**: Click "Select Tileset" to browse your files
4. **Load and View**: Select a `tileset.json` file to load it in the 3D viewer

### Viewer Controls

- **Mouse/Touch Controls**:
  - Left click + drag: Rotate camera
  - Right click + drag: Pan camera
  - Mouse wheel: Zoom in/out
  - Touch: Pinch/drag to navigate

- **Keyboard Controls**:
  - `R` key: Reset view to default position

- **Control Panels**:
  - **Info Panel**: Shows file information and statistics
  - **Rotation Controls**: Adjust model rotation for coordinate system compensation
  - **Perspective Controls**: Adjust field of view
  - **Offset Controls**: Fine-tune model position
  - **Lighting Controls**: Adjust ambient and directional lighting
  - **Click Tool**: Click on the model to center the camera

### File Format Support

The viewer supports Cesium 3D Tiles format:
- `tileset.json` files (main tileset definition)
- Associated tile files (`.b3dm`, `.i3dm`, `.pnts`)
- Texture files (`.ktx2`, `.jpg`, `.png`)
- Geometry files (`.draco` compressed)

## Configuration

### Admin Settings

No special admin configuration is required. The app works out of the box.

### User Settings

Users can customize their viewing experience through the control panels:
- Lighting preferences
- Camera settings
- Model positioning
- UI layout preferences

## Development

### Prerequisites

- Nextcloud development environment
- PHP 8.0+
- Node.js (for building assets)
- Composer

### Setup Development Environment

1. **Clone the repository**:
   ```bash
   git clone https://github.com/msegec/nc-3dtiles-vwr.git
   cd nc-3dtiles-vwr
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Build assets**:
   ```bash
   npm run build
   ```

4. **Link to Nextcloud**:
   ```bash
   ln -s /path/to/nc-3dtiles-vwr /path/to/nextcloud/apps/3d_tiles_viewer
   ```

### Project Structure

```
nc-3dtiles-vwr/
├── appinfo/                 # App metadata and configuration
│   ├── info.xml            # App information
│   └── navigation.php      # Navigation integration
├── lib/                    # PHP backend classes
│   ├── Controller/         # API controllers
│   ├── Service/           # Business logic services
│   └── Viewer/            # File viewer integration
├── templates/              # PHP templates
│   └── viewer.php         # Main viewer template
├── js/                    # JavaScript files
│   ├── viewer.js          # Main viewer logic
│   └── import-map.js      # Module imports
├── css/                   # Stylesheets
│   └── viewer.css         # Viewer styles
├── img/                   # Images and icons
└── README.md             # This file
```

### API Endpoints

The app provides the following API endpoints:

- `GET /apps/3d_tiles_viewer/api/v1/tileset/{fileId}`: Get tileset information
- `GET /apps/3d_tiles_viewer/api/v1/file/{fileId}`: Get file download URL

### Building for Production

```bash
# Build JavaScript and CSS
npm run build

# Create release package
composer install --no-dev
```

## Troubleshooting

### Common Issues

1. **"No tileset.json files found"**
   - Ensure you have uploaded `tileset.json` files to your Nextcloud
   - Check file permissions (read access required)

2. **"Error loading tiles"**
   - Verify the tileset.json file is valid
   - Check that all referenced tile files are accessible
   - Ensure your browser supports WebGL

3. **"Permission denied"**
   - Check file sharing settings in Nextcloud
   - Verify user has read access to the files

4. **Performance issues**
   - Large tilesets may require more memory
   - Consider using smaller tileset files
   - Check browser console for specific errors

### Browser Compatibility

- **Chrome/Edge**: Full support
- **Firefox**: Full support
- **Safari**: Full support (macOS 10.15+)
- **Mobile browsers**: Limited support (basic viewing)

### Debug Mode

Enable debug mode in Nextcloud to see detailed error messages:

```php
// config/config.php
'debug' => true,
'loglevel' => 0,
```

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Guidelines

1. Follow Nextcloud coding standards
2. Write tests for new features
3. Update documentation
4. Test on multiple browsers
5. Ensure mobile compatibility

### Testing

```bash
# Run PHP tests
composer test

# Run JavaScript tests
npm test

# Run integration tests
composer test:integration
```

## License

This project is licensed under the AGPL-3.0 License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- [Three.js](https://threejs.org/) - 3D graphics library
- [3D Tiles Renderer](https://github.com/NASA-AMMOS/3DTilesRendererJS) - 3D Tiles rendering
- [Nextcloud](https://nextcloud.com/) - File hosting platform

## Support

- **Issues**: [GitHub Issues](https://github.com/msegec/nc-3dtiles-vwr/issues)
- **Documentation**: [Wiki](https://github.com/msegec/nc-3dtiles-vwr/wiki)
- **Discussions**: [GitHub Discussions](https://github.com/msegec/nc-3dtiles-vwr/discussions)

## Changelog

### Version 1.0.0 (2024-01-XX)

- Initial release
- Basic 3D Tiles viewer functionality
- Nextcloud file browser integration
- Mobile responsive design
- User control panels
- WGS84 coordinate system support

## Roadmap

- [ ] Support for additional 3D formats
- [ ] Collaborative viewing features
- [ ] Advanced lighting and rendering options
- [ ] Export and sharing capabilities
- [ ] Integration with Nextcloud Maps
- [ ] VR/AR support
- [ ] Offline viewing capabilities 