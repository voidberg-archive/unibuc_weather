<?php
namespace Drupal\unibuc_weather\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfigForm extends ConfigFormBase {
  protected function getEditableConfigNames() {
    return ['unibuc_weather.settings'];
  }

  public function getFormId() {
    return 'unibuc_weather_config';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('unibuc_weather.settings');

    $form['api_key'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Api Key'),
      '#default_value' => $config->get('api_key'),
    );

    $form['temperature'] = array(
      '#type' => 'select',
      '#title' => $this->t('Display in'),
      '#options' => array(
        'c' => 'C',
        'f' => 'F'
      ),
      '#default_value' => $config->get('temperature'),
    );

    $form['city'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#default_value' => $config->get('city'),
    );

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('unibuc_weather.settings');

    $config->set('api_key', $form_state->getValue('api_key'));
    $config->set('temperature', $form_state->getValue('temperature'));
    $config->set('city', $form_state->getValue('city'));

    $config->save();
  }
}