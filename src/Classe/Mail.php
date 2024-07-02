<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    public function send($to_email, $to_name, $subject, $template, $vars = null)
    {
        // Récupération fichier template
        $content = file_get_contents(dirname(__DIR__) . '/Mail/' . $template);

        // Récupération variables pour template
        if ($vars){
            foreach ($vars as $key => $var) {
                $content = str_replace('{' . $key . '}', $var, $content);
            }
        }
        $apikey = $_ENV['MAILJET_API_KEY'];
        $secretKey = $_ENV['MAILJET_SECRET_KEY'];
        $mailjetMail = $_ENV['MAILJET_EMAIL'];
        $mailJetName = $_ENV['MAILJET_NAME'];

        $mailJet = new Client($apikey, $secretKey, true, ['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $mailjetMail,
                        'Name' => $mailJetName
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 6103340, // Template crée sur MailJet
                    'TemplateLanguage' => true, // Toujours à true
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ], // Variables pour le template

                    // 'TextPart' => "Greetings from Mailjet!",
                    // 'HTMLPart' => $content
                ]
            ]
        ];

        // envoyer email
        $mailJet->post(Resources::$Email, ['body' => $body]);
    }
}
