<?php

namespace App\Controller;

use App\Services\DecathlonConnect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class DecathlonConnectController extends AbstractController
{
    private $CONNECT_BASE_URI;
    private $CONNECT_CLIENT_ID;
    private $CONNECT_CLIENT_SECRET;
    private $CONNECT_REDIRECT_URI;
    private $PROFILE_BASE_URI;
    private $PROFILE_API_KEY;
    private $decathlonConnect;

    public function __construct(DecathlonConnect $decathlonConnect)
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../../.env');
        $this->CONNECT_REDIRECT_URI = getenv('CONNECT_REDIRECT_URI');
        $this->CONNECT_BASE_URI = getenv('CONNECT_BASE_URI');
        $this->CONNECT_CLIENT_SECRET = getenv('CONNECT_CLIENT_SECRET');
        $this->CONNECT_CLIENT_ID = getenv('CONNECT_CLIENT_ID');
        $this->PROFILE_API_KEY = getenv('PROFILE_API_KEY');
        $this->PROFILE_BASE_URI = getenv('PROFILE_BASE_URI');

        $this->decathlonConnect = $decathlonConnect;
    }

    /**
     * @Route("/confirm_login", name="confirm_login")
     */
    public function confirmLogin(Request $request)
    {
        $code = $request->query->get('code') ?? null;
        $state = $request->query->get('state') ?? null;

        if (!is_null($code) && !is_null($state)) {
            $response = $this->decathlonConnect->authorize($code);

            $content = json_decode($response->getBody()->getContents(), true);
            if (200 === $response->getStatusCode()) {
                $result = array_merge(
                    $content,
                    $this->decathlonConnect->profile($content['access_token'])
                );

                return $this->json($result);
            }

            if (400 == $response->getStatusCode()) {
                throw new HttpException(400, $content['error_description']);
            }
        }

        return $this->json([]);
    }
}
