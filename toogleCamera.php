<?php
// using the composer autoloader
require_once('vendor/autoload.php');

// include the config file
require_once('config.php');

//include the functions file
require_once('functions.php');

// define flag file for remebering the status of the last setting: If file exists, the script has enabled e-mail, FTP, push and infrared lights else it has disabled those.
$motionDetectionFlagFile = sys_get_temp_dir().'/motionDetectionEnabled';

// get camera setting status
$motionDetectionEnabled = file_exists($motionDetectionFlagFile);

// create a new Reolink_API object
$reolink_connection = new \Reolink_API\Client($reolinkuser, $reolinkpassword, $reolinkcamera_ip);

// set debug mode
$reolink_connection->setDebug($debug);

// check if any client device connected to an AP device can be looked up in the mapping of the config
if (anyConnectedClientDeviceMappedToAPDevice($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion, $apMACToClientMACMapping, $debug))
{
    // in case there is a wifi client device connected to a mapped AP device and the motion detecion settings are enabled, log in to the camera and disable them
    if (!$motionDetectionEnabled)
    {
        // login to the camera
        if ($reolink_connection->login())
        {
            // enable the e-mail send on a detected motion
            $reolink_connection->toggleMotionEmail(true);

            // enable the push notification to the Reolink app on a detected motion
            $reolink_connection->toggleMotionPush(true);

            // enable the FTP upload on a detected motion
            $reolink_connection->toggleFTPUpload(true);

            // enable the near infrared lights
            $reolink_connection->toggleInfraredLight(true);

            // logout from the camera
            $reolink_connection->logout();

            // create the flag file to remember the setting is enabled at the camera for the next run of the script
            fopen($motionDetectionFlagFile, 'w');

            outputStdout('Wifi client device connected to mapped AP: Motion dection disabeled successfully.', $debug);
        } else {
            outputStdout('Wifi client device connected to mapped AP: Motion dection disabeled unsuccessfully.', $debug);
        }
    } else {
        outputStdout('Wifi client device connected to mapped AP but motion dection already disabeled.', $debug);
    }
} else {
    // in case their is no client device connected to a mapped AP device and the motion detecion settings are disabeld, log in to the camera and enable them
    if($motionDetectionEnabled)
    {
        // login to the camera
        if ($reolink_connection->login())
        {
            // disable the e-mail send on a detected motion
            $reolink_connection->toggleMotionEmail(false);

            // disable the push notification to the Reolink app on a detected motion
            $reolink_connection->toggleMotionPush(false);

            // disable the FTP upload on a detected motion
            $reolink_connection->toggleFTPUpload(false);

            // disable the near infrared lights
            $reolink_connection->toggleInfraredLight(false);

            // logout from the camera
            $reolink_connection->logout();

            // delete the flag file to remeber the setting is disabled at the camera for the next run of the script
            unlink($motionDetectionFlagFile);

            outputStdout('No wifi client device connected to mapped AP: Motion dection enabled successfully.', $debug);
        } else {
            outputStdout('No wifi client device connected to mapped AP: Motion dection enabled unsuccessfully.', $debug);
        }
    } else {
        outputStdout('No wifi client device connected to mapped AP but motion dection already enabled.', $debug);
    }
}
