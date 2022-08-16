<?php

namespace Drupal\openy_activity_finder\Plugin\search_api\processor;

use Drupal\openy_activity_finder\OpenyActivityFinderBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds the Ages Min Max to the indexed data.
 *
 * @SearchApiProcessor(
 *   id = "openy_af_ages_min_max",
 *   label = @Translation("Ages Min Max"),
 *   description = @Translation("Creates range of integer values from 2 provided min max values."),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class AgesMinMax extends ProcessorPluginBase {

  /**
   * The config factory.
   *
   * @var ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var static $processor */
    $plugin = parent::create($container, $configuration, $plugin_id, $plugin_definition);

    $plugin->setConfigFactory($container->get('config.factory'));

    return $plugin;
  }

  /**
   * Retrieves the config factory service.
   *
   * @return ConfigFactoryInterface
   *   The config factory.
   */
  protected function getConfigFactory() {
    return $this->configFactory ?: \Drupal::configFactory();
  }

  /**
   * Sets the config factory service.
   *
   * @param ConfigFactoryInterface $config_factory
   *   The config factory.
   *
   * @return $this
   */
  protected function setConfigFactory(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
    return $this;
  }

  /**
   * Retrieves the AF backend service.
   *
   * @return OpenyActivityFinderBackendInterface
   *   The AF backend service.
   */
  protected function getBackendService() {
    $config = $this->getConfigFactory()->get('openy_activity_finder.settings');
    $backend_service_id = $config->get('backend');
    return \Drupal::service($backend_service_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Ages Min Max'),
        'description' => $this->t('Creates range of integer values from 2 provided min max values.'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
        'is_list' => TRUE,
      ];
      $properties['search_api_af_ages_min_max'] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $object = $item->getOriginalObject();
    $entity = $object->getValue();
    if ($entity->hasField('field_session_min_age') && $entity->hasField('field_session_max_age')) {
      $min_age = $entity->field_session_min_age->value;
      $max_age = $entity->field_session_max_age->value;
      if (empty($min_age)) {
        // Set min age as 0 years if min age was not set in the session.
        $min_age = 0;
      }
      if (empty($max_age)) {
        // Set max age as 100 years if max age was not set in the session.
        $max_age = 100 * 12;
      }
      if ($max_age - $min_age < 6) {
        $range = [$min_age];
      }
      else {
        $range = $this->getAgesRange($min_age, $max_age);
      }
      $fields = $this->getFieldsHelper()
        ->filterForPropertyPath($item->getFields(), NULL, 'search_api_af_ages_min_max');
      foreach ($fields as $field) {
        foreach ($range as $value) {
          $field->addValue($value);
        }
      }
    }
  }

  /**
   * Determines range of ages to index.
   *
   * @param int $min_age
   *   The min age of the range.
   * @param int $max_age
   *   The max age of the range.
   *
   * @return array
   *   The array of values to index.
   */
  private function getAgesRange(int $min_age, int $max_age) {
    $backend = $this->getBackendService();
    $ages = $backend->getAges();
    // Get only count of months.
    $ages = array_column($ages, 'value');

    return array_values(array_filter($ages, function($value) use ($min_age, $max_age) {
      return $value >= $min_age && $value <= $max_age;
    }));
  }

}
