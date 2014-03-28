<?php

/**
 * @file
 * Cointains Drupal\lopd\Form\LopdAdminForm.
 */

namespace Drupal\lopd\Form;

use Drupal\Core\Form\ConfigFormBase;

/**
 * Class LopdAdminForm
 * @package Drupal\lopd\Form
 * Provide a admin settings form for LOPD module.
 */
class LopdAdminForm extends ConfigFormBase {

  /**
   * @inheritdoc
   */
  public function getFormId() {
    return 'lopd_admin_form';
  }

  /**
   * @inheritdoc
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->config('lopd.settings');
    $form['messages_to_keep'] = array(
      '#type' => 'select',
      '#title' => t('Database log messages to keep'),
      '#description' => t('The maximum number of messages to keep in the database log.'),
      '#default_value' => $config->get('messages_to_keep'),
      '#options' => array(
        '0' => t('All'),
        '2' => t('@count Years old', array('@count' => 2)),
        '3' => t('@count Years old', array('@count' => 3)),
        '4' => t('@count Years old', array('@count' => 4)),
        '5' => t('@count Years old', array('@count' => 5)),
      ),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * @inheritdoc
   */
  public function submitForm(array &$form, array &$form_state) {
    $this->config('lopd.settings')
      ->set('messages_to_keep', $form_state['values']['messages_to_keep'])
      ->save();
    parent::buildForm($form, $form_state);
  }
} 