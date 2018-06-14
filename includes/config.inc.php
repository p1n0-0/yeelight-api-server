<?php

##
## Yeelight-API-Server Settings
##

# Set secret token (This token is necessary to perform operations with the api)
$secretToken                = "0000000000000000";   // Change me

# Set timeout to search Yeelight bulb
$timeout                    = 1;            // Time in seconds

# Default values
$default_name               = 'yeelight';   // Bulb network name
$default_power              = 'on';         // Power status (Support 'on' or 'off')
$default_bright             = 100;          // Brightness percentage (Minimum 1, Max 100)
$default_rgb                = 0xFFFFFF;     // Default RGB color (Hex value)
$default_effect             = 'sudden';     // Effect (Support 'sudden' = changed directly; or 'smooth' = changed gradual)
$default_effect_duration    = 30;           // Time in milliseconds for smooth effect (Minimum support duration is 30)
$default_cycles             = 5;            // Number of default cycles
$default_cycles_duration    = 1000;         // Time in milliseconds for cycle

# Do not edit from here

# Include response
include_once (__DIR__."/response.inc.php");
