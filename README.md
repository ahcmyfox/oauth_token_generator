# oauth_token_generator
Generic workflow to generate oauth tokens

## Install

```composer install```

## Declare an api service configuration

Make a `<service>.json` file in the `web/configs` directory, based on examples.

You can also fill the given examples configs with your fields (`client_id`, `client_secret`, `redirect_uri`, `scope`, ...)

## Use

Go on `http://<path>/web/index.php?config=<service>` where `service` is the name of your config file, without the extension
