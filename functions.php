<?php
/**
* Outputs a message to stdout if debug mode is enabled
* @param message Message to output
*/
function outputStdout($message, $debug)
{
    //If debug option is not set, don't output anything on stdout
    if (!$debug) {
        return;
    }

    $date = date("Y/m/d H:i:s O");
    $output = sprintf("[%s][NOTICE] %s\n", $date, $message);
    echo $output;
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
            outputStdout('Checking client device MAC ' . $crrClient->mac, $debug);
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
        throw new Exception('Could not login to UniFi controller.');
    }
}
