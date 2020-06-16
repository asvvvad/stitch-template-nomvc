<?php declare(strict_types=1);

// include the Composer autoloader
require '../data/vendor/autoload.php';

// New Stitch instance.
// The only parameter to be passed is the data directory
$stitch = new Stitch('../data');


// Setting the error handler is required
// unless you're okay with a blank page and only the status code being returned.
// It's easy to make as it is made to be just like a route:
Stitch::setErrorHandler(function(array $vars) : array{
    return [
        'view' => 'error',
        'data' => [
            'code' => $vars['code'],
            'error' => $vars['full'],
            'title' => $vars['code'].':'. $vars['full'],
            'description' => $vars['code'] == 404 ? 'This page doesn\'t exist.' : 'HTTP method not allowed',
        ]
    ];
});

Stitch::get('/', function() : array {
	Stitch::$templates->addData([
		'title' => 'Stitch',
		'description' => 'Truely micro PHP framework to write less and enjoy more.',
		'header' => 'Stitch',
	]);
	return ['view' => 'home'];
});

Stitch::get('/api/v1/version', function() : array {
	return ['body' => ['success' => true, 'version' => 'v0.1']];
});

// HTML 
Stitch::get('/handle', 'This route has been <em>handled</em>');

// Voila! function as a service in one line.
Stitch::get('/uppercase', isset($_GET['text']) ? strtoupper($_GET['text']) : 'Pass the text as a GET parameter.');

// You can use all methods supported by FastRoute
// POST request support JSON data so to test you can:
Stitch::post('/echo[/{name}]', function (array $vars) : array {
	return [
		'body' => [
			'success' => isset($_POST['message']),
			'message' => $_POST['message'] ?? 'Message not specified',
			'name' => $vars['name'] ?? 'Name not set',
		],
		'status_code' => isset($_POST['message']) ? 200 : 400
	];
});

// HTTP Redirects / custom status code
// Redirects are made easy! simply set redirect to the uri you want
// There is no default redirect HTTP code sent by Stitch so set your own
// depending on the situation by setting status_code to the appropriate code
// status_code isn't limited to any method or body type and can be used in all calls to addRoute
Stitch::get('/old', function () : array {
	return ['redirect' => '/', 'status_code' => 301];
});

// HTTP headers
Stitch::post('/headers', function () : array {
    return ['header' => ['ONION-ADDRESS', 'asvvvadzqpxmkfjc.onion']];
});


// Different content types
// You can easily set custom headers and override ones set by the server
// This may be used to output content Custom headers & dother than HTML (images, file downloads, pdf documents, etc.)

Stitch::get('/image', function () : array {
    return ['body' => file_get_contents($_ENV['STATIC_DATA'] . '/img/pfp.png'), 'headers' => ['content-type' => 'image/png']];
});

try {
	$stitch->run();
} catch (\Throwable $th) {
	throw $th;
}