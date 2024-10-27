<?php
namespace vindinium;

use GuzzleHttp\Client;
use vindinium\Bot\BubbelBot;


class Application
{
    const AUTH = 'blojjnup';
    const URL = 'http://vindinium.org/api/';
    private $mode = 'training';

    public function __construct()
    {
    }

    public function run()
    {
        $client = new Client([
            'base_uri' => self::URL
            ]
        );

        $response = $client->request('POST', $this->mode, ['form_params' => ['key' => self::AUTH, 'turns' => 40, 'map' => 'm3']]);
        if ($response->getStatusCode() == 200) {
            $state = $this->getState($response);
            echo 'Game running' ."\n";

            $bubbelBot = new BubbelBot($state);

            $gameUrl = $state->playUrl;
            while (!$this->isFinished($state)) {
                echo '. ' . $state->viewUrl . ' -> ' . $state->game->turn/4 . ' | ';

                $direction = $bubbelBot->move($state);

                $response = $client->request('POST', $gameUrl, ['form_params' => ['key' => self::AUTH, 'dir' => $direction]]);
                if ($response->getStatusCode() == 200) {
                    $state = $this->getState($response);
                }
            }
        } else {
            echo "Something went wrong.\n";
        }
        echo "\nfinished.\n";
    }

    private function getState($response)
    {
        $body = $response->getBody();
        $state = json_decode($body->getContents());

        return $state;
    }

    private function isFinished($state)
    {
        return $state->game->finished;
    }
}