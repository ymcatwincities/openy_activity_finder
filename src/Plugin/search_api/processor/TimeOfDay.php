<?php

namespace Drupal\openy_activity_finder\Plugin\search_api\processor;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Adds the time of day to the indexed data.
 *
 * @SearchApiProcessor(
 *   id = "openy_af_time_of_day",
 *   label = @Translation("Time of day"),
 *   description = @Translation("Translates datetime values of session to an index of day's time"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class TimeOfDay extends ProcessorPluginBase {

  const PROPERTY_NAME = 'search_api_af_time_of_day';

  const BASE_DATE = '1970-01-01T';

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Time of day'),
        'description' => $this->t("Translates datetime values of session to an index of day's time"),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
        'is_list' => FALSE,
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

    $value = self::BASE_DATE . '00:00:00Z';
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
      $value = self::BASE_DATE . $_from->format('H:i:s') . 'Z';

      // We need just one value as we can sort only by single value fields.
      break;
    }
    $fields = $this->getFieldsHelper()
      ->filterForPropertyPath($item->getFields(), NULL, self::PROPERTY_NAME);
    foreach ($fields as $field) {
      $field->addValue($value);
    }
  }

}
