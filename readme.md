# WP-Brew

Quickly install and configure macOS 10.12+ for local WordPress development with WP-Brew, built for Mac minimalists. Inspired by [ValetPress](https://github.com/SystmWeb/valetpress), rewritten to suit very opinionated needs.

## What?

WP-Brew uses [Laravel Valet](https://laravel.com/docs/valet) and [WP-CLI](https://wp-cli.org/) to speed up the creation of a WordPress install on your local system. WP-Brew configures your Mac to always run Nginx in the background when your machine starts and with the help of Dnsmasq, all requests on the `*.test` TLD are proxied to point to sites installed on your local machine which run the latest version of PHP via PHP-FPM, backed by MariaDB, encrypted with TLS using HTTP/2.

## Why?

Because, sometimes, running a full virtual machine is simply overkill. No Vagrant. No Apache. No fuss.

## Features

- Download, install and activate a fresh WordPress site in a few seconds
- Run WordPress on your Mac using as little as [7MB of RAM](https://laravel.com/docs/5.6/valet#introduction)
- Modern web stack: Nginx, PHP 7.x, MariaDB, Dnsmasq, WP-CLI, Composer

## To install

1. Clone this repo into a directory such as `~/Resources/WP-Brew` (soon to become a Composer-installed package)
2. Install [Homebrew](https://brew.sh) and run `brew bundle` from within `~/Resources/WP-Brew`; the included Brewfile will properly install everything needed by Valet, plus additional tools and dependencies (soon to be automated via bootstrap.sh/Makefile)
3. Setup [Laravel Valet](https://laravel.com/docs/valet); all that should be needed is to run `composer global require laravel/valet` and to add `~/.composer/vendor/bin` to your `$PATH` (soon to be automated)
4. Symlink `~/Resources/WP-Brew/bin/wpb` somewhere like `/usr/local/bin/wpb` (i.e. somewhere in your `$PATH`) (soon to be automated)
5. Duplicate `~/Resources/WP-Brew/config.sample.json` to `~/Resources/WP-Brew/config.json` and adjust as needed
6. Install and activate the WP-Brew DevTool WordPress plugin for additional development functionality (not to be used as a stand-alone plugin in a production environment!)

## Available Commands:

`wpb create`

- Download WordPress into a directory as specified in the config.json
- Setup the database and configure the install
- Create the user and password specified in config.json

`wpb delete`

- Lists available WP-Brew installations
- Confim deletion of project — database, files and all — prior to execution

`wpb help`

- Will display a summary of available commands

### Coming Soon

`wpb park` - Register a directory as a path containing sites
`wpb link` - Serve a single site from a directory
`wpb unlink` - Destroy the symbolic link to a directory containing a site
`wpb links` - See a listing of all linked directories/sites
`wpb share` - Create a publicly-accessible URL of your local site using ngrok
`wpb forget` - Remove a site from the "parked" directory list
`wpb paths` - View all of your "parked" paths
`wpb restart` - Restart the Valet daemon
`wpb start` - Start the Valet daemon
`wpb stop` - Stop the Valet daemon
`wpb uninstall` - Uninstall the Valet daemon entirely
`wpb secure` - Secure WordPress with SSL (encrypted TLS using HTTP/2 provided by mkcert)
`wpb unsecure` - Destroy an SSL certificate and update https to http


## Config Explained

- `wp_admin_email` is used as the admin email address for new WP installs.
- `wp_admin_user` is used as the username for new WP installs. [If changed you must update the username in the auto-login plugin](https://github.com/sdenike/valetpress/blob/master/plugins/auto-login/auto-login.php#L18)
- `wp_admin_password` is used as the password for new WP installs. [If changed you must update the password in the auto-login plugin](https://github.com/sdenike/valetpress/blob/master/plugins/auto-login/auto-login.php#L19)
- `sites_folder` is a directory that you've run `valet park` in to serve sites.
- `open_browser` if set to `1` a browser will auto open after the install completes, `0` will make so that it doesn't
- `browser` you can set the default browser such as Safari, or Google Chrome, etc
- `valet_domain` Default is set to `test` but you can change this to whatever you use for Valet, this can be adjusted by using `valet domain TLDTOUSE`
- `plugins_add` Plugins that you wish to install/activate on each new install
- `plugins_remove` Plugins that you wish to remove from the default installs

## Requirements

- [Homebrew](https://brew.sh)
- [Laravel Valet](https://laravel.com/docs/valet)
- [Nginx Web Server](https://www.nginx.com)
- [PHP Hypertext Preprocessor](http://php.net)
- [MariaDB](https://mariadb.org) or [MySQL](https://www.mysql.com)
- [Composer PHP Package Manager](https://getcomposer.org)
- [WP-CLI](https://wp-cli.org)
- [jq](https://stedolan.github.io/jq/)
