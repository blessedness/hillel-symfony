<?php

declare(strict_types=1);

namespace App\Services;

use App\Commands\BlogCreate;
use App\Entity\Blog;
use Doctrine\ORM\EntityManager;

class BlogService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * BlogService constructor.
     * @param $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function create(BlogCreate $command): void
    {
        $blog = new Blog();

        $blog->setTitle(
            $command->title
        );

        $blog->setDescription(
            $command->description
        );

        $blog->setUser(
            $command->user
        );

        $this->em->persist($blog);
        $this->em->flush();
    }

    public function update(Blog $blog, BlogCreate $command)
    {
        $blog->setTitle(
            $command->title
        );

        $blog->setDescription(
            $command->description
        );

        $this->em->flush();
    }
}
