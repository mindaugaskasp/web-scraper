# Web scanner

----

## Setup 

Note: tested on Ubuntu only.

1. Change your working dir to this project
2. Run `sudo apt install sox`
3. Run `sudo apt install libsox-fmt-mp3`
4. Run `sudo apt-get install sendmail`
5. Install required dependencies by running command `composer install`
6. Copy configuration file by running command `cp .env.example .env`
7. Set your desired configuration presets for .env file
8. Start web scan by running `php ./app app:scan:web `


## OS level dependencies

1. PHP 7.2
2. sendmail (sudo apt-get install sendmail)
3. Composer dependencies (Twig, Carbon, PHPMailer, various symphony components)

## How to Use
1. Run script as described in `setup` section
2. Wait for Email notification to come to your inbox when stock update is tracked
3. Check logs for errors or updates

## Supported stores

1. skytech.lt
2. Varle.lt
3. kilobaitas.lt

## How to add more stores support

Simply implement WebsiteInterface compliant object and add it to Manager. Website object is responsible for navigating Crawler object, fetching and parsing necessary data and converting it to ProductInterface object. This object gets formatted into HTMl later on and sent via Email to user.
