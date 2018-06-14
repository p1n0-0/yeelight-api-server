# Yeelight API Server
API to remotely control the bulbs of the manufacturer yeelight.

*Warning: This API only works with the RGB mode.
The API has been tested with the color bulb models only.*

## Installation

It must be installed on a pc or raspberry pi with PHP 7.0 or higher.

The ```composer install``` command must be executed to install the dependencies.

It is necessary to expose the web folder in apache or nginx. Requests must be made to the ```api.php``` file.

## Settings
To begin its use it is recommended to edit the file config.inc.php (Inside the "includes" folder) and establish a unique secret token.

It is also possible to modify the default values and the connection timeout to the bulbs.

## Parameters
The API only supports the *POST* method and the parameters specified as *form-data*.

| Parameters            | Default Value | Min Value | Max Value | Description                                                                                                                                                                                                                                                                                                                                                                                                                        |
| --------------------- | ------------- | :-------: | :-------: | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| ```secretToken```     |               |           |           | Secret token set in the config.inc.php file.                                                                                                                                                                                                                                                                                                                                                                                       |
| ```bulbs```           | ```all```     |           |           | List of id of the bulbs to perform the operation separated by ```,```.                                                                                                                                                                                                                                                                                                                                                             |
| ```action```          |               |           |           | Action to perform. The possible values are: ```get```, ```power```, ```bright```, ```rgb``` ```default``` & ```cycle```.                                                                                                                                                                                                                                                                                                           |
| ```power```           | ```on```*     |           |           | Determine if you want to turn the light bulbs on or off. The possible values are: ```on``` & ```off```.                                                                                                                                                                                                                                                                                                                            |
| ```bright```          | ```100```*    | `1`       | `100`     | Determine the level of brightness.                                                                                                                                                                                                                                                                                                                                                                                                 |
| ```rgb```             | ```FFFFFF```* | `000000`  | `FFFFFF`  | Determine the color of the bulbs.                                                                                                                                                                                                                                                                                                                                                                                                  |
| ```effect```          | ```sudden```* |           |           | Support two values: ```sudden``` and ```smooth```. If effect is ```sudden```, then the color temperature will be changed directly to target value, under this case, the parameter ```effect_duration``` is ignored. If effect is ```smooth```, then the color temperature will be changed to target value in a gradual fashion, under this case, the total time of gradual change is specified in parameter ```effect_duration```. |
| ```effect_duration``` | ```30```*     | `30`      | `10000`   | Specifies the total time of the gradual changing. The unit is milliseconds.                                                                                                                                                                                                                                                                                                                                                        |
| ```cycles```          | ```5```*      | `1`       | `20`      | Number of changes to be made.                                                                                                                                                                                                                                                                                                                                                                                                      |
| ```cycles_duration``` | ```1000```*   | `100`     | `10000`   | Duration between cycles. The unit is milliseconds.                                                                                                                                                                                                                                                                                                                                                                                 |
_*The default values can be modified in the file config.inc.php_

## Actions

#### `get` - Get information about the light bulbs.
* Required parameters: ```secretToken``` & ```action```.
* Optional parameters: ```bulbs``` 
* Response:
```
{
   "success": true,
   "data": [
       {
           "id": "0x0000000000000000",
           "address": "192.168.1.X:55443",
           "power": "off",
           "rgb": "ff0000",
           "bright": "100"
       }
   ]
}
```

#### `power` - Turn bulbs on or off.
* Required parameters: ```secretToken``` & ```action```.
* Optional parameters: ```bulbs```, ```power```,  ```effect``` & ```effect_duration```.

#### `bright` - Set brightness of the bulb.
* Required parameters: ```secretToken``` & ```action```.
* Optional parameters: ```bulbs```, ```bright```,  ```effect``` & ```effect_duration```.

#### `rgb` - Set color of the bulb.
* Required parameters: ```secretToken``` & ```action```.
* Optional parameters: ```bulbs```, ```rgb```,  ```effect``` & ```effect_duration```.

#### `default` - Set current state as the default state.
* Required parameters: ```secretToken``` & ```action```.
* Optional parameters: ```bulbs```

#### `cycle` - Make a cycle animation.
* Required parameters: ```secretToken``` & ```action```.
* Optional parameters: ```bulbs```, ```bright```, ```rgb```, ```cycles``` & ```cycles_duration```.

## License

```
MIT License

Copyright (c) 2018 Francisco Gómez Pino

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 ```