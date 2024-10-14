<?php

use LLPhant\OpenAIConfig;
use LLPhant\Chat\OpenAIChat;
use LLPhant\Chat\FunctionInfo\FunctionBuilder;

class MailerExample
{
    /**
     * This function send an email
     */
    public function sendMail(string $subject, string $body, string $email): string
    {
        $msg = 'The email has been sent to '.$email.' with the subject '.$subject.' and the body '.$body.'.';
        echo $msg;
        return $msg;
    }
}

include __DIR__.'/../../vendor/autoload.php';

$config = new OpenAIConfig();
$config->apiKey = '-';
$config->url = 'http://skynet.interserver.net:8080/v1';
$config->model = 'gpt-4';
$chat = new OpenAIChat($config);
$tool = FunctionBuilder::buildFunctionInfo(new MailerExample(), 'sendMail');
$chat->addTool($tool);
$chat->setSystemMessage('You are an AI that deliver information using the email system.
When you have enough information to answer the question of the user you send a mail');
$response = $chat->generateText('Who is Marie Curie in one line? My email is student@foo.com');
echo "\nResponse:";
print_r($response);
echo "\n";
exit;
