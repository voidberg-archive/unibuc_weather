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
      $config = \Drupal::config('unibuc_weather.settings');

      // http://api.openweathermap.org/data/2.5/weather?q=Bucharest&appid=a957b3b17978c0a401605e354a724d7e
      // http://api.openweathermap.org/data/2.5/weather?id=683506&appid=a957b3b17978c0a401605e354a724d7e
      // http://api.openweathermap.org/data/2.5/forecast?id=683506&appid=a957b3b17978c0a401605e354a724d7e

      $api_key = $config->get('api_key');
      $temperature = $config->get('temperature');
      $city = $config->get('city');

      if (!empty($api_key)) {
        $url = 'http://api.openweathermap.org/data/2.5/weather?q=' . $city . '&APPID=' . $api_key;

        $data = (string) \Drupal::httpClient()->get($url)->getBody();

        $data = json_decode($data);
        $temp = $data->main->temp;
        $degree = $temperature;

        if ($temperature == 'c') {
          $temp = $temp - 273;
        } else {
          $temp = $temp * 9 / 5 - 460;
        }

        return array(
          '#markup' => $this->t('The temperature in @city is @temp @degree', array('@city' => $city, '@temp' => $temp, '@degree' => strtoupper($degree)))
        );
      } else {
        return array(
          '#markup' => $this->t("Please set your API Key from <a target='_blank' href='http://openweathermap.org/'>openweathermap.org</a>")
        );
      }
    }
}
