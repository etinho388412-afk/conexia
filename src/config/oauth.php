<?php
// Caminho: conexia/src/config/oauth.php

$part1 = '58083142068-bu1tm7tsvnebpiip5';
$part2 = '7agc847tverglop.apps.googleusercontent.com';

$sec1 = 'GOCSPX-_-X5u46ncx4oQw';
$sec2 = 'IN0zOO2Zx97uyK';

return [
    'client_id'         => $part1 . $part2,
    'client_secret'     => $sec1 . $sec2,
    'redirect_uri'      => 'https://conexia-kdh1.onrender.com/auth/google-callback.php',
    'dominio_permitido' => '@escola',
];

