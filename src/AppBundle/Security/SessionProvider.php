<?php
namespace AppBundle\Security;

use AppBundle\Entity\Session;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class SessionProvider
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * SessionProvider constructor.
     * @param $entityManager
     */
    public function __construct($entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $token
     * @return User|null
     */
    public function loadUserByToken($token = null)
    {
        if (is_null($token)) {
            return null;
        }
        /** @var Session $session */
        $session = $this->em
            ->getRepository(Session::class)
            ->findOneByToken($token);

        return ($session)
            ? $session->getUser()
            : null;
    }

    /**
     * @param User $user
     * @return string
     */
    public function login(User $user)
    {
        $session = $this->em
            ->getRepository(Session::class)
            ->findOneByUser($user);

        if (!$session) {
            $session = new Session();
            $session->setUser($user);
        };

        $token = sha1(microtime() . $user->getId());

        $session->setToken($token);
        $this->em->persist($session);
        $this->em->flush();

        return $token;
    }
}
