<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Message;
use App\Entity\Notation;
use App\Entity\Recommandation;
use App\Entity\Session;
use App\Entity\Skill;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\SessionRepository;
use App\Repository\SkillRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private readonly object $faker;
    private readonly UserPasswordHasherInterface $userPasswordHasher;
    private readonly CategoryRepository $categoryRepository;
    private readonly SkillRepository $skillRepository;
    private readonly UserRepository $userRepository;
    private readonly SessionRepository $sessionRepository;
    private const DEFAULT_PASSWORD = '123456';
    private const DEFAULT_NUMBER_OF_GENERATED_DATA = 30;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, CategoryRepository $categoryRepository, SkillRepository $skillRepository, UserRepository $userRepository, SessionRepository $sessionRepository)
    {
        $this->faker = Factory::create('fr_FR');
        $this->userPasswordHasher = $userPasswordHasher;
        $this->categoryRepository = $categoryRepository;
        $this->skillRepository = $skillRepository;
        $this->userRepository = $userRepository;
        $this->sessionRepository = $sessionRepository;
    }

    public function load(ObjectManager $manager): void
    {
        // Generate {{ DEFAULT_NUMBER_OF_GENERATED_DATA }} Categories
        $this->generateCategories($manager);

        // Generate {{ DEFAULT_NUMBER_OF_GENERATED_DATA }} Skills
        $this->generateSkills($manager);

        // Generate 1 Admin User & {{ DEFAULT_NUMBER_OF_GENERATED_DATA }} classic User
        $this->generateUsers($manager);

        // Generate {{ DEFAULT_NUMBER_OF_GENERATED_DATA }} Sessions
        $this->generateSessions($manager);

        // Generate {{ DEFAULT_NUMBER_OF_GENERATED_DATA }} Recommandations
        $this->generateRecommandations($manager);

        // Generate {{ DEFAULT_NUMBER_OF_GENERATED_DATA }} Notations
        $this->generateNotations($manager);

        // Generate {{ DEFAULT_NUMBER_OF_GENERATED_DATA }} Messages
        $this->generateMessages($manager);
    }

    private function generateUsers(ObjectManager $manager): void
    {
        $skills = $this->skillRepository->findAll();

        // Create Admin User
        $manager->persist($this->createUserAdmin($skills));

        // Create classic User
        for ($i = 0; $i < self::DEFAULT_NUMBER_OF_GENERATED_DATA; $i++) {
            $manager->persist($this->createUser($skills));
        }

        $manager->flush();
    }

    private function createUserAdmin($skills): User
    {
        $user = new User();

        $user->setEmail('admin@shareskillhub.com');
        $user->setRoles(['ROLE_ADMIN']);
        $this->generateEmptyPartOfUser($user, $skills);

        return $user;
    }

    private function createUser($skills): User
    {
        $user = new User();

        $user->setEmail($this->faker->unique()->email());
        $user->setRoles(['ROLE_USER']);
        $this->generateEmptyPartOfUser($user, $skills);

        return $user;
    }

    private function generateEmptyPartOfUser(User $user, $skills): void
    {
        $user->setPassword($this->userPasswordHasher->hashPassword($user, self::DEFAULT_PASSWORD));
        $user->setLastName($this->faker->lastName());
        $user->setFirstName($this->faker->firstName());
        $user->setAddress($this->faker->address());
        $user->setCity($this->faker->city());
        $user->setPostalCode($this->faker->postcode());
        $user->setCountry($this->faker->countryCode());
        $user->setAvatar("https://i.pravatar.cc/?u=" . rand(1,999));
        $user->setJob($this->faker->jobTitle());
        $user->setAbout($this->faker->realTextBetween(50, 100));

        foreach (array_rand($skills, rand(2, 6)) as $key) {
            $user->addSkill($skills[$key]);
        }
    }

    private function generateCategories(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::DEFAULT_NUMBER_OF_GENERATED_DATA; $i++) {
            $category = new Category();

            $category->setName($this->faker->unique()->word());
            $category->setColor($this->faker->unique()->hexColor());

            $manager->persist($category);
        }

        $manager->flush();
    }

    private function generateSkills(ObjectManager $manager): void
    {
        $categories = $this->categoryRepository->findAll();

        for ($i = 0; $i < self::DEFAULT_NUMBER_OF_GENERATED_DATA; $i++) {
            $skill = new Skill();

            $skill->setName($this->faker->unique()->word());

            $skill->setCategory(rand(1,4) > 1 ? $categories[array_rand($categories)] : null);

            $manager->persist($skill);
        }

        $manager->flush();
    }

    private function generateSessions(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();
        $types = ['online', 'onSite'];

        for ($i = 0; $i < self::DEFAULT_NUMBER_OF_GENERATED_DATA; $i++) {
            $session = new Session();

            $session->setSubject(implode(" ", $this->faker->words()));
            $session->setDescription($this->faker->realText());
            $session->setType($types[array_rand($types)]);
            $session->setStartTime(DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('+0 days', '+2 weeks')));
            $session->setEndTime(DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween($session->getStartTime()->format('Y-m-d'), $session->getStartTime()->modify('+1 week')->format('Y-m-d'))));

            $usersTemp = $users;
            $ownerUser = array_rand($usersTemp);
            $session->setOwnerUser($usersTemp[$ownerUser]);
            unset($usersTemp[$ownerUser]);

            foreach (array_rand($usersTemp, rand(2, 8)) as $key) {
                $session->addParticipantUser($usersTemp[$key]);
            }

            $manager->persist($session);
        }

        $manager->flush();
    }

    private function generateRecommandations(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();

        for ($i = 0; $i < self::DEFAULT_NUMBER_OF_GENERATED_DATA; $i++) {
            $recommandation = new Recommandation();

            $recommandation->setContent($this->faker->realText());
            $recommandation->setTimestamp(DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('+0 days', '+3 days')));

            $recommandation->setSenderUser($users[array_rand($users)]);
            $recommandation->setReceiverUser($users[array_rand($users)]);

            $usersTemp = $users;
            $senderUser = array_rand($usersTemp);
            $recommandation->setSenderUser($usersTemp[$senderUser]);
            unset($usersTemp[$senderUser]);
            $recommandation->setReceiverUser($usersTemp[array_rand($usersTemp)]);

            $manager->persist($recommandation);
        }

        $manager->flush();
    }

    private function generateNotations(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();
        $sessions = $this->sessionRepository->findAll();

        for ($i = 0; $i < self::DEFAULT_NUMBER_OF_GENERATED_DATA; $i++) {
            $notation = new Notation();

            $notation->setNote($this->faker->numberBetween(0,5));
            $notation->setComment($this->faker->realText());
            $notation->setOwnerUser($users[array_rand($users)]);
            $notation->setSession($sessions[array_rand($sessions)]);
            $notation->setTimestamp(DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('+0 days', '+3 days')));

            $manager->persist($notation);
        }

        $manager->flush();
    }

    private function generateMessages(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();

        for ($i = 0; $i < self::DEFAULT_NUMBER_OF_GENERATED_DATA; $i++) {
            $message = new Message();

            $message->setContent($this->faker->realText());
            $message->setTimestamp(DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('+0 days', '+3 days')));

            $usersTemp = $users;
            $senderUser = array_rand($usersTemp);
            $message->setSenderUser($usersTemp[$senderUser]);
            unset($usersTemp[$senderUser]);
            $message->setReceiverUser($usersTemp[array_rand($usersTemp)]);

            $manager->persist($message);
        }

        $manager->flush();
    }
}
