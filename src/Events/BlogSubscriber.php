<?php

declare(strict_types=1);

namespace App\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class BlogSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            BlogViewEvent::class => [
                ['obBlogViewFirst', 1],
                ['obBlogViewSecond', 2],
                ['obBlogViewThird', 3],
            ],
        ];
    }

    public function obBlogViewFirst(BlogViewEvent $event)
    {
        dump(1);
    }

    public function obBlogViewSecond(BlogViewEvent $event)
    {
        dump(2);

        $event->stopPropagation();
    }

    public function obBlogViewThird(BlogViewEvent $event)
    {
        try {
            $email = (new TemplatedEmail())
                ->from('root@localhost.local')
                ->to('admin@admin.com')
                ->subject('Some interested by blog')
                ->htmlTemplate('emails/blog-view.html.twig')
                ->context([
                    'blog' => $event->getBlog()
                ]);

            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            $d = 1;
        }
    }
}
