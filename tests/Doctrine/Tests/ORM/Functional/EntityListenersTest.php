<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Functional;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Tests\Models\Company\CompanyContractListener;
use Doctrine\Tests\Models\Company\CompanyFixContract;
use Doctrine\Tests\OrmFunctionalTestCase;

/**
 * @group DDC-1955
 */
class EntityListenersTest extends OrmFunctionalTestCase
{

    /**
     * @var \Doctrine\Tests\Models\Company\CompanyContractListener
     */
    private $listener;

    protected function setUp()
    {
        $this->useModelSet('company');
        parent::setUp();

        $this->listener = $this->em->getConfiguration()
            ->getEntityListenerResolver()
            ->resolve(CompanyContractListener::class);
    }

    public function testPreFlushListeners()
    {
        $fix = new CompanyFixContract();
        $fix->setFixPrice(2000);

        $this->listener->preFlushCalls  = [];

        $this->em->persist($fix);
        $this->em->flush();

        self::assertCount(1, $this->listener->preFlushCalls);
        self::assertSame($fix, $this->listener->preFlushCalls[0][0]);
        self::assertInstanceOf(CompanyFixContract::class, $this->listener->preFlushCalls[0][0]);
        self::assertInstanceOf(PreFlushEventArgs::class, $this->listener->preFlushCalls[0][1]);
    }

    public function testPostLoadListeners()
    {
        $fix = new CompanyFixContract();
        $fix->setFixPrice(2000);

        $this->em->persist($fix);
        $this->em->flush();
        $this->em->clear();

        $this->listener->postLoadCalls  = [];

        $dql = "SELECT f FROM Doctrine\Tests\Models\Company\CompanyFixContract f WHERE f.id = ?1";
        $fix = $this->em->createQuery($dql)->setParameter(1, $fix->getId())->getSingleResult();

        self::assertCount(1, $this->listener->postLoadCalls);
        self::assertSame($fix, $this->listener->postLoadCalls[0][0]);
        self::assertInstanceOf(CompanyFixContract::class, $this->listener->postLoadCalls[0][0]);
        self::assertInstanceOf(LifecycleEventArgs::class, $this->listener->postLoadCalls[0][1]);
    }

    public function testPrePersistListeners()
    {
        $fix = new CompanyFixContract();
        $fix->setFixPrice(2000);

        $this->listener->prePersistCalls  = [];

        $this->em->persist($fix);
        $this->em->flush();

        self::assertCount(1, $this->listener->prePersistCalls);
        self::assertSame($fix, $this->listener->prePersistCalls[0][0]);
        self::assertInstanceOf(CompanyFixContract::class, $this->listener->prePersistCalls[0][0]);
        self::assertInstanceOf(LifecycleEventArgs::class, $this->listener->prePersistCalls[0][1]);
    }

    public function testPostPersistListeners()
    {
        $fix = new CompanyFixContract();
        $fix->setFixPrice(2000);

        $this->listener->postPersistCalls = [];

        $this->em->persist($fix);
        $this->em->flush();

        self::assertCount(1, $this->listener->postPersistCalls);
        self::assertSame($fix, $this->listener->postPersistCalls[0][0]);
        self::assertInstanceOf(CompanyFixContract::class, $this->listener->postPersistCalls[0][0]);
        self::assertInstanceOf(LifecycleEventArgs::class, $this->listener->postPersistCalls[0][1]);
    }

    public function testPreUpdateListeners()
    {
        $fix = new CompanyFixContract();
        $fix->setFixPrice(1000);

        $this->em->persist($fix);
        $this->em->flush();

        $this->listener->preUpdateCalls = [];

        $fix->setFixPrice(2000);

        $this->em->persist($fix);
        $this->em->flush();

        self::assertCount(1, $this->listener->preUpdateCalls);
        self::assertSame($fix, $this->listener->preUpdateCalls[0][0]);
        self::assertInstanceOf(CompanyFixContract::class, $this->listener->preUpdateCalls[0][0]);
        self::assertInstanceOf(PreUpdateEventArgs::class, $this->listener->preUpdateCalls[0][1]);
    }

    public function testPostUpdateListeners()
    {
        $fix = new CompanyFixContract();
        $fix->setFixPrice(1000);

        $this->em->persist($fix);
        $this->em->flush();

        $this->listener->postUpdateCalls = [];

        $fix->setFixPrice(2000);

        $this->em->persist($fix);
        $this->em->flush();

        self::assertCount(1, $this->listener->postUpdateCalls);
        self::assertSame($fix, $this->listener->postUpdateCalls[0][0]);
        self::assertInstanceOf(CompanyFixContract::class, $this->listener->postUpdateCalls[0][0]);
        self::assertInstanceOf(LifecycleEventArgs::class, $this->listener->postUpdateCalls[0][1]);
    }

    public function testPreRemoveListeners()
    {
        $fix = new CompanyFixContract();
        $fix->setFixPrice(1000);

        $this->em->persist($fix);
        $this->em->flush();

        $this->listener->preRemoveCalls = [];

        $this->em->remove($fix);
        $this->em->flush();

        self::assertCount(1, $this->listener->preRemoveCalls);
        self::assertSame($fix, $this->listener->preRemoveCalls[0][0]);
        self::assertInstanceOf(CompanyFixContract::class, $this->listener->preRemoveCalls[0][0]);
        self::assertInstanceOf(LifecycleEventArgs::class, $this->listener->preRemoveCalls[0][1]);
    }

    public function testPostRemoveListeners()
    {
        $fix = new CompanyFixContract();
        $fix->setFixPrice(1000);

        $this->em->persist($fix);
        $this->em->flush();

        $this->listener->postRemoveCalls = [];

        $this->em->remove($fix);
        $this->em->flush();

        self::assertCount(1, $this->listener->postRemoveCalls);
        self::assertSame($fix, $this->listener->postRemoveCalls[0][0]);
        self::assertInstanceOf(CompanyFixContract::class, $this->listener->postRemoveCalls[0][0]);
        self::assertInstanceOf(LifecycleEventArgs::class, $this->listener->postRemoveCalls[0][1]);
    }
}
