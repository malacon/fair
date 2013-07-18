<?php
namespace CB\UserBundle\DependencyInjection\Security\Authentication;

use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class TimeAuthenticator extends JsonUserProvider implements SimpleFormAuthenticatorInterface
{
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        try {
            /** @var \CB\UserBundle\Entity\Family $user */
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException('Invalid username or password');
        }

        $passwordValid = $this->encoderFactory->getEncoder($user)->isPasswordValid($user->getPassword(), $token->getCredentials(), $user->getSalt());

        if ($passwordValid) {
            $currentTimeStamp = time();
            if (!$user->isTimeToLogin()) {
                throw new AuthenticationException('You can\'t log in until '.$user->getTimeToLogin()->format('l, F jS \a\t ga'), 100);
            }

            return new UsernamePasswordToken($user, 'bar', $providerKey, $user->getRoles());
        }

        throw new AuthenticationException('Invalid username or password');
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }
}