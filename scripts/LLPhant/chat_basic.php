<?php

use LLPhant\OpenAIConfig;
use LLPhant\Chat\OpenAIChat;

include __DIR__.'/../../vendor/autoload.php';

$config = new OpenAIConfig();
$config->url = 'http://skynet.interserver.net:8080/v1';
$config->model = 'gpt-4';
$chat = new OpenAIChat($config);
$response = $chat->generateText('what is one + one ?');
print_r($response);
exit;
$client = OpenAI::factory()
    ->withBaseUri('http://skynet.interserver.net:8080/v1') // default: api.openai.com/v1
    ->make();

$response = $client->models()->list();

print_r($response->data);
