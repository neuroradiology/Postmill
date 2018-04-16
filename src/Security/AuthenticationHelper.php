<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;

class AuthenticationHelper {
    /**
     * @var FirewallMap
     */
    private $firewallMap;

    /**
     * @var RememberMeServicesInterface
     */
    private $rememberMeServices;

    /**
     * @var string
     */
    private $secret;

    public function __construct(
        FirewallMap $firewallMap,
        RememberMeServicesInterface $rememberMeServices,
        string $secret
    ) {
        $this->firewallMap = $firewallMap;
        $this->rememberMeServices = $rememberMeServices;
        $this->secret = $secret;
    }

    /**
     * Programmatically set a user as logged in.
     *
     * @param User          $user
     * @param Request       $request
     * @param Response|null $response
     *
     * @return Response provided response, or a new one if none was provided
     */
    public function login(User $user, Request $request, Response $response = null): Response {
        $config = $this->firewallMap->getFirewallConfig($request);

        if (!$config) {
            throw new \BadMethodCallException('No firewall for this request');
        }

        $token = new RememberMeToken($user, $config->getName(), $this->secret);

        $response = $response ?: new Response();

        $this->rememberMeServices->loginSuccess($request, $response, $token);

        return $response;
    }
}
