# Keda Battle

The code is written in PHP (some Javascripts used) and a MySQL database is used for this project.
All files here are from the website (http://lielakeda.lv/battle/) except the folder "get".
The "get" folder contains scripts to be run daily when updating the list of photos.
Keda_Battle.sql includes SQL commands for creating the database.

## Requirements

https://github.com/google/php-photoslibrary

Run this:
```Shell
composer require google/photos-library
```
And copy the `vendor` directory to the root of Keda Battle.
