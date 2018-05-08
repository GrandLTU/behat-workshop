<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use AppBundle\Entity\Task;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use InvalidArgumentException;
use Platform\Bundle\AdminBundle\Model\AdminUser;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class FixtureContext.
 */
class FixtureContext extends RawMinkContext implements KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var array
     */
    private $references = [];

    /**
     * Sets HttpKernel instance.
     *
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     *
     * @throws ToolsException
     */
    public function initializeDatabase(): void
    {
        static $initialized = false;
        if ($initialized) {
            return;
        }

        $doctrine = $this->kernel->getContainer()->get('doctrine');
        /** @var Connection $connection */
        $connection = $doctrine->getConnection();
        $dbName = $connection->getParams()['path'];
        $schema = $connection->getSchemaManager();
        $schema->dropDatabase($dbName);
        $schema->createDatabase($dbName);

        /** @var EntityManager $manager */
        $manager = $doctrine->getManager();
        $metadata = $manager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($manager);
        $schemaTool->createSchema($metadata);
    }

    /**
     * @AfterScenario
     */
    public function afterScenario(): void
    {
        (new ORMPurger($this->getManager()))->purge();
        $this->references = [];
    }

    /**
     * @Given /^I am logged in as administrator$/
     *
     * @throws ORMException
     */
    public function iAmLoggedInAsAdministrator(): void
    {
        $this->thereIsAnAdminUser('administrator');
        $this->iLoginAsUser('administrator');
    }

    /**
     * @When /^I login in as "([^"]*)"$/
     * @Given /^I am logged in as "([^"]*)"$/
     *
     * @param string $name
     */
    public function iLoginAsUser(string $name): void
    {
        try {
            $this->getSession()->setBasicAuth($name);
        } catch (UnsupportedDriverActionException $e) {
            $this->visitPath('/');
            $this->getSession()->setCookie('test_auth', $name);
        }
    }

    /**
     * @Given /^There is an admin user "([^"]*)"$/
     * @param string $name
     *
     * @return AdminUser
     *
     * @throws ORMException
     */
    public function thereIsAnAdminUser(string $name): AdminUser
    {
        if (isset($this->references[AdminUser::class][$name])) {
            throw new InvalidArgumentException(sprintf('User %s already exists', $name));
        }

        $object = new AdminUser();
        $object->setUsername($name);
        $object->setEmail($name . '@example.com');
        $object->setPlainPassword($name);
        $object->setLocaleCode(key($this->references[Locale::class]));
        $object->setEnabled(true);

        $manager = $this->getManager();
        $manager->persist($object);
        $manager->flush();

        $this->references[AdminUser::class][$name] = $object;

        return $object;
    }

    /**
     * @Given /^There is a locale$/
     * @Given /^There is a locale "([^"]*)"$/
     *
     * @param string|null $locale
     *
     * @return Locale
     */
    public function thereIsALocale(string $locale = null): Locale
    {
        if (null === $locale) {
            $locale = $this->kernel->getContainer()->getParameter('locale');
        }

        if (isset($this->references[Locale::class][$locale])) {
            throw new InvalidArgumentException(sprintf('Locale %s already exists', $locale));
        }

        $object = new Locale();
        $object->setCode($locale);

        $manager = $this->getManager();
        $manager->persist($object);
        $manager->flush();

        $this->references[Locale::class][$locale] = $object;

        return $object;
    }

    /**
     * @return EntityManager
     */
    private function getManager(): EntityManager
    {
        return $this->kernel->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    /**
     * @Given /^Task "([^"]*)"with required comment and status "([^"]*)"$/
     */
    public function taskWithRequiredCommentAndStatus(string $title, string $status)
    {
        $task = new Task();
        $task->setStatus($status);
        $task->setCommentNeeded(true);
        $task->setTimeSpent(0);
        $task->setTitle($title);

        $manager = $this->getManager();
        $manager->persist($task);
        $manager->flush();
    }
}
