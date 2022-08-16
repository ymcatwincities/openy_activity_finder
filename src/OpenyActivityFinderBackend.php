<?php

namespace Drupal\openy_activity_finder;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

abstract class OpenyActivityFinderBackend implements OpenyActivityFinderBackendInterface {

  use StringTranslationTrait;

  /**
   * Activity Finder configuration.
   *
   * @var Config
   */
  protected $config;

  /**
   * Site's default timezone.
   *
   * @var \DateTimeZone
   */
  protected $timezone;

  /**
   * {@inheritdoc}
   */
  abstract public function runProgramSearch($parameters, $log_id);

  /**
   * {@inheritdoc}
   */
  abstract public function getLocations();

  /**
   * OpenyActivityFinderBackend constructor.
   *
   * @param ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->get('openy_activity_finder.settings');
    $this->timezone = new \DateTimeZone($config_factory->get('system.date')->get('timezone')['default']);
  }

  /**
   * {@inheritdoc}
   */
  public function getAges() {
    $ages = [];

    $ages_config = $this->config->get('ages');

    if (!$ages_config) {
      return [];
    }

    foreach (explode("\n", $ages_config) as $row) {
      $row = trim($row);
      list($months, $label) = explode(',', $row);
      $ages[] = [
        'label' => $label,
        'value' => $months,
      ];
    }

    return $ages;
  }

  /**
   * Get weeks from configuration.
   */
  public function getWeeks() {
    $weeks = [];

    $weeks_config = $this->config->get('weeks');

    if (!$weeks_config) {
      return [];
    }

    foreach (explode("\n", $weeks_config) as $row) {
      $row = trim($row);
      list($months, $label) = explode(',', $row);
      $weeks[] = [
        'label' => $label,
        'value' => $months,
      ];
    }

    return $weeks;
  }

  /**
   * {@inheritdoc}
   */
  public function getDaysOfWeek() {
    return [
      [
        'label' => 'Mon',
        'search_value' => 'monday',
        'value' => '1',
      ],
      [
        'label' => 'Tue',
        'search_value' => 'tuesday',
        'value' => '2',
      ],
      [
        'label' => 'Wed',
        'search_value' => 'wednesday',
        'value' => '3',
      ],
      [
        'label' => 'Thu',
        'search_value' => 'thursday',
        'value' => '4',
      ],
      [
        'label' => 'Fri',
        'search_value' => 'friday',
        'value' => '5',
      ],
      [
        'label' => 'Sat',
        'search_value' => 'saturday',
        'value' => '6',
      ],
      [
        'label' => 'Sun',
        'search_value' => 'sunday',
        'value' => '7',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getPartsOfDay() {
    return [
      [
        'label' => $this->t('Morning'),
        'description' => $this->t('Open - 12 p.m.'),
        'value' => '1',
      ],
      [
        'label' => $this->t('Afternoon'),
        'description' => $this->t('12 - 5 p.m.'),
        'value' => '2',
      ],
      [
        'label' => $this->t('Evening'),
        'description' => $this->t('5 p.m. - Close'),
        'value' => '3',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDaysTimes() {
    $weekdays = $this->getDaysOfWeek();
    $parts_of_day = $this->getPartsOfDay();
    array_unshift($parts_of_day, [
      'label' => $this->t('Anytime'),
      'description' => '',
      'value' => '0',
    ]);

    $values = [];
    foreach ($weekdays as $day) {
      $value = $day;
      $value['value'] = [];
      foreach ($parts_of_day as $time) {
        $time['value'] = $day['value'] . $time['value'];
        $value['value'][] = $time;
      }

      $values[] = $value;
    }

    return $values;
  }

  /**
   * Get categories type.
   */
  public function getCategoriesType() {
    return 'multiple';
  }

  /**
   * Get the "Group collapse settings" from the AF settings page.
   */
  public function getFiltersSectionConfig() {
    $config = [];
    foreach (['schedule', 'category', 'locations'] as $name) {
      $config[$name] = TRUE;
      $value = $this->config->get("{$name}_collapse_group");
      if ($value) {
        $config[$name] = strstr($value, 'collapsed') || strstr($value, 'disabled');
      }
    }
    return $config;
  }
}
