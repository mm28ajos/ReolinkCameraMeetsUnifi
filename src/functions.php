<?php
/**
* Outputs a message to syslog if debug mode is enabled
* @param message Message to output
*/
function outputStdout($message, $debug)
{
    //If debug option is not set, don't output anything on stdout
    if (!$debug) {
        return;
    }

    syslog(LOG_DEBUG, $message);
}

/**
* Outputs an error message to syslog
* @param string errorMessage Message to output
*/
function outputErr($errorMessage)
{
    syslog(LOG_ERR, $errorMessage);
}


/**
 * Sends the messages from trigger_error to syslog.
 */
function syslogErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        return false;
    }
    outputErr("$errstr on line $errline in file $errfile");
	    
    return true;
}

/**
 * Check if the client device MAC address is mapped to the access point's MAC address it is connected to based on the settings file.
 * @param string clientDeviceMAC the client device's MAC address
 * @param string apDeviceMAC the access point's MAC address
 * @param array apMACToClientMACMapping the mapping of the client device MAC to the access point MAC
 */
function clientDeviceMACMappedToAPDeviceMAC($clientDeviceMAC, $apDeviceMAC, $apMACToClientMACMapping, $debug)
{
    if (!in_array($apDeviceMAC, array_keys($apMACToClientMACMapping)))
    {
      outputStdout('AP device MAC ' . $apDeviceMAC . ' not listed in config mapping.', $debug);
      return false;
    }

    // return true if client device MAC is mapped to AP device MAC in config
    return in_array($clientDeviceMAC, $apMACToClientMACMapping[$apDeviceMAC]);
}

/**
 * Check if any connected client device is connected to a mapped access point as defined by the mapping setting.
 * @param string controlleruser the client device's MAC address
 * @param string controllerpassword the access point's MAC address
 * @param string controllerurl the mapping of the client device MAC to the access point MAC
 * @param string site_id
 * @param string controllerversion
 * @param array apMACToClientMACMapping
 * @param boolean debug
 * @return boolean true if the current client device is mapped to its connected access point device else flase
 * @throw UnifiLoginFailure
 */
function anyConnectedClientDeviceMappedToAPDevice($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion, $apMACToClientMACMapping, $debug)
{
    // initialize the UniFi API connection class and log in to the controller
    $unifi_connection = new UniFi_API\Client($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion);
    $set_debug_mode   = $unifi_connection->set_debug($debug);
    $loginresults     = $unifi_connection->login();

    if ($loginresults === true)
    {
        // pull the client list data
        $clients_array    = $unifi_connection->list_clients();

        // logout
        $unifi_connection->logout();

        // check each connected client
        foreach ($clients_array as $crrClient)
        {
            // if the current client device is mapped to its connected access point device, return true
            if (clientDeviceMACMappedToAPDeviceMAC($crrClient->mac, $crrClient->ap_mac, $apMACToClientMACMapping, $debug))
            {
                outputStdout('Client device MAC ' . $crrClient->mac . ' connected to mapped AP device MAC ' . $crrClient->ap_mac . ' according to mapping config.', $debug);
                return true;
            } else {
                // if non of the connected client devices was mapped to its connected access point device, return false
                outputStdout('Client device MAC ' . $crrClient->mac . ' connected to AP device MAC ' . $crrClient->ap_mac . ' not listed in mapping config.', $debug);
            }
        }
        // if non of the connected client devices was mapped to its connected access point device, return false
        outputStdout('No client device connected to mapped AP device accoring to mapping config.', $debug);
        return false;
    } else {
	throw new UnifiLoginFailure('Could not login to UniFi controller.');
    }
}

/**
 * Toogle the e-mail and push alert, FTP upload and near infrared lights at once.
 * @param boolean toogleBoolean the boolean value to toggle the motion actions to
 * @param reolinkconnection reolink_connection the object which represents the connection to the reolink camera
 */
function toogleMotionDetectionActions($reolink_connection, $toogleBoolean)
{
	// toogle the e-mail send on a detected motion
	$reolink_connection->toggleMotionEmail($toogleBoolean);

	// toogle the push notification to the Reolink app on a detected motion
	$reolink_connection->toggleMotionPush($toogleBoolean);

	// toogle the FTP upload on a detected motion
	$reolink_connection->toggleFTPUpload($toogleBoolean);
}
