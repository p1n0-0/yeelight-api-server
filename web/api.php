<?php

##
## Yeelight-API-Server
##

// Include composer dependencies
include_once(__DIR__ . "/../vendor/autoload.php");

// Include settings
include(__DIR__ . "/../includes/config.inc.php");

// Load dependencies
use Yeelight\Bulb;
use Yeelight\YeelightClient;

// Check if method is POST
if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
    // Check if secret token is valid
    if ((isset($_POST['secretToken']))&&($_POST['secretToken']==$secretToken)) {

        // Instance Yeelight Client
        $client = new YeelightClient($timeout);

        try { // If throw exception return error

            // Obtain list of bulbs
            $bulbList = $client->search();

            // If no bulbs, return 404 error
            if (count($bulbList)==0) {
                return_error(404,"No bulb was found");
                die();
            }

            // if the user only wants to operate with one or several concrete bulbs
            if ((isset($_POST['bulbs']))&&($_POST['bulbs']!='all')) {

                // Obtain Ids
                $bulbListIds = explode(',', $_POST['bulbs']);

                // Obtain bulbs selected
                $selectedBulb = [];
                foreach ($bulbList as $bulb) {
                    if (in_array($bulb->getId(), $bulbListIds)) {
                        $selectedBulb[] = $bulb;
                    }
                }

                // If no selected bulbs, return 404 error
                if (count($selectedBulb)==0) {
                    return_error(404,"The selected bulbs were not found");
                    die();
                }

                // Save selected bulbs
                $bulbList = $selectedBulb;

            }

            // Obtain commons values
            $power = ( (isset($_POST['power'])) && ( ($_POST['power']=='on') || ($_POST['power']=='off')) ) ? $_POST['power'] : $default_power;
            $bright = ( (isset($_POST['bright'])) && (!empty($_POST['bright'])) && (is_numeric($_POST['bright'])) ) ? $_POST['bright'] : $default_bright;
            $rgb = ( (isset($_POST['rgb'])) && (!empty($_POST['rgb'])) && (ctype_xdigit($_POST['rgb']))) ? hexdec($_POST['rgb']) : $default_rgb;
            $effect = ( (isset($_POST['effect'])) && ( ($_POST['effect']==Bulb\Bulb::EFFECT_SMOOTH) || ($_POST['effect']==Bulb\Bulb::EFFECT_SUDDEN) ) ) ? $_POST['effect'] : $default_effect;
            $effect_duration = ( (isset($_POST['effect_duration'])) && (!empty($_POST['effect_duration'])) && (is_numeric($_POST['effect_duration'])) ) ? $_POST['effect_duration'] : $default_effect_duration;
            $cycles = ( (isset($_POST['cycles'])) && (!empty($_POST['cycles'])) && (is_numeric($_POST['cycles'])) ) ? $_POST['cycles'] : $default_cycles;
            $cycles_duration = ( (isset($_POST['cycles_duration'])) && (!empty($_POST['cycles_duration'])) && (is_numeric($_POST['cycles_duration'])) ) ? $_POST['cycles_duration'] : $default_cycles_duration;

            // Restrictions
            $bright = ($bright > 100) ? 100 : (($bright < 1) ? 1 : $bright);
            $effect_duration = ($effect_duration > 10000) ? 10000 : (($effect_duration < 30) ? 30 : $effect_duration);
            $cycles = ($cycles > 20) ? 20 : (($cycles < 1) ? 1 : $cycles);
            $cycles_duration = ($cycles_duration > 10000) ? 10000 : (($cycles_duration < 100) ? 100 : $cycles_duration);

            // Possible actions
            switch ($_POST['action']) {

                case 'get':

                    $data = [];

                    foreach ($bulbList as $bulb) {
                        $bulb->getProp([Bulb\BulbProperties::POWER, Bulb\BulbProperties::RGB, Bulb\BulbProperties::BRIGHT])->done(function (Bulb\Response $response) {
                            global $data, $bulb;
                            $result = $response->getResult();
                            $data[] = ["id" => $bulb->getId(), "address" => $bulb->getAddress(), "power" => $result[0], "rgb" => dechex($result[1]), "bright" => $result[2]];
                        }, function (Bulb\Exceptions\BulbCommandException $exception) {
                            global $data, $bulb;
                            $data[] = ["id" => $bulb->getId(), "address" => $bulb->getAddress(), "exception" => $exception->getMessage()];
                        });
                    }

                    return_ok($data); // Return ok & close communication with client

                    break;

                case 'power':

                    foreach ($bulbList as $bulb) {
                        $bulb->setPower($power, $effect, $effect_duration);
                    }

                    return_ok(); // Return ok & close communication with client

                    break;

                case 'bright':

                    foreach ($bulbList as $bulb) {
                        $bulb->setBright($bright ,$effect, $effect_duration);
                    }

                    return_ok(); // Return ok & close communication with client

                    break;

                case 'rgb':

                    foreach ($bulbList as $bulb) {
                        $bulb->setRgb($rgb, $effect, $effect_duration);
                    }

                    return_ok(); // Return ok & close communication with client

                    break;

                case 'default':

                    foreach ($bulbList as $bulb) {
                        $bulb->setDefault();
                    }

                    return_ok(); // Return ok & close communication with client

                    break;

                case 'cycle':

                    // Get current values
                    $currentValues = [];
                    foreach ($bulbList as $key => $bulb) {
                        $bulb->getProp([Bulb\BulbProperties::POWER, Bulb\BulbProperties::RGB, Bulb\BulbProperties::BRIGHT])->done(function (Bulb\Response $response) {
                            global $currentValues, $key, $bulb;
                            $result = $response->getResult();
                            $currentValues[$key] =  ["power" => $result[0], "rgb" => $result[1], "bright" => $result[2]];
                        }, function (Bulb\Exceptions\BulbCommandException $exception) {
                            return_error(500,$exception->getMessage());
                            die();
                        });
                    }

                    return_ok(); // Return ok & close communication with client

                    // Run cycles
                    for ($x=0;$x<abs($cycles);$x++) {
                        foreach ($bulbList as $key => $bulb) {
                            if ($currentValues[$key]["power"] == "on") {
                                $bulb->setBright(($x%2==0) ? $bright : $currentValues[$key]["bright"], Bulb\Bulb::EFFECT_SUDDEN, 30);
                                $bulb->setRgb(($x%2==0) ? $rgb : $currentValues[$key]["rgb"], Bulb\Bulb::EFFECT_SUDDEN, 30);
                            }
                        }
                        usleep($cycles_duration * 1000);
                    }

                    // Restore original values
                    for ($x=0;$x<1;$x++) {
                        foreach ($bulbList as $key => $bulb) {
                            if ($currentValues[$key]["power"] == "on") {
                                $bulb->setBright($currentValues[$key]["bright"], Bulb\Bulb::EFFECT_SUDDEN, 30);
                                $bulb->setRgb($currentValues[$key]["rgb"], Bulb\Bulb::EFFECT_SUDDEN, 30);
                            }
                        }
                        sleep(1);
                    }

                    break;

                default:
                    return_error(404,"The action was not found or was not specified");
                    die();

            }

        } catch (\Yeelight\Exceptions\SocketException $e) { // Return error if exception
            return_error(500, $e->getMessage());
        }
    } else { // The secret token is invalid
        return_error(401, 'Unauthorized');
    }
} else { // The method is different from POST
    return_error(405, 'Method Not Allowed');
}
