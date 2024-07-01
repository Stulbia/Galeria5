<?php

/**
 * Comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\comment;
use App\Entity\Photo;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CommentFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        $this->createMany(20, 'comments', function (int $i) {
            $comment = new comment();
            $comment->setContent($this->faker->sentence);
            $comment->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $comment->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );

            /** @var Photo $photo
             */
            $photo = $this->getRandomReference('photos');
            $comment->setPhoto($photo);

            /** @var User $user
             */
            $user = $this->getRandomReference('users');
            $comment->setUser($user);

            return $comment;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: PhotoFixtures::class}
     */
    public function getDependencies(): array
    {
        return [PhotoFixtures::class];
    }
}
