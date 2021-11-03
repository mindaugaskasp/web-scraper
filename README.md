# Web scanner

Scanner visits online retailer sites and looks for items in inventory by specified keywords. If any items are found in stock - script sends Email notification and plays alert sound - so you don't miss out on a low supply item you want to buy. Currently implemented for Lithuanian retailers, but can be accommodated for any online shop easily.

## Setup 

Note: tested on Ubuntu 20.04 only.

1. Change your working dir to this project
2. Run `sudo apt install sox`
3. Run `sudo apt install libsox-fmt-mp3`
4. Run `sudo apt-get install sendmail`
5. Run `yarn install`
6. Install required dependencies by running command `composer install`
7. Copy configuration file by running command `cp .env.example .env`
8. Set your desired configuration presets for .env file
9. Start web scan by running `php ./app app:scan:web `

## Dependencies

1. PHP 7.2
2. Composer dependencies (Twig, Carbon, PHPMailer, various symphony components)
3. sox audio library
4. sendmail library

## How to Use
1. Run script as described in `setup` section
2. Wait for Email notification to come to your inbox when stock update is tracked
3. Check logs for errors or updates

## Supported stores

1. skytech.lt
2. Varle.lt
3. kilobaitas.lt
4. Topocentras.lt
5. Kaina24.lt

## How to add more stores support

Simply implement WebsiteInterface compliant object. All objects ending with `Website` suffix are automatically loaded into Manager object.

## How to use Google SMTP for email notifications

1. Open Google Email settings
2. Head to Forwarding and POP/IMAP section
3. Enable IMAP
4. Head to general Google account settings (https://myaccount.google.com/securityhttps://myaccount.google.com/security)
5. Disable 2FA if it is enabled and enable low security app usage (proceed at your own risk, or create separate google email account for this.)
6. Enter Google account password/username into .env file

This way you don't need your email server to handle sending notifications and let your Google account do all the work.
