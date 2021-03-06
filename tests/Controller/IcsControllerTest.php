<?php
namespace App\Tests\Controller;

use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\SessionData;
use App\Tests\Traits\JsonControllerTest;

/**
 * Class ConfigControllerTest
 */
class IcsControllerTest extends WebTestCase
{
    use JsonControllerTest;

    /**
     * @var ProxyReferenceRepository
     */
    protected $fixtures;

    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        parent::setUp();
        $this->client = $this->makeClient();
        $this->fixtures = $this->loadFixtures([
            'App\Tests\Fixture\LoadAuthenticationData',
            'App\Tests\Fixture\LoadOfferingData',
            'App\Tests\Fixture\LoadCourseLearningMaterialData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadSessionDescriptionData',
        ])->getReferenceRepository();
    }

    public function tearDown() : void
    {
        parent::tearDown();
        unset($this->client);
        unset($this->fixtures);
    }

    public function testSessionAttributesShowUp()
    {
        $container = $this->client->getContainer();
        $session = $container->get(SessionData::class)->getOne();
        $session['attireRequired'] = true;
        $session['equipmentRequired'] = true;
        $session['attendanceRequired'] = true;
        $id = $session['id'];
        $this->makeJsonRequest(
            $this->client,
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'sessions', 'id' => $id]),
            json_encode(['session' => $session]),
            $this->getTokenForUser(2)
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);

        $url = '/ics/' . hash('sha256', '1');
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        $content = $response->getContent();

        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Status Code: {$response->getStatusCode()} is not OK"
        );
        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'text/calendar; charset=utf-8'
            ),
            var_export($response->headers, true)
        );

        $this->assertTrue(
            (!empty($content)),
            $content
        );
        $matches = [];
        preg_match('/DESCRIPTION:(.+?)DTSTAMP/s', $content, $matches);
        $this->assertEquals(count($matches), 2, 'Found description in response');
        $firstDescription = preg_replace('/\s+/', '', $matches[1]);

        $this->assertRegExp(
            '#Youwillneedspecialattire#',
            $firstDescription,
            'Special attire required shows up'
        );

        $this->assertRegExp(
            '#Youwillneedspecialequipment#',
            $firstDescription,
            'Special equiptment required shows up'
        );

        $this->assertRegExp(
            '#AttendanceisRequired#',
            $firstDescription,
            'Attendance required shows up'
        );
    }

    public function testSessionAttributesAreHidden()
    {
        $container = $this->client->getContainer();
        $session = $container->get(SessionData::class)->getOne();
        $session['attireRequired'] = false;
        $session['equipmentRequired'] = false;
        $session['attendanceRequired'] = false;
        $id = $session['id'];
        $this->makeJsonRequest(
            $this->client,
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'sessions', 'id' => $id]),
            json_encode(['session' => $session]),
            $this->getTokenForUser(2)
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);

        $url = '/ics/' . hash('sha256', '1');
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        $content = $response->getContent();

        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Status Code: {$response->getStatusCode()} is not OK"
        );
        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'text/calendar; charset=utf-8'
            ),
            var_export($response->headers, true)
        );

        $this->assertTrue(
            (!empty($content)),
            $content
        );
        $matches = [];
        preg_match('/DESCRIPTION:(.+?)DTSTAMP/s', $content, $matches);
        $this->assertEquals(count($matches), 2, 'Found description in response');
        $firstDescription = preg_replace('/\s+/', '', $matches[1]);

        $this->assertNotRegExp(
            '#Youwillneedspecialattire#',
            $firstDescription,
            'Special attire required is hidden'
        );

        $this->assertNotRegExp(
            '#Youwillneedspecialequipment#',
            $firstDescription,
            'Special equiptment required is hidden'
        );

        $this->assertNotRegExp(
            '#AttendanceisRequired#',
            $firstDescription,
            'Attendance required is hidden'
        );
    }

    public function testAbsolutePathsToEvents()
    {
        $url = '/ics/' . hash('sha256', '1');
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        $content = $response->getContent();

        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Status Code: {$response->getStatusCode()} is not OK"
        );
        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'text/calendar; charset=utf-8'
            ),
            var_export($response->headers, true)
        );

        $this->assertTrue(
            (!empty($content)),
            $content
        );
        $matches = [];
        preg_match('/DESCRIPTION:(.+?)DTSTAMP/s', $content, $matches);
        $this->assertEquals(count($matches), 2, 'Found description in response');
        $firstDescription = preg_replace('/\s+/', '', $matches[1]);

        $today = new \DateTime();
        $format = $today->format('Ymd');

        $this->assertRegExp(
            "#http://localhost/events/U${format}O2#",
            $firstDescription,
            'Event Links are absolute paths'
        );
    }
}
