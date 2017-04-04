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
      $cache = \Drupal::cache();

      // http://api.openweathermap.org/data/2.5/weather?q=Bucharest&appid=a957b3b17978c0a401605e354a724d7e
      // http://api.openweathermap.org/data/2.5/weather?id=683506&appid=a957b3b17978c0a401605e354a724d7e
      // http://api.openweathermap.org/data/2.5/forecast?id=683506&appid=a957b3b17978c0a401605e354a724d7e

      $api_key = $config->get('api_key');
      $temperature = $config->get('temperature');
      $city = $config->get('city');

      if (!empty($api_key)) {
        $cid = 'unibuc_weather:' . $city;

        $cachedData = $cache->get($cid);

        if ($cachedData) {
          $temp = $cachedData->data;
        } else {
          $url = 'http://api.openweathermap.org/data/2.5/weather?q=' . $city . '&APPID=' . $api_key;

          $data = (string) \Drupal::httpClient()->get($url)->getBody();

          $data = json_decode($data);
          $temp = $data->main->temp;

          $cache->set($cid, $temp, time() + 3600);
        }

        if ($temperature == 'c') {
          $temp = $temp - 273;
        } else {
          $temp = $temp * 9 / 5 - 460;
        }

        return array(
          '#theme' => 'unibuc_weather_display',
          '#city' => $city,
          '#temp' => $temp,
          '#degree' => $temperature,
        );
      } else {
        return array(
          '#markup' => $this->t("Please set your API Key from <a target='_blank' href='http://openweathermap.org/'>openweathermap.org</a>")
        );
      }
    }
}
