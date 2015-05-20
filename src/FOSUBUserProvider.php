<?php

namespace **NAMESPACEPREFIX**\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Monolog\Logger;
use \DateTime;


class FOSUBUserProvider extends BaseClass
{

    /**
     * {@inheritDoc}
     */
    protected $em;
    protected $request;
    protected $phittle;

    public function __construct(UserManagerInterface $userManager, EntityManager $em, RequestStack $requestStack, Logger $logger, array $properties, \Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        $this->userManager = $userManager;
        $this->em = $em;
        $this->request = $requestStack->getCurrentRequest();
        $this->properties = $properties;
        $this->logger = $logger;
    }

    /**
     * splits single name string into salutation, first, last, suffix
     *
     * @param string $name
     * @return array
     */
    public static function doSplitName($name)
    {
        $results = array();

        $r = explode(' ', $name);
        $size = count($r);

        //check first for period, assume salutation if so
        if (mb_strpos($r[0], '.') === false) {
            $results['salutation'] = '';
            $results['first'] = $r[0];
        } else {
            $results['salutation'] = $r[0];
            $results['first'] = $r[1];
        }

        //check last for period, assume suffix if so
        if (mb_strpos($r[$size - 1], '.') === false) {
            $results['suffix'] = '';
        } else {
            $results['suffix'] = $r[$size - 1];
        }

        //combine remains into last
        $start = ($results['salutation']) ? 2 : 1;
        $end = ($results['suffix']) ? $size - 2 : $size - 1;

        $last = '';
        for ($i = $start; $i <= $end; $i++) {
            $last .= ' ' . $r[$i];
        }
        $results['last'] = trim($last);

        return $results;
    }

    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
        $user_email = $response->getEmail();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $setter = 'set' . ucfirst($service);
        $setter_id = $setter . 'Id';
        $setter_token = $setter . 'AccessToken';

        //we "disconnect" previously connected users using OAUTH id
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        //Property here is the provider user identifier. For Facebook is facebook_id
        // For each provider ID fosuser entity has to have a property (and a db field)
        $property = $this->getProperty($response);
        $username = $response->getUsername();
        $userEmail = $response->getEmail();

        // Oauth Email is used to get the user (for example the facebook registered email)
        $user_by_email = $this->userManager->findUserByEmail($userEmail);
        // Oauth provider ID is used to get the user  (e.g. facebook user id)
        $user_by_oauthid = $this->userManager->findUserBy(array($property => $username));
        //when the user is registrating - We require that the user is not registered
        //with either the email or the oauth id
        // If userEmail is null - Means Facebook Permissions did not include Email
        if (null === $user_by_email && null === $user_by_oauthid && !is_null($userEmail)) {
            $this->logger->info('::LX:: NEW USER -> CREATING');
            $this->logger->info('::LX:: FACEBOOK RESPONSE' . print_r($response->getResponse(), 1));
            $service = $response->getResourceOwner()->getName();
            $setter = 'set' . ucfirst($service);
            $setter_id = $setter . 'Id';
            $setter_token = $setter . 'AccessToken';
            // create new user here - We set specific oauth service data (e.g. For Facebook this is facebook_id and facebook_access_token)
            $user = $this->userManager->createUser();
            $user->$setter_id($response->getUsername());
            $user->$setter_token($response->getAccessToken());
            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setUsername($userEmail);
            $user->setEmail($userEmail);
            if (isset($response->getResponse()['name'])) {
                $splitted_name = $this->doSplitName($response->getResponse()['name']);
                $user->setFullname($response->getResponse()['name']);
                $user->setName($splitted_name['first']);
                $user->setLastName($splitted_name['last']);
            }
            $user->setRegistrationDate(new \DateTime());
            $user->setPhoto("https://graph.facebook.com/" . $response->getUsername() . "/picture?width=260&height=260");
            //When creating a user, NickName will be the left part of the email
            $user->setEnabled(true);

            $this->userManager->updateUser($user);

            return $user;
        } elseif (is_null($userEmail)) {
            throw new EmailNotFoundException(sprintf('User "%d" Does not provide enough privileges.', $username));

        } elseif ($user_by_email->getFacebookId() == null || $user_by_email->getFacebookId() == "" || $user_by_email->getGoogleId() == null) {
            $this->logger->info("::LX:: THIS USER has an account with us but not a OAUTH Login - Merging ");
            // Setting this user OAUTH ID (e.g. Facebook ID) to their Account
            $service = $response->getResourceOwner()->getName();
            $setter = 'set' . ucfirst($service);
            $setter_id = $setter . 'Id';
            $setter_token = $setter . 'AccessToken';
            $user_by_email->$setter_id($response->getUsername());
            $user_by_email->$setter_token($response->getAccessToken());
            $this->userManager->updateUser($user_by_email);

        }

        //if user exists - go with the HWIOAuth way
        $user_by_email = parent::loadUserByOAuthUserResponse($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        //update access token
        $user_by_email->$setter($response->getAccessToken());

        return $user_by_email;
    }

}