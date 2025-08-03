# Nextcloud 3D Tiles Viewer - Deployment Guide

This guide provides multiple methods to deploy the 3D Tiles Viewer app to Nextcloud, addressing common installation issues.

## 🚨 Common Installation Issues

### Why Manual Installation Fails

1. **Missing Dependencies**: The app requires specific PHP extensions and Node.js dependencies
2. **Permission Issues**: Incorrect file ownership and permissions
3. **Cache Problems**: Nextcloud cache not cleared after installation
4. **Version Conflicts**: Incompatible Nextcloud or PHP versions
5. **Missing Files**: Incomplete app structure or missing critical files

## 📋 Prerequisites Check

Before deployment, verify your environment:

```bash
# Check Nextcloud version
php occ status

# Check PHP version (must be 8.1+)
php --version

# Check Node.js version (must be 16.0+)
node --version

# Check npm version (must be 8.0+)
npm --version

# Check Composer
composer --version
```

## 🛠️ Method 1: Automated Deployment (Recommended)

### Step 1: Prepare the App

```bash
# Clone or download the app
git clone https://github.com/msegec/nc-3dtiles-vwr.git
cd nc-3dtiles-vwr

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Verify the app structure
ls -la appinfo/
ls -la lib/
ls -la js/
ls -la css/
```

### Step 2: Deploy to Nextcloud

```bash
# Navigate to Nextcloud apps directory
cd /path/to/your/nextcloud/apps

# Copy the app (rename to match Nextcloud conventions)
cp -r /path/to/nc-3dtiles-vwr 3d_tiles_viewer

# Set correct ownership (replace www-data with your web server user)
sudo chown -R www-data:www-data 3d_tiles_viewer

# Set correct permissions
sudo chmod -R 755 3d_tiles_viewer
sudo chmod -R 644 3d_tiles_viewer/appinfo/*.xml
sudo chmod -R 644 3d_tiles_viewer/appinfo/*.php
```

### Step 3: Clear Nextcloud Cache

```bash
# Navigate to Nextcloud root
cd /path/to/your/nextcloud

# Clear all caches
php occ files:scan --all
php occ files:cleanup
php occ cache:clear
```

### Step 4: Enable the App

```bash
# Enable via CLI (recommended)
php occ app:enable 3d_tiles_viewer

# Verify installation
php occ app:list | grep 3d_tiles_viewer
```

## 🔧 Method 2: Manual Installation with Troubleshooting

### Step 1: Verify App Structure

Ensure your app has all required files:

```bash
# Required directory structure
nc-3dtiles-vwr/
├── appinfo/
│   ├── info.xml          # ✅ Must exist
│   ├── navigation.php    # ✅ Must exist
│   └── routes.php        # ✅ Must exist
├── lib/
│   ├── AppInfo/
│   │   └── Application.php
│   ├── Controller/
│   │   └── ViewerController.php
│   └── Viewer/
│       └── FileViewer.php
├── js/
│   ├── viewer.js
│   └── mime.js
├── css/
│   └── viewer.css
└── templates/
    └── viewer.php
```

### Step 2: Check File Permissions

```bash
# Set proper ownership
sudo chown -R www-data:www-data /path/to/nextcloud/apps/3d_tiles_viewer

# Set directory permissions
sudo find /path/to/nextcloud/apps/3d_tiles_viewer -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /path/to/nextcloud/apps/3d_tiles_viewer -type f -exec chmod 644 {} \;

# Make scripts executable
sudo chmod +x /path/to/nextcloud/apps/3d_tiles_viewer/setup.sh
```

### Step 3: Verify Nextcloud Configuration

Check your Nextcloud config:

```bash
# Check Nextcloud config
php occ config:list system

# Check app directory
php occ config:system:get apps_paths
```

## 🚨 Method 3: Emergency Deployment (When All Else Fails)

### Step 1: Complete Reset

```bash
# Stop web server
sudo systemctl stop apache2  # or nginx

# Backup current apps
sudo cp -r /path/to/nextcloud/apps /path/to/nextcloud/apps.backup

# Clear all caches
sudo rm -rf /path/to/nextcloud/data/cache/*
sudo rm -rf /path/to/nextcloud/data/tmp/*
```

### Step 2: Fresh Installation

```bash
# Create fresh app directory
sudo mkdir -p /path/to/nextcloud/apps/3d_tiles_viewer

# Copy app files
sudo cp -r /path/to/nc-3dtiles-vwr/* /path/to/nextcloud/apps/3d_tiles_viewer/

# Set permissions
sudo chown -R www-data:www-data /path/to/nextcloud/apps/3d_tiles_viewer
sudo chmod -R 755 /path/to/nextcloud/apps/3d_tiles_viewer
```

### Step 3: Restart Services

```bash
# Start web server
sudo systemctl start apache2  # or nginx

# Clear Nextcloud cache
cd /path/to/nextcloud
php occ cache:clear
php occ files:scan --all
```

## 🔍 Troubleshooting Guide

### Issue 1: App Not Appearing in Admin Panel

**Symptoms**: App doesn't show in Admin → Apps

**Solutions**:
```bash
# Check app directory exists
ls -la /path/to/nextcloud/apps/3d_tiles_viewer

# Check appinfo files
ls -la /path/to/nextcloud/apps/3d_tiles_viewer/appinfo/

# Clear app cache
php occ app:update --all
php occ files:scan --all
```

### Issue 2: "App Not Compatible" Error

**Symptoms**: Nextcloud shows compatibility error

**Solutions**:
```bash
# Check Nextcloud version
php occ status

# Check PHP version
php --version

# Verify app requirements in info.xml
cat /path/to/nextcloud/apps/3d_tiles_viewer/appinfo/info.xml
```

### Issue 3: Permission Denied Errors

**Symptoms**: 403 or 500 errors when accessing app

**Solutions**:
```bash
# Fix ownership
sudo chown -R www-data:www-data /path/to/nextcloud/apps/3d_tiles_viewer

# Fix permissions
sudo chmod -R 755 /path/to/nextcloud/apps/3d_tiles_viewer
sudo chmod -R 644 /path/to/nextcloud/apps/3d_tiles_viewer/appinfo/*.xml

# Check web server user
ps aux | grep apache  # or nginx
```

### Issue 4: JavaScript/CSS Not Loading

**Symptoms**: App loads but viewer doesn't work

**Solutions**:
```bash
# Rebuild assets
cd /path/to/nextcloud/apps/3d_tiles_viewer
npm install
npm run build

# Check file permissions
ls -la js/
ls -la css/

# Clear browser cache
# Or use incognito mode to test
```

### Issue 5: Database/Configuration Errors

**Symptoms**: Database connection or config errors

**Solutions**:
```bash
# Check Nextcloud database
php occ db:check

# Repair database
php occ db:repair

# Check config
php occ config:list system
```

## 📊 Verification Checklist

After deployment, verify these items:

- [ ] App appears in Admin → Apps list
- [ ] App can be enabled without errors
- [ ] App appears in navigation menu
- [ ] No errors in Nextcloud logs
- [ ] JavaScript and CSS files load correctly
- [ ] 3D viewer opens when clicking tileset.json files
- [ ] No permission errors in web server logs

## 🔧 Advanced Configuration

### Custom App Path

If you need to install in a custom location:

```bash
# Add custom apps path to Nextcloud config
php occ config:system:set apps_paths --value='{"path":"\/custom\/apps","url":"\/custom-apps","writable":true}'

# Restart web server
sudo systemctl restart apache2
```

### Development Mode

For development installations:

```bash
# Install with dev dependencies
composer install
npm install

# Build in development mode
npm run build:dev

# Watch for changes
npm run watch
```

## 📞 Support Commands

Useful commands for troubleshooting:

```bash
# Check app status
php occ app:list | grep 3d_tiles_viewer

# Check app info
php occ app:info 3d_tiles_viewer

# Check logs
php occ log:list | grep 3d_tiles_viewer

# Check file permissions
ls -la /path/to/nextcloud/apps/3d_tiles_viewer/

# Check web server logs
sudo tail -f /var/log/apache2/error.log  # or nginx
```

## 🎯 Success Indicators

Your deployment is successful when:

1. **CLI Verification**: `php occ app:list` shows `3d_tiles_viewer` as enabled
2. **Web Interface**: App appears in Admin → Apps as enabled
3. **Navigation**: App appears in Nextcloud navigation menu
4. **Functionality**: Clicking tileset.json files opens the 3D viewer
5. **No Errors**: Clean logs without critical errors

## 🚀 Quick Deployment Script

Create a deployment script for easy installation:

```bash
#!/bin/bash
# deploy.sh - Quick deployment script

set -e

echo "🚀 Deploying 3D Tiles Viewer to Nextcloud..."

# Variables
NEXTCLOUD_PATH="/path/to/your/nextcloud"
APP_SOURCE="/path/to/nc-3dtiles-vwr"
APP_DEST="$NEXTCLOUD_PATH/apps/3d_tiles_viewer"

# Build the app
echo "📦 Building app..."
cd "$APP_SOURCE"
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Deploy to Nextcloud
echo "📁 Deploying to Nextcloud..."
sudo cp -r "$APP_SOURCE" "$APP_DEST"
sudo chown -R www-data:www-data "$APP_DEST"
sudo chmod -R 755 "$APP_DEST"

# Clear cache and enable
echo "🔄 Clearing cache and enabling app..."
cd "$NEXTCLOUD_PATH"
php occ cache:clear
php occ files:scan --all
php occ app:enable 3d_tiles_viewer

echo "✅ Deployment complete!"
echo "Check Admin → Apps to verify installation"
```

Make it executable: `chmod +x deploy.sh`

## 📝 Notes

- Always backup your Nextcloud installation before deploying apps
- Test in a development environment first
- Keep your Nextcloud and PHP versions up to date
- Monitor logs for any issues after deployment
- Consider using Docker for easier deployment and testing 