# ArenaSmsPluginForMautic
Arena Interactive SMS-Plugin for Mautic

## Installation
To install plugin as git submodule use these commands in project root directory:
```bash
$ git submodule add -f git@github.com:PatrikNorrgard/ArenaSmsPluginForMautic.git plugins/MauticSmsGatewayBundle
$ app/console mautic:plugins:install
``` 
and enable(publish) plugin in plugin configuration.  

## Installation using browser

1. Download repository and move the plugin to mautic installation directory `plugins/MauticSmsGateway`  
2. Open mautic in browser.  
3. Open settings (top right corner gear wheel).  
4. Press `Plugins`.  
5. Press `Install\Upgrade Plugins` button and wait until completetion.  
6. Choose Arena plugin.  
7. In `Enable\Auth` tab set `Published` to YES.

#### To work with plugin and see menu item "Arena SMS gateway"

go to `Configuration` > `Text Message Settings` and select `Arena SMS gateway` 