<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\Blog;
use Symfony\Contracts\EventDispatcher\Event;

class BlogViewEvent extends Event
{
    public const NAME_VIEW = 'blog.view';

    /**
     * @var Blog
     */
    private $blog;

    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }

    /**
     * @return Blog
     */
    public function getBlog(): Blog
    {
        return $this->blog;
    }
}
