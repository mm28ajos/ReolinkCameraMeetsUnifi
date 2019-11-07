<?php

// UniFi settings
$controlleruser     = 'user'; // the user name for access to the UniFi Controller
$controllerpassword = 'password'; // the password for access to the UniFi Controller
$controllerurl      = 'https://10.1.1.1:8443'; // full url to the UniFi Controller, eg. 'https://22.22.11.11:8443'
$controllerversion  = '5.11.46'; // the version of the Controller software, eg. '4.6.6' (must be at least 4.0.0)
$site_id = 'default'; // the short name of the site

// Reolink settings
$reolinkuser = 'user'; // the user name for access to the webinterface of the camera
$reolinkpassword = 'password'; // the password for access to the webinterface of the camera
$reolinkcamera_ip = '10.1.1.2'; // ip of the webinterface of the camera

// set to true (without quotes) to enable debug output
$debug = false;

// the MAC of the AP devices and the client device MAC addresses which should trigger the switch off the motion detection e-mail, push, FTP upload and near infrared lights
$apMACToClientMACMapping = array(
                                // i.e. switch off motion detection e-mail, push, FTP upload and near infrared lights if Mobile1 with MAC 15:a2:11:c2:c2:a2 or Mobile2 with MAC 15:a2:11:a3:b2:a2 is connected to AP with MAC 17:e3:19:c1:f1:a1
                                 '17:e3:19:c1:f1:a1' => array('Mobile1' => '15:a2:11:c2:c2:a2',
                                                              'Mobile2' => '15:a2:11:a3:b2:a2'),
                                 // i.e. switch off motion detection e-mail, push, FTP upload and near infrared lights if Mobile1 with MAC 15:a2:11:c2:c2:a2 or Mobile2 with MAC 15:a2:11:a3:b2:a2 is connected to AP with MAC 28:e3:19:c1:f1:a2
                                 '28:e3:19:c1:f1:a2' => array('Mobile1' => '15:a2:11:c2:c2:a2',
                                                              'Mobile2' => '15:a2:11:a3:b2:a2'));
