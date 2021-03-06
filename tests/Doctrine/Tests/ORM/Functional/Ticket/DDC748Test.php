<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\Common\Collections\Collection;
use Doctrine\Tests\Models\CMS\CmsAddress;
use Doctrine\Tests\Models\CMS\CmsArticle;
use Doctrine\Tests\Models\CMS\CmsUser;

class DDC748Test extends \Doctrine\Tests\OrmFunctionalTestCase
{
    protected function setUp()
    {
        $this->useModelSet('cms');
        parent::setUp();
    }

    public function testRefreshWithManyToOne()
    {
        $user = new CmsUser();
        $user->name = "beberlei";
        $user->status = "active";
        $user->username = "beberlei";

        $article = new CmsArticle();
        $article->setAuthor($user);
        $article->text = "foo";
        $article->topic = "bar";

        $this->em->persist($user);
        $this->em->persist($article);
        $this->em->flush();

        self::assertInstanceOf(Collection::class, $user->articles);
        $this->em->refresh($article);
        self::assertNotSame($article, $user->articles, "The article should not be replaced on the inverse side of the relation.");
        self::assertInstanceOf(Collection::class, $user->articles);
    }

    public function testRefreshOneToOne()
    {
        $user = new CmsUser();
        $user->name = "beberlei";
        $user->status = "active";
        $user->username = "beberlei";

        $address = new CmsAddress();
        $address->city = "Bonn";
        $address->country = "Germany";
        $address->street = "A street";
        $address->zip = 12345;
        $address->setUser($user);

        $this->em->persist($user);
        $this->em->persist($address);
        $this->em->flush();

        $this->em->refresh($address);
        self::assertSame($user, $address->user);
        self::assertSame($user->address, $address);
    }
}
