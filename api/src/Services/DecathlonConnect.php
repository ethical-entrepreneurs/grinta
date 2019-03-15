<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Dotenv\Dotenv;
use Emarref;

class DecathlonConnect
{
    private $CONNECT_BASE_URI;
    private $CONNECT_CLIENT_ID;
    private $CONNECT_CLIENT_SECRET;
    private $CONNECT_REDIRECT_URI;
    private $PROFILE_BASE_URI;
    private $PROFILE_API_KEY;
    private $userRepository;
    private $manager;

    public function __construct(UserRepository $userRepository, ObjectManager $manager)
    {
        $this->CONNECT_REDIRECT_URI = getenv('CONNECT_REDIRECT_URI');
        $this->CONNECT_BASE_URI = getenv('CONNECT_BASE_URI');
        $this->CONNECT_CLIENT_SECRET = getenv('CONNECT_CLIENT_SECRET');
        $this->CONNECT_CLIENT_ID = getenv('CONNECT_CLIENT_ID');
        $this->PROFILE_API_KEY = getenv('PROFILE_API_KEY');
        $this->PROFILE_BASE_URI = getenv('PROFILE_BASE_URI');
        $this->userRepository = $userRepository;
        $this->manager = $manager;
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
        $jwt = new Emarref\Jwt\Jwt();
        $token = $jwt->deserialize($bearer);
        $userId = $token->getPayload()->findClaimByName('sub')->getValue();

        $identity = $this->requestSportsUser('sports_user/identity', $bearer);
        $contact = $this->requestSportsUser('sports_user/contacts', $bearer);

        $internalUserId = $this->findUserDb($userId, $contact);

        return array_merge(
            $identity,
            $contact,
            ['user_id' => $internalUserId, 'decathlon_connect_id' => $userId]
        );
    }

    public function findUserDb(string $decathlonConnectId, array $contact): int
    {
        $user = $this
            ->userRepository
            ->findOneBy(['decathlonConnectId' => $decathlonConnectId]);

        if (!$user) {
            $email = $contact['email']['value'] ?? null;
            $newUser = new User();

            $newUser
                ->setPassword(uniqid())
                ->setDecathlonConnectId($decathlonConnectId)
                ->setEmail($email)
                ;
            $this->manager->persist($newUser);
            $this->manager->flush();

            return $newUser->getId();
        }

        return $user->getId();
    }

    private function requestSportsUser(string $endpoint, string $bearer): array
    {
        $client = new Client([
            'base_uri' => $this->PROFILE_BASE_URI,
        ]);

        try {
            $response = $client->request('GET', $endpoint, [
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
