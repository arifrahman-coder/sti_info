<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$localPath = APPPATH . 'config/external_services.local.php';
$localSecrets = is_file($localPath) ? require $localPath : array();

$environmentKeys = array(
    'openai_api_key' => 'OPENAI_API_KEY',
    'recaptcha_home_secret' => 'RECAPTCHA_HOME_SECRET',
    'recaptcha_admin_secret' => 'RECAPTCHA_ADMIN_SECRET',
);

foreach ($environmentKeys as $key => $environmentName) {
    $environmentValue = getenv($environmentName);
    $config[$key] = $environmentValue !== FALSE && $environmentValue !== ''
        ? $environmentValue
        : (isset($localSecrets[$key]) ? $localSecrets[$key] : '');
}
