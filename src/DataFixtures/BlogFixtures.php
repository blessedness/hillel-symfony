<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BlogFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        /** @var User $user */
        $user = $this->userRepository->findOneBy([
            'id' => 1
        ]);

        for ($i = 0; $i <= 50; $i++) {
            $blog = new Blog();
            $blog->setTitle(
                $faker->text(random_int(10, 30))
            );

            $blog->setDescription(
                $faker->text(random_int(1000, 5000))
            );

            $blog->setUser($user);

            for ($j = 0; $j <= rand(1, 5); $j++) {
                $tag = new Tag($faker->word);

                $blog->addTag($tag);
            }

            $manager->persist($blog);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixture::class,
        ];
    }
}
