<?php


namespace App\Manager;

use Doctrine\ORM\EntityManagerInterface;

class BaseManager
{
    public $CODE_KEY = "code";
    public $STATUS_KEY = "status";
    public $MESSAGE_KEY = "message";
    public $DATA_KEY = "data";
    public $em;
    public function  __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
}