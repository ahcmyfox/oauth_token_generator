# oauth_token_generator
Generic workflow to generate oauth tokens

## Install

```composer install```

## Declare an api service configuration

Make a `<service>.json` file in the `web/configs` directory, based on examples.

## Configure a service

Fill the json configuration file with your fields (`client_id`, `client_secret`, `redirect_uri`, `scope`, ...)

Declare the `redirect_uri = http://<path>/web/get_token.php` in the service api application configuration.

## Use

Go on `http://<path>/web/index.php?config=<service>` where `service` is the name of your config file, without the extension
