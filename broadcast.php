<?php
// composer
require __DIR__ . '/vendor/autoload.php';

if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
           $headers = [];
       foreach ($_SERVER as $name => $value)
       {
           if (substr($name, 0, 5) == 'HTTP_')
           {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
} 

$headers = getallheaders();

$formData = $_POST ?? [];

$queryParam = $_GET ?? [];

$method = $_SERVER['REQUEST_METHOD'];

$body = file_get_contents('php://input');

function callUrl($method, $url, $headers, $post, $queryParam, $body)
{
	if ($queryParam) {
        $url .= '?' . http_build_query($queryParam);
    }
    $client = new \GuzzleHttp\Client([
        'header' => $headers
    ]);

    $options = [];

    if ($body) {
        $options['body'] = $body;
    }

    if ($post) {
        $options['form_params'] = $post;
    }

    $client->request($method, $url, $options);
}

$broadcastUrl = include(__DIR__ . '/urls.php');


foreach ($broadcastUrl as $url) {
    callUrl($method, $url, $headers, $formData, $queryParam, $body);
}
?>