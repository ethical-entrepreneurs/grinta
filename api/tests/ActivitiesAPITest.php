<?php

namespace App\Tests;

use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use App\Services\ActivitiesAPI;

class ActivitiesAPITest extends TestCase
{
    /** @var ActivitiesAPI $api */
    private $api;

    public function setUp()
    {
        parent::setUp();

        $this->api = new ActivitiesAPI();
    }

    public function test_events_all(): void
    {
        $response = $this->api->eventsAll();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_events_get(): void
    {
        $response = $this->api->eventsGet(167);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_events_create(): void
    {
        try {
            $response = $this->api->eventsCreate([
                'name' => 'Test name',
                'description' => 'Test description',
                'sponsor' => 'Test club partner',
                'sport' => 1,
                'proficiencyLevel' => [5, 10, 20],
                'startDate' => '2019-02-01T09:00:00Z',
                'endDate' => '2019-02-01T10:00:00Z',
                'location' => [
                    'identifier' => 'ChIJSW0ePExp0TgRKUQheDVH3-s',
                    'name' => 'Decathlon Croix',
                    'address' => [
                        'streetAddress' => '12 rue de la Centenaire',
                        'postalCode' => '59650',
                        'addressLocality' => 'Villeneuve d\'Ascq',
                        'addressCountry' => 'France',
                    ],
                    'geo' => [
                        'latitude' => 34.5553494,
                        'longitude' => 69.207486,
                    ],
                ],
                'inLanguage' => ['fr', 'en'],
                'isAccessibleForFree' => false,
                'acceptsReservations' => true,
                'typicalAgeRange' => '18-100',
                'maximumAttendeeCapacity' => 10,
                'isCertified' => false,
                'isSupervised' => false,
                'isAccessibleForDisabled' => false,
                'offers' => [
                    'price' => '19.5',
                    'priceCurrency' => 'EUR',
                    'availability' => 'InStock',
                    'validFrom' => '2018-10-30T09:51:54Z',
                ],
                'organizer' => [
                    'identifier' => '1866e72f-2cd7-4f81-bdfc-a8e84b9658bb',
                    'name' => 'Jean Jacques',
                    'email' => 'jeanjacques@goldman.com',
                    'additionalType' => 'FED',
                    'externalId' => '84b04715-329b-33f6-9c4e-e198761a9d35',
                ],
            ]);
        } catch(ClientException $e) {
            dump($e->getResponse()->getBody()->getContents());die;
        }


        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_groups_all(): void
    {
        $response = $this->api->groupsAll();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_groups_get(): void
    {
        $response = $this->api->groupsGet(2);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_groups_create(): void
    {
        try {
            $response = $this->api->groupsCreate([
                'title' => 'grinta challenge #1',
                'description' => 'grinta challenge #1',
                'subEvents' => [
                    167
                ]
            ]);
        } catch(ClientException $e) {
            dump($e->getResponse()->getBody()->getContents());die;
        }


        $this->assertEquals(201, $response->getStatusCode());
    }
}
