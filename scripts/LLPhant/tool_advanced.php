<?php

use LLPhant\OpenAIConfig;
use LLPhant\Chat\OpenAIChat;
use LLPhant\Chat\FunctionInfo\FunctionBuilder;
use LLPhant\Chat\FunctionInfo\Parameter;
use LLPhant\Chat\FunctionInfo\FunctionInfo;
use LLPhant\Chat\Message;

class Weather
{
    /**
     * Gets the weather for a given location
     */
    public function getWeather(string $location): string
    {
        return 'The weather is cloudy. The temperature is 44 degrees celsius.';
    }
}

include __DIR__.'/../../vendor/autoload.php';

$config = new OpenAIConfig();
$config->apiKey = '-';
$config->url = 'http://skynet.interserver.net:8080/v1';
$config->model = 'gpt-4';

    $chat = new OpenAIChat($config);
    $location = new Parameter('location', 'string', 'the name of the city, the state or province and the nation');
    $weather = new Weather();
    $function = new FunctionInfo(
        'getWeather',
        $weather,
        'returns the current weather in the given location. The result contains the description of the weather plus the current temperature in Celsius',
        [$location]
    );
    $chat->addTool($function);
    $chat->setSystemMessage('You are an AI that answers to questions about weather in certain locations by calling external services to get the information');
    $messages = [
        Message::user('What is the weather in Venice?'),
    ];
    $functionInfo = $chat->generateChatOrReturnFunctionCalled($messages);
print_r($functionInfo);
echo "Total Tokens ".$chat->getTotalTokens()."\n";
    $toolCallId = $functionInfo->getToolCallId();
echo "Tool Call ID". $functionInfo->getToolCallId()."\n";
    $messages = array_merge($messages, $functionInfo->callAndReturnAsOpenAIMessages());
print_r($messages);
    $response = $chat->generateChatOrReturnFunctionCalled($messages);
echo "Total Tokens ".$chat->getTotalTokens()."\n";
print_r($response);
