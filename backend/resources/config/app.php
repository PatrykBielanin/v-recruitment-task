<?php

return [
	'debug' => getenv('APP_DEBUG') === 'true',

	'baseUrl' => getenv('APP_BASE_URL'),

	'apiBaseUrl' => getenv('API_BASE_URL'),
];
