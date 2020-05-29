<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use App\Security\TokenGenerator;
use function array_intersect;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\BlogPost;
use function rand;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    private $faker;

    private $tokenGenerator;

    private const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'name' => 'admin',
            'password' => 'Hello123',
            'roles' => [User::USER_SUPERADMIN],
            'enabled' => true
        ],
        [
            'username' => 'user1',
            'email' => 'user1@gmail.com',
            'name' => 'user1',
            'password' => 'Hello123',
            'roles' => [User::USER_ADMIN],
            'enabled' => true
        ],
        [
            'username' => 'user2',
            'email' => 'user2@gmail.com',
            'name' => 'user2',
            'password' => 'Hello123',
            'roles' => [User::USER_WRITER],
            'enabled' => false
        ],
        [
            'username' => 'user3',
            'email' => 'user3@gmail.com',
            'name' => 'user3',
            'password' => 'Hello123',
            'roles' => [User::USER_COMMENTATOR],
            'enabled' => true
        ],
        [
            'username' => 'user4',
            'email' => 'user4@gmail.com',
            'name' => 'user4',
            'password' => 'Hello123',
            'roles' => [User::USER_EDITOR],
            'enabled' => false
        ],
        [
            'username' => 'user5',
            'email' => 'user5@gmail.com',
            'name' => 'user5',
            'password' => 'Hello123',
            'roles' => [User::USER_COMMENTATOR],
            'enabled' => true
        ]
    ];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator) {
        $this->encoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
        $this->tokenGenerator = $tokenGenerator;
    }
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadBlogPosts(ObjectManager $manager) {

        for($i=0;$i<100;$i++) {
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished($this->faker->dateTimeThisYear);
            $blogPost->setContent($this->faker->realText());

            $authorReference = $this->getRandomAuthored($blogPost);

            $blogPost->setAuthor($authorReference);
            $blogPost->setSlug($this->faker->slug);

            $this->setReference("blog_post_$i", $blogPost);

            $manager->persist($blogPost);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager) {
        for($i=0;$i<100;$i++) {
            for($j=0;$j<rand(1,10);$j++) {
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);

                $authorReference = $this->getRandomAuthored($comment);

                $comment->setAuthor($authorReference);
                $comment->setBlogPost($this->getReference("blog_post_$i"));

                $manager->persist($comment);
            }
        }
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager) {

        foreach(self::USERS as $userFixture) {
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setEmail($userFixture['email']);
            $user->setName($userFixture['name']);

            $user->setPassword($this->encoder->encodePassword($user ,$userFixture['password']));

            $user->setRoles($userFixture['roles']);
            $user->setPasswordChangeDate(null);
            $user->setEnabled($userFixture['enabled']);

            if(!$userFixture['enabled']) {
                $user->setConfirmationToken(
                    $this->tokenGenerator->getRandomSecureToken()
                );
            }

            $this->addReference('user_' . $userFixture['username'] , $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getRandomAuthored($entity): User{
        $randomUser = self::USERS[rand(0, 5)];

        if($entity instanceof BlogPost && !count(array_intersect($randomUser['roles'], [USER::USER_SUPERADMIN, USER::USER_ADMIN, USER::USER_WRITER]))) {
            return $this->getRandomAuthored($entity);
        }
        return $this->getReference('user_' . $randomUser['username']);
    }
}
