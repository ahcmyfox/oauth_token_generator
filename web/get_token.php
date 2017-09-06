<?php

require '../vendor/autoload.php';

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
	echo "ERROR : missing authorization code in GET parameters";
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
$client = new GuzzleHttp\Client();
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
	echo "<p style='font-size:24px'> Error </p>";
	echo "<p>" . $e->getResponse()->getBody(true) . "</p>";
	die();
}

// Read the tokens in the answer

$answer = json_decode($res->getBody(), true);

echo "<p style='font-size:24px'> And you access token is ...</p>";
echo "<p>".$answer[$config['api_token']['access_key']]."</p>";

if ($config['api_token']['refresh_key'])
{
	echo "<p style='font-size:24px'> Your refresh token is ...</p>";
	echo "<p>".$answer[$config['api_token']['refresh_key']]."</p>";
}

?>