
# amezmo-drupal-integrations

`amezmo-drupal-integrations` brings in everything needed to use an existing Drupal site on [Amezmo](https://www.amezmo.com)

Add this project to any Drupal distribution based on drupal/core-composer-scaffold to enable it for use on Amezmo.

This project provides a custom settings file and a few other small changes necessary to run Drupal on Amezmo.

## Enabling this project

This project must be enabled in the top-level composer.json file, or it will be ignored and will not perform any of its functions.
```
{
    ...
    "require": {
        "sdubois/amezmo-drupal-integrations": "dev-main"
    },
    ...
    "extra": {
        "drupal-scaffold": {
            "allowed-packages": [
                "sdubois/amezmo-drupal-integrations"
            ]
        }
    },
    "autoload": {
        "files": [
            "load.environment.php"
        ]
    }
}
```

If installing this on a pre-existing Drupal site, you will likely need to update your settings.php file to load the settings.amezmo.php file with the following snippet:

```
if (file_exists($app_root . '/' . $site_path . '/settings.amezmo.php')) {
  include $app_root . '/' . $site_path . '/settings.amezmo.php';
}
```

## File storage

Amezmo stores static files (images, documents, etc) in the `/webroot/storage` directory. In order to use this directory with Drupal, this package creates a symlink to this directory from the Drupal public files directory on each deployment. 

You can use SCP or SFTP to copy files to this directory.

## Credit

This repository is based on the pantheon-systems/drupal-integrations project. Thanks to everyone involved!
