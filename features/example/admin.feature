Feature: Homepage
  In order to administrate application
  As a administrator
  I need to be able to see admin page

  Background:
    Given There is a locale

  Scenario: Open admin page not logged in
    When I go to "/admin/"
    Then I should be on "/admin/login"
    And the response status code should be 200

  Scenario: Open admin page logged in
    Given I am logged in as administrator
    When I go to "/admin/"
    Then I should be on "/admin/"
    And the response status code should be 200
    And I should see "Admin platform"

  # TODO: locale/user creation/update/delete
  # TODO: task flow
