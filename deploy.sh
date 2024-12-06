#!/bin/bash

# First verify the checksum between uploaded and live code to see if deployment is necessary.
echo "Verifying checksum..."
CURRENT_CHECKSUM=$(cat live/checksum.txt)
if [ $? -ne 0 ]; then
  echo "No current checksum found. Assuming deployment is necessary."
  CURRENT_CHECKSUM="NoCurrentChecksum"
fi

NEW_CHECKSUM=$(cat uploads/checksum.txt)
if [ $? -ne 0 ]; then
  echo "Can't find new checksum. Should be there though. Exiting."
  exit 1
fi

if [ "$CURRENT_CHECKSUM" == "$NEW_CHECKSUM" ]; then
  echo "Checksums are identical, no deployment necessary."
  exit 0
fi

# Clean-up
echo "Removing old build..."
rm -rf previous_build

# Prepare new build
echo "Unzipping new build..."
unzip -q uploads/build.zip -d new_build

echo "Shuffling files..."
cp uploads/checksum.txt new_build
cp environment/.env new_build

echo "Symlinking storage..."
ln -s /actual/path \
  /symlinked/path

# Setup new build
echo "Bringing down builds..."
(cd new_build && php artisan down --render='errors::503')
(cd live && php artisan down)

echo "Rotating builds..."
mv live previous_build
mv new_build live

echo "Migrating database..."
(cd live && php artisan migrate --force)

echo "Clearing laravel config..."
(cd live && php artisan 'config:clear')

echo "Clearing laravel events..."
(cd live && php artisan 'event:clear')

echo "Clearing laravel routes..."
(cd live && php artisan 'route:clear')

echo "Clearing laravel views..."
(cd live && php artisan 'view:clear')

echo "Syncing permissions and roles..."
(cd live && php artisan 'proto:syncroles')

echo "Caching laravel (optimize)..."
(cd live && php artisan 'optimize')

echo "Bringing up new live build..."
(cd live && php artisan up)

echo "Zipping previous build..."
zip -rq previous_build.zip previous_build

echo "Done!"
