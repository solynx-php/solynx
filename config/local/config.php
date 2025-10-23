<?php

return array_merge(require __DIR__ . '/../base.php', [
    'debug' => true,
    'app_url' => getenv('APP_URL') ?: 'http://localhost:8000',
]);
