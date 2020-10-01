<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\Blog;

class BlogListener
{
    private $event;

    public function onBlogView($event)
    {
        $this->event = $event;
    }

    /**
     * @return Blog
     */
    public function getEvent()
    {
        return $this->event;
    }
}
