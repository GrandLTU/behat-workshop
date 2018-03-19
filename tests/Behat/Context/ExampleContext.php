<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ExampleContext.
 */
class ExampleContext extends MinkContext implements KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * Sets HttpKernel instance.
     *
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^I am logged in as "([^"]*)"$/
     *
     * @param string $username
     */
    public function iAmLoggedInAs(string $username): void
    {
        $this->getSession()->setBasicAuth($username);
    }
}
