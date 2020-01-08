#!/bin/sh

# copy service file
cp ReolinkCameraMeetsUnifi.service ReolinkCameraMeetsUnifi.service.tmp

# get user
serviceuser=$(logname)
# change user in service file
sed -i -e "s|_USER_|$serviceuser|g" ReolinkCameraMeetsUnifi.service.tmp

# change script filepath in service file
DIRECTORY=$(cd `dirname $0` && pwd)

sed -i -e "s|toogleCameraService.php|$DIRECTORY/src/toogleCameraService.php|g" ReolinkCameraMeetsUnifi.service.tmp

# copy service file to /etc/systemd/system/
sudo cp ReolinkCameraMeetsUnifi.service.tmp /etc/systemd/system/ReolinkCameraMeetsUnifi.service

# delete temporary service file
rm ReolinkCameraMeetsUnifi.service.tmp

# copy config file to /etc/
sudo cp reolinkUnifi.conf /etc/reolinkUnifi.conf
