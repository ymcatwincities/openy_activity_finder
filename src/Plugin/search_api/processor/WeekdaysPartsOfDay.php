<?php

namespace Drupal\openy_activity_finder\Plugin\search_api\processor;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\options\Plugin\Field\FieldType\ListItemBase;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds the weekdays and parts of day to the indexed data.
 *
 * @SearchApiProcessor(
 *   id = "openy_af_weekdays_parts_of_day",
 *   label = @Translation("Weekdays and Parts of day"),
 *   description = @Translation("Translates days and datetime values of session
 *   to an index of weekdays and parts of day"), stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class WeekdaysPartsOfDay extends ProcessorPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Config Factory definition.
   *
   * @var ConfigFactory
   */
  protected $configFactory;

  /**
   * Constructs a Block object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param ConfigFactory $config_factory
   *   The Config Factory.
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              ConfigFactory $config_factory) {
    $this->configFactory = $config_factory;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  const PROPERTY_NAME = 'search_api_af_weekdays_parts_of_day';

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Weekdays and Parts of day'),
        'description' => $this->t("Translates days and datetime values of session to an index of weekdays and parts of day"),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
        'is_list' => TRUE,
      ];
      $properties[self::PROPERTY_NAME] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $object = $item->getOriginalObject();
    $entity = $object->getValue();
    if (!$entity->hasField('field_session_time')) {
      return;
    }

    $paragraphs = $entity->field_session_time ? $entity->field_session_time->referencedEntities() : [];
    if (empty($paragraphs)) {
      return;
    }

    $activity_finder_settings = $this->configFactory->get('openy_activity_finder.settings');
    $backend_service_id = $activity_finder_settings->get('backend');
    $backend = \Drupal::service($backend_service_id);
    $weekdays = $backend->getDaysOfWeek();

    $timezone = new \DateTimeZone(\Drupal::config('system.date')->get('timezone')['default']);
    $time12pm = strtotime('12:00:00Z');
    $time5pm = strtotime('17:00:00Z');


    $day_values = [];
    $time_values = [];
    $values = [];
    foreach ($paragraphs as $paragraph) {
      /** @var FieldItemListInterface $days */
      $days = $paragraph->field_session_time_days;
      if ($days->isEmpty()) {
        continue;
      }

      /** @var ListItemBase $day */
      $daylist = [];
      foreach ($days as $day) {
        if ($day) {
          $daylist[] = $day->getValue()['value'];
        }
      }

      // Convert weekday names into numerical values.
      foreach ($weekdays as $weekday) {
        if (in_array($weekday['search_value'], $daylist)) {
          $day_values[] = $weekday['value'];
        }
      }

      // Add values for any time in the found days.
      foreach ($day_values as $day_value) {
        $values[] = $day_value . 0;
      }

      /** @var FieldItemListInterface $range */
      $range = $paragraph->field_session_time_date;
      if ($range->isEmpty()) {
        continue;
      }

      /** @var DateRangeItem $_period */
      $_period = $range->get(0);
      if ($_period->isEmpty()) {
        continue;
      }

      $_from = DrupalDateTime::createFromTimestamp(strtotime($_period->get('value')->getValue() . 'Z'), $timezone);
      $_to = DrupalDateTime::createFromTimestamp(strtotime($_period->get('end_value')->getValue() . 'Z'), $timezone);
      $_from_time = strtotime($_from->format('H:i:s') . 'Z');
      $_to_time = strtotime($_to->format('H:i:s') . 'Z');
      if ($_from_time < $time12pm) {
        $time_values[] = 1;
      }
      if ($_from_time <= $time5pm && $_to_time >= $time12pm) {
        $time_values[] = 2;
      }
      if ($_to_time > $time5pm) {
        $time_values[] = 3;
      }

      foreach ($day_values as $day_value) {
        foreach ($time_values as $time_value) {
          $values[] = $day_value . $time_value;
        }
      }
    }

    $values = array_unique($values, SORT_NUMERIC);
    $fields = $this->getFieldsHelper()
      ->filterForPropertyPath($item->getFields(), NULL, self::PROPERTY_NAME);
    foreach ($fields as $field) {
      foreach ($values as $value) {
        $field->addValue($value);
      }
    }
  }

}
