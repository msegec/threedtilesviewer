#!/bin/bash
# diagnose_installation.sh - Diagnostic script for ThreeDTilesViewer installation issues

set -e

echo "🔍 Diagnosing ThreeDTilesViewer installation issues..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    local status=$1
    local message=$2
    if [ "$status" = "OK" ]; then
        echo -e "${GREEN}✅ $message${NC}"
    elif [ "$status" = "WARNING" ]; then
        echo -e "${YELLOW}⚠️  $message${NC}"
    else
        echo -e "${RED}❌ $message${NC}"
    fi
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_status "WARNING" "Running as root - be careful with permissions"
fi

# Variables
NEXTCLOUD_PATH="/var/www/html"
APP_NAME="ThreeDTilesViewer"
APP_PATH="$NEXTCLOUD_PATH/apps/$APP_NAME"

echo "📋 System Information:"
echo "Nextcloud path: $NEXTCLOUD_PATH"
echo "App name: $APP_NAME"
echo "App path: $APP_PATH"

# Check 1: Nextcloud installation
echo ""
echo "🔍 Check 1: Nextcloud Installation"
if [ -d "$NEXTCLOUD_PATH" ]; then
    print_status "OK" "Nextcloud directory exists"
else
    print_status "ERROR" "Nextcloud directory not found at $NEXTCLOUD_PATH"
    exit 1
fi

# Check 2: Apps directory
echo ""
echo "🔍 Check 2: Apps Directory"
if [ -d "$NEXTCLOUD_PATH/apps" ]; then
    print_status "OK" "Apps directory exists"
    echo "Available apps:"
    ls -la "$NEXTCLOUD_PATH/apps/" | grep -E "(ThreeDTilesViewer|threedtilesviewer|hreeilesiewer)" || echo "No matching apps found"
else
    print_status "ERROR" "Apps directory not found"
    exit 1
fi

# Check 3: App directory existence
echo ""
echo "🔍 Check 3: App Directory"
if [ -d "$APP_PATH" ]; then
    print_status "OK" "App directory exists at $APP_PATH"
else
    print_status "ERROR" "App directory not found at $APP_PATH"
    echo "Checking for case variations..."
    if [ -d "$NEXTCLOUD_PATH/apps/threedtilesviewer" ]; then
        print_status "WARNING" "Found lowercase version at $NEXTCLOUD_PATH/apps/threedtilesviewer"
    fi
    if [ -d "$NEXTCLOUD_PATH/apps/hreeilesiewer" ]; then
        print_status "WARNING" "Found corrupted version at $NEXTCLOUD_PATH/apps/hreeilesiewer"
    fi
fi

# Check 4: App structure
echo ""
echo "🔍 Check 4: App Structure"
if [ -d "$APP_PATH" ]; then
    required_files=("appinfo/info.xml" "appinfo/navigation.php" "appinfo/routes.php" "lib/AppInfo/Application.php")
    missing_files=()
    
    for file in "${required_files[@]}"; do
        if [ -f "$APP_PATH/$file" ]; then
            print_status "OK" "Found $file"
        else
            print_status "ERROR" "Missing $file"
            missing_files+=("$file")
        fi
    done
    
    if [ ${#missing_files[@]} -eq 0 ]; then
        print_status "OK" "All required files present"
    else
        print_status "ERROR" "Missing files: ${missing_files[*]}"
    fi
fi

# Check 5: File permissions
echo ""
echo "🔍 Check 5: File Permissions"
if [ -d "$APP_PATH" ]; then
    # Get web server user
    WEB_USER=$(ps aux | grep -E "(apache|nginx|www-data)" | head -1 | awk '{print $1}')
    if [ -z "$WEB_USER" ]; then
        WEB_USER="www-data"
    fi
    
    echo "Web server user: $WEB_USER"
    
    # Check ownership
    if [ "$(stat -c '%U' "$APP_PATH")" = "$WEB_USER" ]; then
        print_status "OK" "Correct ownership"
    else
        print_status "WARNING" "Incorrect ownership - should be $WEB_USER"
    fi
    
    # Check permissions
    if [ -r "$APP_PATH" ] && [ -x "$APP_PATH" ]; then
        print_status "OK" "Directory permissions OK"
    else
        print_status "WARNING" "Directory permissions need fixing"
    fi
fi

# Check 6: Nextcloud CLI
echo ""
echo "🔍 Check 6: Nextcloud CLI"
if command -v php &> /dev/null; then
    print_status "OK" "PHP available"
    
    # Try to run occ
    if [ -f "$NEXTCLOUD_PATH/occ" ]; then
        print_status "OK" "occ found"
        
        # Check app status
        echo "App status:"
        cd "$NEXTCLOUD_PATH"
        php occ app:list | grep -i "threedtilesviewer" || echo "App not found in list"
    else
        print_status "ERROR" "occ not found"
    fi
else
    print_status "ERROR" "PHP not available"
fi

# Check 7: Cache status
echo ""
echo "🔍 Check 7: Cache Status"
if [ -d "$NEXTCLOUD_PATH/data/cache" ]; then
    cache_size=$(du -sh "$NEXTCLOUD_PATH/data/cache" | cut -f1)
    print_status "OK" "Cache directory exists (size: $cache_size)"
else
    print_status "WARNING" "Cache directory not found"
fi

# Recommendations
echo ""
echo "📋 Recommendations:"
echo ""

if [ ! -d "$APP_PATH" ]; then
    echo "1. Install the app:"
    echo "   sudo cp -r /path/to/ThreeDTilesViewer $APP_PATH"
    echo "   sudo chown -R www-data:www-data $APP_PATH"
    echo "   sudo chmod -R 755 $APP_PATH"
    echo ""
fi

echo "2. Clear caches:"
echo "   cd $NEXTCLOUD_PATH"
echo "   php occ cache:clear"
echo "   php occ files:scan --all"
echo ""

echo "3. Enable the app:"
echo "   php occ app:enable ThreeDTilesViewer"
echo ""

echo "4. Verify installation:"
echo "   php occ app:list | grep ThreeDTilesViewer"
echo ""

echo "5. Check logs for errors:"
echo "   tail -f $NEXTCLOUD_PATH/data/nextcloud.log"
echo ""

echo "🎯 Diagnostic complete!" 