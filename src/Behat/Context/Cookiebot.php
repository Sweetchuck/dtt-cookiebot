<?php

declare(strict_types = 1);

namespace Sweetchuck\DrupalTestTraits\Cookiebot\Behat\Context;

use Behat\Behat\Hook\Scope\ScenarioScope;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Sweetchuck\DrupalTestTraits\Core\Behat\Context\Base;

class Cookiebot extends Base {

  protected string $acceptManuallyScenarioTag = 'cookiebot_accept_manually';

  protected bool $cookebotModuleWasEnabled = TRUE;

  /**
   * Uninstall Cookiebot.
   *
   * @BeforeSuite
   */
  public static function uninstallCookiebot(BeforeSuiteScope $scope) {
    \Drupal::getContainer()
      ->get('module_handler')
      ->moduleExists('cookiebot');

    //if ($this->hasToBeAcceptedManually($scope)) {
    //  return;
    //}

    \Drupal::getContainer()->get('module_installer')->uninstall(['cookiebot']);
  }

  /**
   * Install cookiebot module if it was installed.
   *
   * @AfterSuite
   */
  public static function installCookiebot(AfterSuiteScope $scope) {
    //if ($this->hasToBeAcceptedManually($scope)
    //  || !$this->cookebotModuleWasEnabled
    //) {
    //  return;
    //}

    $isEnabled = \Drupal::getContainer()
      ->get('module_handler')
      ->moduleExists('cookiebot');
    if ($isEnabled) {
      return;
    }

    \Drupal::getContainer()
      ->get('module_installer')
      ->install(['cookiebot']);

    $configNames = [
      'cookiebot.settings',
    ];
    foreach ($configNames as $configName) {
      $command = sprintf(
        'cd %s && cat %s | ./vendor/bin/drush --config="drush" --yes config:set --input-format="yaml" %s "?" - 2>&1',
        // @todo Get the project root.
        escapeshellarg('/home/andor/Documents/Composer/vendor/anrt-client/griffith-college-d9'),
        // @todo Get config dir.
        escapeshellarg("./config/sync/$configName.yml"),
        escapeshellarg($configName),
      );
      $output = [];
      $exitCode = 0;
      exec($command, $output, $exitCode);
    }
  }

  public function hasToBeAcceptedManually(ScenarioScope $scope): bool {
    return in_array(
      $this->acceptManuallyScenarioTag,
      $scope->getScenario()->getTags(),
    );
  }

}
