Feature: Gather Contexts

  Background:
    Given a file named "features/bootstrap/SomeContext.php" with:
      """
      <?php
      use Behat\Behat\Context\Context;
      class SomeContext implements Context
      {
          private AnotherContext $anotherContext;

          /** @When this scenario executes */
          public function thisScenarioExecutes() {}

          /** @Then the other context is initialized */
          public function anotherContextIsInitialized()
          {
              PHPUnit\Framework\Assert::assertTrue(isset($this->anotherContext));
          }
      }
      """
    And a file named "features/bootstrap/AnotherContext.php" with:
      """
      <?php
      use Behat\Behat\Context\Context;
      class AnotherContext implements Context
      {
      }
      """
    And a file named "features/test.feature" with:
      """
      Feature:
        Scenario:
          When this scenario executes
          Then the other context is initialized
      """

  Scenario: Context is uninitialized without the extension
    Given a file named "behat.yml" with:
      """
      default:
        suites:
          default:
            contexts:
              - SomeContext
              - AnotherContext
        extensions: ~
      """
    When I run behat
    Then it should fail

  Scenario: Context is initialized with the extension
    Given a file named "behat.yml" with:
      """
      default:
        suites:
          default:
            contexts:
              - SomeContext
              - AnotherContext
        extensions:
          Zayon\BehatGatherContextExtension\ContextGathererExtension: ~
      """
    When I run behat
    Then it should pass
