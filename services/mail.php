<?php

require __DIR__ . '/../vendor/autoload.php';

use \Mailjet\Resources;

class Mail
{
    private static $API_KEY = 'c5e11d7b1c3b63b4a5a78a94b2621540';
    private static $SECRET_KEY = '9369011ae0a4dee9e17d6f9d4dc70260';

    public static function sendAccountInvitation($toEmail, $toName, $hash)
    {
        $mj = new \Mailjet\Client(self::$API_KEY, self::$SECRET_KEY, true, ['version' => 'v3.1']);

        // Define your request body

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "adriana.gavrilut21@gmail.com",
                        'Name' => "Office"
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => $toName
                        ]
                    ],
                    'Subject' => "Link activare cont!",
                    'TextPart' => "Greetings from Mailjet!",
                    'HTMLPart' => "<h3>$toName, pentru activarea contului intrați <a href=\"" . BASE_URL . "/admin/activare.php?hash=$hash\">aici</a>!</h3>
            <br />Vă mulțumim!"
                ]
            ]
        ];

        // All resources are located in the Resources class

        $response = $mj->post(Resources::$Email, ['body' => $body]);

        // Read the response

        $response->success();
        // $response->success() && var_dump($response->getData());
    }


    public static function sendResetPasswordEmail($toEmail, $toName, $hash)
    {

        $mj = new \Mailjet\Client(self::$API_KEY, self::$SECRET_KEY, true, ['version' => 'v3.1']);

        // Define your request body

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "adriana.gavrilut21@gmail.com",
                        'Name' => "Office"
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => $toName
                        ]
                    ],
                    'Subject' => "Link resetare parolă!",
                    'TextPart' => "Greetings from Mailjet!",
                    'HTMLPart' => "<h3>$toName, pentru resetarea parolei intrați <a href=\"" . BASE_URL . "/admin/resetare.php?hash=$hash\">aici</a>!</h3>
            <br />Dacă nu ați solicitat dvs. resetarea parolei ignorați acest email."
                ]
            ]
        ];

        // All resources are located in the Resources class

        $response = $mj->post(Resources::$Email, ['body' => $body]);

        // Read the response

        $response->success();
    }
}
