<?php
/**
 * @file
 * Contains Drupal\lopd\Tests\LopdTestCaseTest.
 */

namespace Drupal\lopd\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\lopd\LopdServiceInterface;
use Drupal\lopd\Tests\Stub\LopdTestTrait;

/**
 * Class LopdTestCaseTest
 *
 * @group lopd
 */
class LopdTestCaseTest extends WebTestBase {
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
      'name' => 'Spanish LOPD operations test',
      'description' => 'Verify than the module provided operations are registered.',
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

  /*
   * The test check the correct function of LOPD operations. The checked operations
   * are the following:
   *  - Login.
   *  - Logout.
   *  - Failed login.
   */
  public function testOperations() {
    // Check failed_login operation:
    $edit = array(
      'name' => $this->randomMachineName(),
      'pass' => $this->randomString(),
    );

    $this->drupalPostForm('user', $edit, 'Log in');
    $lopd_entry = $this->getLOPDEntries('', 1)->fetchObject();
    $this->checkOperation($lopd_entry, LopdServiceInterface::LOPD_OPERATION_LOGIN_FAILED);

    // Check login operation:
    $this->drupalLogin($this->normal_user);
    $lopd_entry = $this->getLOPDEntries('', 1)->fetchObject();
    $this->checkOperation($lopd_entry, LopdServiceInterface::LOPD_OPERATION_LOGIN);

    // Check logout operation:
    $this->drupalLogout($this->normal_user);
    $lopd_entry = $this->getLOPDEntries('', 1)->fetchObject();
    $this->checkOperation($lopd_entry, LopdServiceInterface::LOPD_OPERATION_LOGOUT);
  }

}
