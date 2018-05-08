Feature:
  In order to administrate application
  As a administrator
  I need to be able to manager users

  Background:
    Given There is a locale
    And I am logged in as administrator

  Scenario: List users
    Given I am on users page
    Then I should see "administrator" in grid

  Scenario: Edit user
    Given There is an admin user "demo_user"
    And I am on users page
    When I edit "demo_user" from grid
    And I fill in the following:
      | Username   | edited_user |
      | First name | First name  |
    And I press "Save changes"
    Then I should see "Admin user has been successfully updated." flash message

