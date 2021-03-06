<?php

namespace App\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\JsonControllerTest;

class ApiControllerTest extends WebTestCase
{
    use JsonControllerTest;

    public function setUp()
    {
        $this->loadFixtures([
            'App\Tests\Fixture\LoadAuthenticationData'
        ]);
    }

    public function testNoEndpoint()
    {
        $client = $this->createClient();
        $this->makeJsonRequest(
            $client,
            'GET',
            '/api/v1/nothing',
            null,
            $this->getTokenForUser(1)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function testNoVersion()
    {
        $client = $this->createClient();
        $this->makeJsonRequest(
            $client,
            'GET',
            '/api/nothing',
            null,
            $this->getTokenForUser(1)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function testBadVersion()
    {
        $client = $this->createClient();
        $this->makeJsonRequest(
            $client,
            'GET',
            '/api/1/courses',
            null,
            $this->getTokenForUser(1)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }
}
