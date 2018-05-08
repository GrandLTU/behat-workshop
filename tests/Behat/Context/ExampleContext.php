<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use Behat\Behat\Tester\Exception\PendingException;
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
     * @Then /^I should see "([^"]*)" in grid$/
     * @param string $text
     */
    public function iShouldSeeInGrid(string $text)
    {
        $this->assertElementContainsText('.ui.sortable.stackable.celled.table', $text);
    }

    /**
     * @Given /^I should not see "([^"]*)" in grid$/
     * @param string $text
     */
    public function iShouldNotSeeInGrid(string $text)
    {
        $this->assertElementNotContainsText('.ui.sortable.stackable.celled.table', $text);
    }

    /**
     * @Then /^I should see "([^"]*)" flash message$/
     * @param string $text
     */
    public function iShouldSeeFlashMessage(string $text)
    {
        $this->assertElementContainsText('.sylius-flash-message', $text);
    }

    /**
     * @Given /^I am on users page$/
     */
    public function iAmOnUsersPage()
    {
        $this->visit('/admin/users');
    }

    /**
     * @Then /^I edit "([^"]*)" from grid$/
     * @param string $text
     */
    public function iEditFromGrid(string $text)
    {
        $this->getSession()->getPage()->find(
            'xpath',
            "//tr[@class=\"item\"]/td[text()[contains(., \"{$text}\")]]/../"
            . 'td/descendant::a[text()[contains(., "Edit")]]'
        )->click();
    }

    /**
     * @Given /^I change user name to "([^"]*)"$/
     * @param string $name
     */
    public function iChangeUserNameTo(string $name)
    {
        $this->fillField('Username', $name);
        $this->pressButton('Save changes');
    }

    /**
     * @When /^I close task "([^"]*)"$/
     * @param string $task
     */
    public function iCloseTaskThenIShouldSeeError(string $task)
    {
        $this->visit('/admin/tasks/');
        $this->iShouldSeeInGrid($task);
        $this->iEditFromGrid($task);
        $this->pressButton('receive_comment');
    }

    /**
     * @Then /^I should see error$/
     */
    public function iShouldSeeError()
    {
        $this->assertElementOnPage('.sylius-validation-error');
    }
}
