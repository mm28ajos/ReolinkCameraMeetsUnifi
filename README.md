## Reolink Camera meets UniFi

A PHP-cli script to disable e-mail and push notification on motion detection of Reolink cameras as well as FTP upload on motion detection and near infrared light in case no predefined Wifi client device is connected to a UniFi access point.

This script is useful if you want to disable the camera (i.e. notifications on motion/FTP storage on motion detection and near infrared lights) in case you are at home. Being at home is detected by the script by checking the connected Wifi client devices connected to the Unifi network. Hereby, you can define mappings of Wifi client devices to Wifi access points by MAC addresses e.g. to exclude a particular access point from being relevant for switching of the motion detection notification. 

## Requirements

- a Reolink camera
- a UniFi controller
- a web server with PHP installed (tested with PHP-cli Version 7.3.11-1~deb10u1)
- network connectivity between this web server and the Reolink camera
- network connectivity between this web server and the UniFi controller

## Installation ##

Execute the following `git` command from the shell in your project directory:

```sh
git clone https://github.com/mm28ajos/ReolinkCameraMeetsUnifi.git
```

You should use [Composer](#composer) to resolve the required dependecies of the CLI script.

Follow the [installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have composer installed.

Once composer is installed, simply execute this command from the shell in your git directory:

```sh
composer update
```

## Example usage

### Adjust config
Adjust the config.php to your needs.
```sh
nano /path/to/Git/ReolinkCameraMeetsUnifi/config.php
```

### Add cronjob
For example, add cronjobs checking for disconnected/connected Wifi client devices and necessary switching of camera settings every 10 seconds. This could also be solved by enhancing the script to run as systemd service.
```sh
crontab -e
```
Add the follwing lines.
```
* * * * * /usr/bin/php /path/to/Git/ReolinkCameraMeetsUnifi/toogleCamera.php
* * * * * (sleep 10; /usr/bin/php /path/to/Git/ReolinkCameraMeetsUnifi/toogleCamera.php)
* * * * * (sleep 20; /usr/bin/php /path/to/Git/ReolinkCameraMeetsUnifi/toogleCamera.php)
* * * * * (sleep 30; /usr/bin/php /path/to/Git/ReolinkCameraMeetsUnifi/toogleCamera.php)
* * * * * (sleep 40; /usr/bin/php /path/to/Git/ReolinkCameraMeetsUnifi/toogleCamera.php)
* * * * * (sleep 50; /usr/bin/php /path/to/Git/ReolinkCameraMeetsUnifi/toogleCamera.php)
```

## Credits
The Readme is partially based on:

- https://github.com/Art-of-WiFi/UniFi-API-client/blob/master/README.md
