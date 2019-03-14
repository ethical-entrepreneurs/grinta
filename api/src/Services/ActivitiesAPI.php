<?php

namespace App\Services;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class ActivitiesAPI
{
    # http client requirements
    private const BASE_URI = 'http://event.event-api-nginx.traefik-crm-europe-pic.preprod.subsidia.org';
    private const X_API_KEY = 'grinta_api';
    private const JWT_TOKEN = 'eyJhbGciOiJSUzI1NiIsImtpZCI6Ik1BSU4ifQ.eyJzY29wZSI6WyJwcm9maWxlIiwib3BlbmlkIl0sImNsaWVudF9pZCI6ImRmZjM0NDUzMDIwNDc4ZGMyZjJlMDgzYmExYjBiYTU4MTFkNWE0NjciLCJpc3MiOiJpZHBkZWNhdGhsb24ucHJlcHJvZC5vcmciLCJqdGkiOiJxaWt5U0lZTzhZIiwic3ViIjoiWjAzQURBSSIsInVpZCI6IlowM0FEQUkiLCJleHAiOjE1NTI1NjkzNDZ9.em17ZuVD6t9DPx_2s9RA_ZGHwTt3ngiVsMuU6iec5aq3AXjlRKrgRlHFEC2foFiqdCXuYBVlO-TcwdPeIdvMypc4aExz_DzVZ9G73ozFcJVa9jsdJzcwae0zaFmfoIzgxdT6a45v2lkiQRj766Y7hUc9FcMEPTwrygIdWHHI0zFIysyU9cvZVn5DeMIfOSfKbf7RoLBW9LBb_6w4D4gmqAsCJ-nGoRxAM25ZyAo956VvwMm4gqVZm4hJXjc-AIL_RID1p2WCsVxo4NGHhkFabSYGJcRx1OGjjWtbWg93CN8HgxiZd8Gi5_zeNmyLNuyvber5IEBhLjUeOW3zvYKyOQ';

    # sport activities api endpoints
    private const GROUPS_URI = '/v1/groups';
    private const EVENTS_URI = '/v1/events';

    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URI,
            'headers' => [
                'x-api-key' => self::X_API_KEY,
                'Authorization' => 'Bearer ' . self::JWT_TOKEN,
                'cache-control' => 'no-cache',
                'Accept' => 'application/json'
            ]
        ]);
    }

    public function eventsAll(): ResponseInterface
    {
        return $this->client->get(self::EVENTS_URI . '?latitude=50.6725295&longitude=3.1489177999999356');
    }

    public function eventsGet(int $id): ResponseInterface
    {
        return $this->client->get(self::EVENTS_URI . '/' . $id);
    }

    public function eventsCreate(array $event): ResponseInterface
    {
        return $this->client->post(self::EVENTS_URI, ['json' => $event]);
    }

    public function groupsAll(): ResponseInterface
    {
        return $this->client->get(self::GROUPS_URI);
    }

    public function groupsGet(int $id): ResponseInterface
    {
        return $this->client->get(self::GROUPS_URI . '/' . $id);
    }

    public function groupsCreate(array $group): ResponseInterface
    {
        return $this->client->post(self::GROUPS_URI, ['json' => $group]);
    }
}
