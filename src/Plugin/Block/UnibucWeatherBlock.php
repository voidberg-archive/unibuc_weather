<?php
/**
 * @file
 * Contains \Drupal\unibuc\Plugin\Block\LinksBlock.
 */
namespace Drupal\unibuc_weather\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a custom block.
 *
 * @Block(
 *   id = "hello_weather_block",
 *   admin_label = @Translation("A block that shows weather info."),
 *   category = @Translation("Blocks")
 * )
 */
class UnibucWeatherBlock extends BlockBase implements BlockPluginInterface {

    /**
     * {@inheritdoc}
     */
    public function build() {
      $config = $this->getConfiguration();

      // http://api.openweathermap.org/data/2.5/weather?q=Bucharest&appid=a957b3b17978c0a401605e354a724d7e
      // http://api.openweathermap.org/data/2.5/weather?id=683506&appid=a957b3b17978c0a401605e354a724d7e
      // http://api.openweathermap.org/data/2.5/forecast?id=683506&appid=a957b3b17978c0a401605e354a724d7e
      if (!empty($config['hello_weather_api_key'])) {
        $url = 'http://api.openweathermap.org/data/2.5/forecast?id=683506&APPID=' . $config['hello_weather_api_key'];

        $data = (string) \Drupal::httpClient()->get($url)->getBody();

        $data = json_decode($data);
        $temp = $data->list[0]->main->temp;
        $degree = $config['hello_weather_api_display'];

        if ($config['hello_weather_api_display'] == 'c') {
          $temp = $temp - 273;
        } else {
          $temp = $temp * 9 / 5 - 460;
        }

        return array(
          '#markup' => $this->t('The temperature in @city is @temp @degree', array('@city' => 'Bucharest', '@temp' => $temp, '@degree' => strtoupper($degree)))
        );
      } else {
        return array(
          '#markup' => $this->t("Please set your API Key from <a target='_blank' href='http://openweathermap.org/'>openweathermap.org</a>")
        );
      }
    }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state){
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['hello_weather_api_key'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Api Key'),
      '#description' => $this->t('Apy Key for openweathermap.org'),
      '#default_value' => isset($config['hello_weather_api_key']) ? $config['hello_weather_api_key'] : ''
    );

    $form['hello_weather_api_display'] = array(
      '#type' => 'select',
      '#title' => $this->t('Display in'),
      '#options' => array(
        'c' => 'C',
        'f' => 'F'
      ),
      '#description' => $this->t('How to display the temperature.'),
      '#default_value' => isset($config['hello_weather_api_display']) ? $config['hello_weather_api_display'] : ''
    );

    return $form;
  }

  public function blockSubmit($form, FormStateInterface $form_state){
    $this->setConfigurationValue('hello_weather_api_key', $form_state->getValue('hello_weather_api_key'));
    $this->setConfigurationValue('hello_weather_api_display', $form_state->getValue('hello_weather_api_display'));
  }
}
