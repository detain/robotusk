<?php

include __DIR__.'/../../vendor/autoload.php';

$client = OpenAI::factory()
    ->withApiKey('-')
    ->withBaseUri('http://skynet.interserver.net:8080/v1')
    ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
    ->make();

$response = $client->chat()->create([
    'model' => 'gpt-4',
    'messages' => [
        ['role' => 'user', 'content' => 'What\'s the weather like in Boston?'],
    ],
    'tools' => [
        [
            'type' => 'function',
            'function' => [
                'name' => 'get_current_weather',
                'description' => 'Get the current weather in a given location',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'location' => [
                            'type' => 'string',
                            'description' => 'The city and state, e.g. San Francisco, CA',
                        ],
                        'unit' => [
                            'type' => 'string',
                            'enum' => ['celsius', 'fahrenheit']
                        ],
                    ],
                    'required' => ['location'],
                ],
            ],
        ]
    ]
]);

echo $response->id. "\n"; // 'chatcmpl-6pMyfj1HF4QXnfvjtfzvufZSQq6Eq'
echo $response->object. "\n"; // 'chat.completion'
echo $response->created. "\n"; // 1677701073
echo $response->model. "\n"; // 'gpt-3.5-turbo-0613'

foreach ($response->choices as $result) {
    echo $result->index. "\n"; // 0
    echo $result->message->role. "\n"; // 'assistant'
    echo $result->message->content. "\n"; // null
    echo $result->message->toolCalls[0]->id. "\n"; // 'call_123'
    echo $result->message->toolCalls[0]->type. "\n"; // 'function'
    echo $result->message->toolCalls[0]->function->name. "\n"; // 'get_current_weather'
    echo $result->message->toolCalls[0]->function->arguments. "\n"; // "{\n  \"location\": \"Boston, MA\"\n}"
    echo $result->finishReason. "\n"; // 'tool_calls'
}

echo $response->usage->promptTokens. "\n"; // 82,
echo $response->usage->completionTokens. "\n"; // 18,
echo $response->usage->totalTokens. "\n"; // 100
exit;
