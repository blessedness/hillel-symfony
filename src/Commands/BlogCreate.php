<?php

declare(strict_types=1);

namespace App\Commands;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class BlogCreate extends AbstractCommand
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5")
     */
    public $title;
    /**
     * @Assert\NotBlank()
     */
    public $description;
    /**
     * @var User
     */
    public $user;

    public function __construct(string $title = null, string $description = null)
    {
        $this->title = $title;
        $this->description = $description;
    }
}
