<?php
return [
    'enabled'       => filter_var($_ENV['GDRIVE_ENABLED'] ?? false, FILTER_VALIDATE_BOOL),
    'client_id'     => $_ENV['GDRIVE_CLIENT_ID'] ?? '',
    'client_secret' => $_ENV['GDRIVE_CLIENT_SECRET'] ?? '',
    'refresh_token' => $_ENV['GDRIVE_REFRESH_TOKEN'] ?? '',
    'folder_id'     => $_ENV['GDRIVE_FOLDER_ID'] ?? '',
];