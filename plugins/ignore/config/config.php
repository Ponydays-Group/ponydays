<?php

if (!class_exists('Config')) die('Hacking attempt!');

Config::Set('db.table.user_ignore', '___db.table.prefix___user_ignore');
Config::Set('db.table.user_forbid_ignore', '___db.table.prefix___user_forbid_ignore');

return array(
    
);