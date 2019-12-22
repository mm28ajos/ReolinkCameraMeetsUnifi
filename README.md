## Reolink Camera meets UniFi

A PHP systemd service to disable e-mail and push notification on motion detection of Reolink cameras as well as FTP upload on motion detection and near infrared light in case no predefined Wifi client device is connected to a UniFi access point.

This service is useful if you want to disable the camera (i.e. notifications on motion/FTP storage on motion detection and near infrared lights) in case you are at home. Being at home is detected by the service by checking the connected Wifi client devices connected to the Unifi network. Hereby, you can define mappings of Wifi client devices to Wifi access points by MAC addresses e.g. to exclude a particular access point from being relevant for switching of the motion detection notification. 

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

You should use [Composer](#composer) to resolve the required dependecies of the service.

Follow the [installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have composer installed.

Once composer is installed, simply execute this command from the shell in your git directory in the folder src:

```sh
cd src
composer update
```

### Execute install.sh
Execute the install.sh script in the git folder with root permission.
```sh
sudo sh install.sh
```

### Adjust config
Adjust the reolinkUnifi.conf to your needs.
```sh
sudo nano /etc/reolinkUnifi.conf
```
### Start and enable systemd service
Start and enable the service.
```sh
sudo systemctl start ReolinkCameraMeetsUnifi.service 
sudo systemctl enable ReolinkCameraMeetsUnifi.service 
```

### Check service status and logs
Check the service status to see if there is any error.
```sh
sudo systemctl status ReolinkCameraMeetsUnifi.service 
```
Alternativly, check the syslog to see if there is any error.
```sh
sudo tail /var/log/syslog
```

### Optional: Adjust config and restart service
In case you had to correct the config, restart the service to make the config changes available.
```sh
sudo systemctl restart ReolinkCameraMeetsUnifi.service 
```


```

## Credits
The Readme is partially based on:

- https://github.com/Art-of-WiFi/UniFi-API-client/blob/master/README.md
