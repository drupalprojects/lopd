<?php
/**
 * @file
 * Contains Drupal\lopd\Tests\LopdCronTest.
 */

namespace Drupal\lopd\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\lopd\Tests\Stub\LopdTestTrait;
use Drupal\Component\Render\FormattableMarkup;

/**
 * Class LopdCronTest
 *
 * @group lopd
 */
class LopdCronTest extends WebTestBase {
  use LopdTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['lopd'];

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $normal_user;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $privileged_user;

  public static function getInfo() {
    return array(
      'name' => 'Spanish LOPD cron test',
      'description' => 'Verify than the CRON process delete the LOPD entries',
    );
  }

  public function setUp() {
    // Enable LOPD module.
    parent::setUp(array('lopd'));
    // Create users.
    $this->privileged_user = $this->drupalCreateUser([
      'config LOPD module',
      'access LOPD data',
    ]);
    $this->normal_user = $this->drupalCreateUser();
  }


  public function testDeleteLopdEntriesWithCron() {
    // Check access to admin/config/system/lopd
    $this->drupalLogin($this->privileged_user);
    $this->drupalGet('admin/config/system/lopd');

    // Check exists lopd_messages_to_keep field and is set to 0 as default value.
    $this->assertFieldByName('messages_to_keep', 0, 'The lopd_messages_to_keep
      field is correctly set to 0 as default');

    // Check that lopd_messages_to_keep variable is set correctly:
    $allowed_values = array(2, 3, 4, 5);
    foreach ($allowed_values as $value) {
      $edit = array(
        'messages_to_keep' => $value,
      );
      $this->drupalPostForm('admin/config/system/lopd', $edit, t('Save configuration'));

      $lopd_settings = $this->config('lopd.settings')->get('messages_to_keep');
      $this->assertEqual($lopd_settings, $value, 'The messages_to_keep settings is saved correctly');

      // Check the Cron process delete entries:
      $max_timestamp = strtotime("- $value years");
      $this->createRandomEntries(15);
      $this->cronRun();
      $entries = $this->getLOPDEntries()->fetchAll();
      foreach ($entries as $entry) {
        if ($entry->timestamp <= $max_timestamp) {
          $this->fail(new FormattableMarkup("Cron proccess didn't remove a entry less than @maxtime for @value value",
            array('@maxtime' => $max_timestamp, '@value' => $value)), 'Lopd');
        }
      }
    }
  }
}