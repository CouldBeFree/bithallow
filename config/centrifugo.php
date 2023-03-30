<?php
return [
    'url' => env('CENT_URL_API', 'http://localhost:8000/api/'), # URL api 
    'hmac_secret' => env('CENT_HMAC_SECRET', ''), # Kmac secret key
    'apikey' => env('CENT_API_KEY', ''), # API key
];