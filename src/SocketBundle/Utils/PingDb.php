<?php
namespace SocketBundle\Utils;

use Doctrine\ORM\EntityManager;
use QuestBundle\Entity\Chapter;

class PingDb
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Quest constructor.
     * @param $entityManager
     */
    public function __construct($entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Ping Db
     */
    public function ping()
    {
        $this->em->getRepository(Chapter::class)
            ->find(0);
    }
}
