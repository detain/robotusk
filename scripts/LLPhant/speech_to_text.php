<?php

use LLPhant\OpenAIConfig;
use LLPhant\Audio\OpenAIAudio;

include __DIR__.'/../../vendor/autoload.php';

$config = new OpenAIConfig();
$config->apiKey = '-';
$config->url = 'http://skynet.interserver.net:8080/v1';
$config->model = 'whisper-1';
$audio = new OpenAIAudio($config);
$response = $audio->transcribe(__DIR__.'/output.mp3');
echo "\nResponse:";
print_r($response);
echo "\n";
exit;
