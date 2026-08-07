<?php
// Caminho: conexia/src/config/oauth.php

return [
    'client_id'         => getenv('GOOGLE_CLIENT_ID') ?: '',
    'client_secret'     => getenv('GOOGLE_CLIENT_SECRET') ?: '',
    'redirect_uri'      => 'https://conexia-kdh1.onrender.com/src/auth/google-callback.php',
    'dominio_permitido' => '@escola',
];

