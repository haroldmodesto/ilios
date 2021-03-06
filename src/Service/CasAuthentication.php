<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Manager\AuthenticationManager;
use App\Traits\AuthenticationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class CasAuthentication
 */
class CasAuthentication implements AuthenticationInterface
{
    use AuthenticationService;

    /**
     * @var AuthenticationManager
     */
    protected $authManager;

    /**
     * @var JsonWebTokenManager
     */
    protected $jwtManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var CasManager
     */
    protected $casManager;

    /**
     * @var SessionUserProvider
     */
    protected $sessionUserProvider;

    /**
     * Constructor
     * @param AuthenticationManager $authManager
     * @param JsonWebTokenManager $jwtManager
     * @param LoggerInterface $logger
     * @param RouterInterface $router
     * @param CasManager $casManager
     * @param SessionUserProvider $sessionUserProvider
     */
    public function __construct(
        AuthenticationManager $authManager,
        JsonWebTokenManager $jwtManager,
        LoggerInterface $logger,
        RouterInterface $router,
        CasManager $casManager,
        SessionUserProvider $sessionUserProvider
    ) {
        $this->authManager = $authManager;
        $this->jwtManager = $jwtManager;
        $this->logger = $logger;
        $this->router = $router;
        $this->casManager = $casManager;
        $this->sessionUserProvider = $sessionUserProvider;
    }

    /**
     * Authenticate a user from shibboleth
     *
     * If the user is not yet logged in send a redirect Request
     * If the user is logged in, but no account exists send an error
     * If the user is authenticated send a JWT
     * @param Request $request
     *
     * @throws \Exception when the shibboleth attributes do not contain a value for the configured user id attribute
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $service = $request->query->get('service');
        $ticket = $request->query->get('ticket');

        if (!$ticket) {
            return new JsonResponse([
                'status' => 'redirect',
                'errors' => [],
                'jwt' => null,
            ], JsonResponse::HTTP_OK);
        }

        $userId = $this->casManager->getUserId($service, $ticket);
        if (!$userId) {
            $msg =  "No user found for authenticated user.";
            $this->logger->error($msg, ['server vars' => var_export($_SERVER, true)]);
            throw new \Exception($msg);
        }
        /* @var \App\Entity\AuthenticationInterface $authEntity */
        $authEntity = $this->authManager->findOneBy(['username' => $userId]);
        if ($authEntity) {
            $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($authEntity->getUser());
            if ($sessionUser->isEnabled()) {
                $jwt = $this->jwtManager->createJwtFromSessionUser($sessionUser);

                return $this->createSuccessResponseFromJWT($jwt);
            }
        }

        return new JsonResponse([
            'status' => 'noAccountExists',
            'userId' => $userId,
            'errors' => [],
            'jwt' => null,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Logout a user
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $logoutUrl = $this->casManager->getLogoutUrl();
        return new JsonResponse([
            'status' => 'redirect',
            'logoutUrl' => $logoutUrl
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @inheritdoc
     */
    public function getPublicConfigurationInformation(Request $request)
    {
        $configuration = [];
        $configuration['type'] = 'cas';
        $configuration['casLoginUrl'] = $this->casManager->getLoginUrl();

        return $configuration;
    }

    /**
     * @inheritdoc
     */
    public function createAuthenticationResponse(Request $request): Response
    {
        return new Response();
    }
}
