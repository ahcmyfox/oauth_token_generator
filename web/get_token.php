<?php

require __DIR__.'/../vendor/autoload.php';

$loader = new Twig_Loader_Filesystem(__DIR__.'/../templates');
$twig   = new Twig_Environment($loader, []);
$client = new GuzzleHttp\Client();

// Read the config to use
if (isset($_GET['state'])) 
{
	$config_file = 'configs/' . $_GET['state'] . ".json";
}
else
{
	echo "Missing GET parameter state";
	die();
}

// Load the config
$config = json_decode(file_get_contents($config_file), true);

// Estimate the parameters
$token_params = array_map (function ($value) use ($config) 
{
	if(preg_match('/%.*%/', $value)) 
	{
		$value = substr($value, 1, strlen($value)-2);
		return $config[$value];
	} 
	return $value;
}, $config['api_token']['params']);

// Append the authorization code to parameters
if (isset($_GET['code'])) 
{
	$token_params['code'] = $_GET['code'];
}
else
{
	echo $twig->render('token.twig', [
		'title' => sprintf('%s authentication', $_GET['state']), 
		'boxes' => ['Error' => 'missing authorization code in GET parameters']
	]);
	die();
}

// Build the additional headers
if (array_key_exists('headers', $config['api_token']))
{
	$additional_headers = $config['api_token']['headers'];
}
else
{
	$additional_headers = [];
}

// Execute the request to get the tokens
try
{
	$res = $client->request('POST', $config['api_token']['url'], [
		'headers'     => $additional_headers,
		'form_params' => $token_params,
		'verify'      => __DIR__ . '\cacert.pem',
	]);
}
catch (Exception $e)
{
	echo $twig->render('token.twig', [
		'title' => sprintf('%s authentication', $_GET['state']), 
		'boxes' => ['Error' => $e->getResponse()->getBody(true)]
	]);
	die();
}

// Read the tokens in the answer
$answer = json_decode($res->getBody(), true);

$tokens = [sprintf('And your %s access token is...', $_GET['state']) => $answer[$config['api_token']['access_key']]];

if (array_key_exists('refresh_key', $config['api_token']))
{
	if ($config['api_token']['refresh_key'])
	{
		$tokens[sprintf('%s refresh token', $_GET['state'])] = $answer[$config['api_token']['refresh_key']];
	}
}

// Render the view to display the tokens
echo $twig->render('token.twig', ['title' => sprintf('%s authentication', $_GET['state']), 'boxes' => $tokens]);

?>