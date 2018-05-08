Feature:
  In order to administrate application
  As a administrator
  I need to be able to manager tasks

  Background:
    Given There is a locale
    And I am logged in as administrator

  Scenario: Closing task without comment
    Given Task "demo_task "with required comment and status "waiting_for_comment"
    When I close task "demo_task"
    Then I should see error
