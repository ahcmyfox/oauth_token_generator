<?php

// Read the config to use
if (isset($_GET['config'])) 
{
	$config_file = 'configs/' . $_GET['config'] . ".json";
}
else
{
	echo "Missing GET parameter config";
	die();
}

// Load the config
$config = json_decode(file_get_contents($config_file), true);

// Estimate the GET parameters
$get_params = array_map (function ($value) use ($config) 
{
	if(preg_match('/%.*%/', $value)) 
	{
		$value = substr($value, 1, strlen($value)-2);
		return $config[$value];
	} 
	return $value;
}, $config['api_authorize']['params']);

// Add the config in the OAuth state parameter
$get_params['state'] = $_GET['config'];

// Generate the authorize URL
$authorization_url = $config['api_authorize']['url'] . '?' . http_build_query($get_params);

// Redirect to authorize URL 
header("Location: $authorization_url", FILTER_SANITIZE_URL); /* Redirect browser */

exit();

?>