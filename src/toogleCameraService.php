
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

// define threshold period in seconds with no wifi client connected for switching on motion detection
$toogleOnThreshold = 5;

// define the seconds to sleep befor running the next check
$sleepTimeLoop = 2;

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
			$motionDetectionStatus = false;
			$motionDetectionTarget = false;

			// logout from the camera
			$reolink_connection->logout();

			// set init flag to true
			$initCameraSetting = true;

			// do some default logging
			outputStdout("Successfully set inital camera settings.", true);
		} else {
			// do some logging
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
		$motionDetectionTargetNew = !anyConnectedClientDeviceMappedToAPDevice($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion, $apMACToClientMACMapping, $debug);
		$dateTime = new DateTime();
		$timestamp = $dateTime->getTimestamp();
		$motionDetectionTargetTimestampNew = $timestamp;
		
		if ($motionDetectionTargetNew != $motionDetectionTarget)
		{
			$motionDetectionTarget = $motionDetectionTargetNew;
			$motionDetectionTargetTimestamp = $motionDetectionTargetTimestampNew;
		}

		if ($motionDetectionTarget != $motionDetectionStatus)
		{
			if ($motionDetectionTarget)
			{
				if ($timestamp - $motionDetectionTargetTimestamp > $toogleOnThreshold)
				{
					// do some default logging
					outputStdout('No Wifi client device connected to mapped AP: Motion dection going to be enabled.', true);

					// login to the camera
					if ($reolink_connection->login())
					{
						// disable motion detection actions
						toogleMotionDetectionActions($reolink_connection, true);
		
						// logout from the camera
						$reolink_connection->logout();

						// set the flag to false to remember the setting is disabled at the camera for the next run of the loop
						$motionDetectionStatus = true;
			    
						// do some default logging
						outputStdout('No Wifi client device connected to mapped AP: Motion dection enabled successfully.', true);
					} else {
					    outputErr('Could not connect to camera');
					}
	
				} else {
					// do some logging
					outputStdout('Toogle on threshold not reached.', $debug);
				}
			} else {
				// do some default logging
				outputStdout('Wifi client device connected to mapped AP: Motion dection going to be disabled.', true);

				// login to the camera
				if ($reolink_connection->login())
				{
				    // disable motion detection actions
				    toogleMotionDetectionActions($reolink_connection, false);

				    // logout from the camera
				    $reolink_connection->logout();

				    // set the flag to false to remember the setting is disabled at the camera for the next run of the loop
				    $motionDetectionStatus = false;
				    
				    // do some default logging
				    outputStdout('Wifi client device connected to mapped AP: Motion dection disabeled successfully.', true);
				} else {
				    outputErr('Could not connect to camera');
				}
			}
		} else {
			if ($motionDetectionStatus)
			{
				outputStdout('No wifi client device connected to mapped AP and motion dection already enabled.', $debug);
			} else {
				outputStdout('Wifi client device connected to mapped AP and motion dection already disabled.', $debug);
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
	
	// sleep for some seconds befor checking connection of wifi clients again
	sleep($sleepTimeLoop);
}
