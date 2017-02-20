<?php

namespace Tests\IliosApiBundle\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Authentication API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class AuthenticationTest extends AbstractEndpointTest
{
    protected $testName =  'authentications';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAuthenticationData',
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadAlertData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadInstructorGroupData',
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
            'Tests\CoreBundle\Fixture\LoadUserMadeReminderData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadPendingUserUpdateData',
            'Tests\CoreBundle\Fixture\LoadPermissionData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadReportData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'username' => ['username', $this->getFaker()->text(100)],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
    {
        return [
            'invalidateTokenIssuedBefore' => ['invalidateTokenIssuedBefore', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'user' => [[1], ['user' => 2]],
            'username' => [[1], ['username' => 'newuser']],
        ];
    }

    protected function createMany($count)
    {
        $userDataLoader = $this->container->get('ilioscore.dataloader.user');
        $users = $userDataLoader->createMany($count);
        $savedUsers = $this->postMany('users', $users);

        $dataLoader = $this->getDataLoader();

        $data = array_map(function ($user) use ($dataLoader) {
            $arr = $dataLoader->create();
            $arr['user'] = (string) $user['id'];
            $arr['username'] .= $user['id'];

            return $arr;
        }, $savedUsers);

        return $data;
    }

    protected function compareData(array $expected, array $result)
    {
        unset($expected['passwordSha256']);
        unset($expected['passwordBcrypt']);
        unset($expected['password']);
        $this->assertEquals(
            $expected,
            $result
        );
    }

    public function testPostMultipleAuthenticationWithEmptyPassword()
    {
        $data = $this->createMany(101);
        $data = array_map(function ($arr) {
            unset($arr['password']);
            return $arr;
        }, $data);
        $this->postManyTest($data);
    }

    public function testPostAuthenticationWithEmptyPassword()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['password']);

        $this->postTest($data, $data);
    }

    public function testPutAuthenticationWithNewUsernameAndPassword()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['username'] = 'somethingnew';
        $data['password'] = 'somethingnew';

        $this->putTest($data, $data, $data['user']);
    }

    public function testPostAuthenticationForUserWithNonPrimarySchool()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['user'] = '4';
        $user4 = $this->getOne('users', 4);
        $this->assertSame($user4['school'], '2', 'User #4 should be in school 2 or this test is garbage');

        $this->postTest($data, $data);
    }

    public function testPostAuthenticationWithNoUsernameOrPassword()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['username']);
        unset($data['password']);

        $this->postTest($data, $data);
    }

    /**
     * Overridden because authenticaiton users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    public function testDelete()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->deleteTest($data['user']);
    }

    /**
     * Overridden because authenticaiton users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function getOneTest()
    {
        $pluralObjectName = $this->getPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $returnedData = $this->getOne($pluralObjectName, $data['user']);
        $this->compareData($data, $returnedData);

        return $returnedData;
    }

    /**
     * Overridden because authenticaiton users
     * 'user' as the Primary Key
     * @dataProvider putsToTest
     */
    public function testPut($key, $value)
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        if (array_key_exists($key, $data) and $data[$key] == $value) {
            $this->fail(
                "This value is already set for {$key}. " .
                "Modify " . get_class($this) . '::putsToTest'
            );
        }
        $data[$key] = $value;

        $postData = $data;
        $this->putTest($data, $postData, $data['user']);
    }

    /**
     * Overridden because authenticaiton users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    public function testPutForAllData()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $faker = $this->getFaker();
        foreach ($all as $data) {
            $data['username'] = $faker->text(50);

            $this->putTest($data, $data, $data['user']);
        }
    }


    /**
     * Overridden because authenticaiton users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    public function testPostMany()
    {
        $data = $this->createMany(10);
        $this->postManyTest($data);
    }

    /**
     * Overridden because authenticaiton users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function postTest($data, $postData)
    {
        $pluralObjectName = $this->getPluralName();
        $responseData = $this->postOne($pluralObjectName, $postData);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($pluralObjectName, $responseData['user']);
        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Overridden because authenticaiton users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function putTest($data, $postData, $id)
    {
        $pluralObjectName = $this->getPluralName();
        $responseData = $this->putOne($pluralObjectName, $id, $postData);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($pluralObjectName, $responseData['user']);
        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Overridden because authenticaiton users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function postManyTest($data)
    {
        $pluralObjectName = $this->getPluralName();
        $responseData = $this->postMany($pluralObjectName, $data);
        $ids = array_map(function (array $arr) {
            return $arr['user'];
        }, $responseData);
        $filters = [
            'filters[user]' => $ids,
            'limit' => count($ids)
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($pluralObjectName, $filters);

        foreach ($data as $i => $datum) {
            $response = $fetchedResponseData[$i];
            $this->compareData($datum, $response);
        }

        return $fetchedResponseData;
    }

    /**
     * Overridden because authenticaiton users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function getOne($pluralObjectName, $userId)
    {
        $url = $this->getUrl(
            'ilios_api_authentication_get',
            ['version' => 'v1', 'object' => $pluralObjectName, 'userId' => $userId]
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);
        return json_decode($response->getContent(), true)[$pluralObjectName][0];
    }

}