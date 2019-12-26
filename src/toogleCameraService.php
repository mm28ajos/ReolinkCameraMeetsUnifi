
<?php

class UnifiLoginFailure extends Exception { }

// open syslog
openlog('ReolinkCameraMeetsUnifi', LOG_PID, LOG_DAEMON);

// using the composer autoloader
require_once('vendor/autoload.php');

// include the config file
require_once('/etc/reolinkUnifi.conf');

// include the functions file
require_once('functions.php');

// set to the user defined error handler
set_error_handler('syslogErrorHandler');

// create a new Reolink_API object
$reolink_connection = new \Reolink_API\Client($reolinkuser, $reolinkpassword, $reolinkcamera_ip);

// set debug mode
$reolink_connection->setDebug($debug);

// set init camera flag false
$initCameraSetting = false;

// init camera settings until success
while (!$initCameraSetting)
{
	try
	{
		// login to the camera
		if ($reolink_connection->login())
		{
			// disable motion detection actions to set default state
			toogleMotionDetectionActions($reolink_connection, false);

			// set flag to indicate motion detection is disabled
			$motionDetectionEnabled = false;

			// logout from the camera
			$reolink_connection->logout();

			// set init falg to true
			$initCameraSetting = true;
		} else {
			// exit with error code 1
			outputErr('Could not connect to Reolink Camera');
		}
	}
	catch (GuzzleHttp\Exception\ConnectException $e)
	{
		outputErr($e->getMessage());
		outputErr('Could not connect to Reolink Camera');
		// wait 5 seconds for next try
		sleep(5);
	}
}

// loop to check constantly for need to toogle camera settings
for (;;)
{
	try {
		if (anyConnectedClientDeviceMappedToAPDevice($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion, $apMACToClientMACMapping, $debug))
		{
		    // in case there is a wifi client device connected to a mapped AP device and the motion detecion settings are enabled, log in to the camera and disable them
		    if ($motionDetectionEnabled)
		    {
			// login to the camera
			if ($reolink_connection->login())
			{
			    // disable motion detection acations
			    toogleMotionDetectionActions($reolink_connection, false);

			    // logout from the camera
			    $reolink_connection->logout();

			    // set the flag to false to remember the setting is disabled at the camera for the next run of the loop
			    $motionDetectionEnabled = false;

			    outputStdout('Wifi client device connected to mapped AP: Motion dection disabeled successfully.', $debug);
			} else {
			    outputErr('Could not connect to camera');
			}
		    } else {
			outputStdout('Wifi client device connected to mapped AP and motion dection already disabeled.', $debug);
		    }
		} else {
		    // in case their is no client device connected to a mapped AP device and the motion detecion settings are disabeld, log in to the camera and enable them
		    if(!$motionDetectionEnabled)
		    {
			// login to the camera
			if ($reolink_connection->login())
			{
			    // enable the motion detection actions
			    toogleMotionDetectionActions($reolink_connection, true);

			    // logout from the camera
			    $reolink_connection->logout();

			    // set the flag to true to remeber the setting is enbled at the camera for the next run of the loop
			    $motionDetectionEnabled = true;
			    
			    outputStdout('No wifi client device connected to mapped AP: Motion dection enabled successfully.', $debug);
			} else {
			    outputErr('Could not connect to camera.');
			}
		    } else {
			outputStdout('No wifi client device connected to mapped AP and motion dection already enabled.', $debug);
		    }
		}
	}

	catch (UnifiLoginFailure $e)
	{ 
		outputErr($e->getMessage());
	}

	catch (GuzzleHttp\Exception\ConnectException $e)
        {
                outputErr($e->getMessage());
                outputErr('Could not connect to Reolink Camera');
        }
	
	// sleep for 5 seconds befor checking connection of wifi clients again
	sleep($checkIntervalInSeconds);
}
