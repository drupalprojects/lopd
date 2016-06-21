<?php
/**
 * @file
 * Contains Drupal\lopd\Tests\Stub\LopdTestTrait.php
 */

namespace Drupal\lopd\Tests\Stub;

use Drupal\Component\Render\FormattableMarkup;


trait LopdTestTrait {

  /**
   * Return de last entry adsded to LOPD table. If $operation is set, it will
   * filtered for this $operation.
   *
   * @param $operation
   *        Operation to filter.
   */
  private function getLOPDEntries($operation = '', $limit = 0) {
    $query = db_select('lopd', 'l')
      ->fields('l')
      ->orderBy('l.lopdid', 'DESC');
    if (!empty($operation)) {
      $query->codition('l.operation', $operation);
    }
    if (!empty($limit)) {
      $query->range(0, $limit);
    }

    return $query->execute();
  }

  /**
   * Check a $operation row in DB. This is used to check that a lopd entry is correct.
   * @param  Object $lopd_entry
   *         Obeject with data of a LOPD entry in DB.
   * @param  String $operation_type
   *         LOPD operation type.
   */
  private function checkOperation($lopd_entry, $operation_type) {
    // Check $operation_object is not null:
    $this->assertNotNull($lopd_entry,
      new FormattableMarkup('@operation: LOPD entry object returned from DB', array('@operation' => $operation_type)),
      'Lopd');

    // Check $operation_object timestamp field:
    $this->assertNotNull($lopd_entry->timestamp,
      new FormattableMarkup('@operation: timestamp collumn is set', array('@operation' => $operation_type)),
      'Lopd');

    // Check $operation_object uid field:
    $this->assertNotNull($lopd_entry->uid,
      new FormattableMarkup('@operation: uid collumn is set', array('@operation' => $operation_type)),
      'Lopd');

    // Check $operation_object authname field:
    $this->assertNotNull($lopd_entry->authname,
      new FormattableMarkup('@operation: authname collumn is set', array('@operation' => $operation_type)),
      'Lopd');

    //Check $operation_object ip field:
    $this->assertNotNull($lopd_entry->ip,
      new FormattableMarkup('@operation: Ip collumn is set', array('@operation' => $operation_type)),
      'Lopd');

    //Check $operation_object operation name field:
    $this->assertEqual($lopd_entry->operation, $operation_type,
      new FormattableMarkup('The @operation operation has been logged', array('@operation' => $operation_type)), 'Lopd');
  }

  /**
   * Create random LOPD entries into DB. This is used to simulate some test as
   * testDeleteLopdEntriesWithCron.
   * @param  $num_entries
   *         Number of entries set into DB.
   * @param  $max_timestamp
   *         Max timestamp set into BD for each LOPD entry.
   */
  private function createRandomEntries($num_entries) {
    for ($i = 0; $i < $num_entries; $i++) {
      \Drupal::database()->insert('lopd')
        ->fields(array(
          'uid' => 1,
          'authname' => 'user',
          'ip' => \Drupal::request()->getClientIp(),
          'operation' => 'operation',
          'timestamp' => mt_rand(0, time())))
        ->execute();
    }
  }
}