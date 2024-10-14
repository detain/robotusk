<?php

use Cognesy\Instructor\Features\LLM\Data\LLMConfig;
use Cognesy\Instructor\Features\LLM\Drivers\OpenAIDriver;
use Cognesy\Instructor\Instructor;

include __DIR__.'/../../vendor/autoload.php';

class Person {
    public string $name;
    public int $age;
}

$driver = new OpenAIDriver(new LLMConfig(
    apiUrl: 'http://skynet.interserver.net:8080/v1',
    apiKey: '-',
    endpoint: '/chat/completions',
    metadata: ['organization' => ''],
    model: 'gpt-4',
//    maxTokens: 128,
));
$instructor = (new Instructor)->withDriver($driver);

// Step 2: Provide content to process
$text = "His name is Jason and he is 28 years old.";

// Step 3: Use Instructor to run LLM inference
$person = $instructor->respond(
    messages: $text,
    responseModel: Person::class,
);

var_dump($person);
