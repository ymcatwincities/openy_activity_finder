<?php

namespace Drupal\openy_activity_finder\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\openy_activity_finder\OpenyActivityFinderSolrBackend;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'Activity Finder 4' block.
 *
 * @Block(
 *   id = "activity_finder_4",
 *   admin_label = @Translation("Activity Finder 4"),
 *   category = @Translation("Paragraph Blocks")
 * )
 */
class ActivityFinder4Block extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Config Factory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
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
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
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

  /**
   * {@inheritdoc}
   */
  public function build() {
    $activity_finder_settings = $this->configFactory->get('openy_activity_finder.settings');
    $backend_service_id = $activity_finder_settings->get('backend');
    $backend = \Drupal::service($backend_service_id);
    $conf = $this->getConfiguration();
    // Convert associative array to usual one to have an array after
    // json_encode() to guarantee the options rendering order.
    $sort_options = [];
    foreach ($backend->getSortOptions() as $key => $option) {
      $sort_options[] = [
        'label' => $option,
        'value' => $key,
      ];
    }

    return [
      '#theme' => 'openy_activity_finder_4_block',
      '#ages' => $backend->getAges(),
      '#days' => $backend->getDaysOfWeek(),
      '#times' => $backend->getPartsOfDay(),
      '#days_times' => $backend->getDaysTimes(),
      '#categories' => $backend->getCategories(),
      '#categories_type' => $backend->getCategoriesType(),
      '#activities' => $backend->getCategories(),
      '#locations' => $backend->getLocations(),
      '#is_search_box_disabled' => $activity_finder_settings->get('disable_search_box'),
      '#is_spots_available_disabled' => $activity_finder_settings->get('disable_spots_available'),
      '#expanderSectionsConfig' => $activity_finder_settings->getRawData(),
      '#sort_options' => $sort_options,
      '#legacy_mode' => $conf['legacy_mode'],
      '#attached' => [
        'drupalSettings' => [
          'activityFinder' => [
            'is_search_box_disabled' => $activity_finder_settings->get('disable_search_box'),
            'is_spots_available_disabled' => $activity_finder_settings->get('disable_spots_available'),
          ],
        ],
        'library' => 'openy_activity_finder/activity_finder_4',
      ],
      '#cache' => [
        'tags' => $this->getCacheTags(),
        'contexts' => $this->getCacheContexts(),
        'max-age' => $this->getCacheMaxAge(),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), [OpenyActivityFinderSolrBackend::ACTIVITY_FINDER_CACHE_TAG]);
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $conf = $this->getConfiguration();
    $form['legacy_mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Legacy mode'),
      '#description' => $this->t('Enable legacy mode for activity finder'),
      '#default_value' => $conf['legacy_mode'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['legacy_mode'] = $form_state->getValue('legacy_mode');
  }

}
