<?php

namespace Drupal\openy_activity_finder\Plugin\search_api\processor;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Adds the parts of day to the indexed data.
 *
 * @SearchApiProcessor(
 *   id = "openy_af_parts_of_day",
 *   label = @Translation("Parts of day"),
 *   description = @Translation("Translates datetime values of session to an index of day's part"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class PartsOfDay extends ProcessorPluginBase {

  const PROPERTY_NAME = 'search_api_af_parts_of_day';

  const MORNING = 1;

  const AFTERNOON = 2;

  const EVENING = 3;

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Parts of day'),
        'description' => $this->t("Translates datetime values of session to an index of day's part"),
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

    $timezone = new \DateTimeZone(\Drupal::config('system.date')->get('timezone')['default']);
    $time12pm = strtotime('12:00:00Z');
    $time5pm = strtotime('17:00:00Z');

    $values = [];
    foreach ($paragraphs as $paragraph) {
      /** @var \Drupal\Core\Field\FieldItemListInterface $range */
      $range = $paragraph->field_session_time_date;
      if ($range->isEmpty()) {
        continue;
      }

      /** @var \Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem $_period */
      $_period = $range->get(0);
      if ($_period->isEmpty()) {
        continue;
      }

      $_from = DrupalDateTime::createFromTimestamp(strtotime($_period->get('value')->getValue() . 'Z'), $timezone);
      $_to = DrupalDateTime::createFromTimestamp(strtotime($_period->get('end_value')->getValue() . 'Z'), $timezone);
      $_from_time = strtotime($_from->format('H:i:s') . 'Z');
      $_to_time = strtotime($_to->format('H:i:s') . 'Z');
      if ($_from_time < $time12pm) {
        $values[] = self::MORNING;
      }
      if ($_from_time <= $time5pm && $_to_time >= $time12pm) {
        $values[] = self::AFTERNOON;
      }
      if ($_to_time > $time5pm) {
        $values[] = self::EVENING;
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
