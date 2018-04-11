Feature: Homepage
  In order to interact with application
  As a user
  I need to be able to see homepage

  Scenario: Open homepage
    When I am on the homepage
    Then I should see "Welcome to Symfony"
