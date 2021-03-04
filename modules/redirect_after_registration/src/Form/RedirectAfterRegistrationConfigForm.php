<?php

namespace Drupal\redirect_after_registration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RedirectAfterRegistrationConfigForm.
 */
class RedirectAfterRegistrationConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'redirect_after_registration.redirectafterregistrationconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'redirect_after_registration_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('redirect_after_registration.redirectafterregistrationconfig');
    $form['destination'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Destination'),
      '#description' => $this->t('Redirection destination to be redirected after registration.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('destination'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('redirect_after_registration.redirectafterregistrationconfig')
      ->set('destination', $form_state->getValue('destination'))
      ->save();
  }

}
