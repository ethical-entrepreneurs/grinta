<?php

namespace App\Services;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Dotenv\Dotenv;

class DecathlonConnect
{
    private $CONNECT_BASE_URI;
    private $CONNECT_CLIENT_ID;
    private $CONNECT_CLIENT_SECRET;
    private $CONNECT_REDIRECT_URI;
    private $PROFILE_BASE_URI;
    private $PROFILE_API_KEY;

    public function __construct()
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../../.env');
        $this->CONNECT_REDIRECT_URI = getenv('CONNECT_REDIRECT_URI');
        $this->CONNECT_BASE_URI = getenv('CONNECT_BASE_URI');
        $this->CONNECT_CLIENT_SECRET = getenv('CONNECT_CLIENT_SECRET');
        $this->CONNECT_CLIENT_ID = getenv('CONNECT_CLIENT_ID');
        $this->PROFILE_API_KEY = getenv('PROFILE_API_KEY');
        $this->PROFILE_BASE_URI = getenv('PROFILE_BASE_URI');
    }

    public function authorize(string $code): ResponseInterface
    {
        $client = new Client([
            'base_uri' => $this->CONNECT_BASE_URI,
        ]);

        return $client->request('POST', 'connect/oauth/token', [
            'headers' => [
                'content-type' => 'application/json',
            ],
            'query' => [
                'client_id' => $this->CONNECT_CLIENT_ID,
                'client_secret' => $this->CONNECT_CLIENT_SECRET,
                'code' => $code,
                'redirect_uri' => $this->CONNECT_REDIRECT_URI,
                'grant_type' => 'authorization_code',
            ],
        ]);
    }

    public function profile(string $bearer): array
    {
        $client = new Client([
            'base_uri' => $this->PROFILE_BASE_URI,
        ]);

        try {
            $response = $client->request('GET', 'sports_user/identity', [
                'headers' => [
                    'content-type' => 'application/json',
                    'x-api-key' => $this->PROFILE_API_KEY,
                    'authorization' => sprintf('Bearer %s', $bearer),
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return [];
        }
    }
}
