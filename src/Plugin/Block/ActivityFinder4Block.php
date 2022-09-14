<?php

namespace Drupal\openy_activity_finder\Plugin\Block;

use Drupal\media\MediaInterface;
use Drupal\openy_activity_finder\OpenyActivityFinderBackendInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\openy_activity_finder\OpenyActivityFinderSolrBackend;
use Drupal\openy_system\EntityBrowserFormTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'Activity Finder' block.
 *
 * @Block(
 *   id = "activity_finder_4",
 *   admin_label = @Translation("Activity Finder"),
 *   category = @Translation("Paragraph Blocks")
 * )
 */
class ActivityFinder4Block extends BlockBase implements ContainerFactoryPluginInterface {

  use EntityBrowserFormTrait;

  /**
   * Config Factory definition.
   *
   * @var ConfigFactory
   */
  protected $configFactory;

  /**
   * The entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

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
   * @param EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              ConfigFactory $config_factory,
                              EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'label_display' => 'visible',
      'limit_by_category_daxko' => [],
      'limit_by_category' => [],
      'exclude_by_category' => [],
      'exclude_by_location' => [],
      'legacy_mode' => 0,
      'weeks_filter' => 0,
      'hide_home_branch_block' => 0,
      'background_image' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    list($activity_finder_settings, $backend_service_id, $backend) = $this->getBackend();
    $conf = $this->getConfiguration();

    $image_mobile = '';
    $image_desktop = '';
    /** @var MediaInterface $media */
    if (!empty($conf['background_image']) && $media = static::loadEntityBrowserEntity($conf['background_image'])) {
      $image = $media->field_media_image->entity;
      $storage = $this->entityTypeManager->getStorage('image_style');
      $image_mobile = $storage->load('prgf_banner')->buildUrl($image->getFileUri());
      $image_desktop = $storage->load('prgf_gallery')->buildUrl($image->getFileUri());
    }

    $limit_by_category = $conf['limit_by_category'];

    if ($backend_service_id == "openy_daxko2.openy_activity_finder_backend") {
      $limit_by_category = $conf['limit_by_category_daxko'] ? explode(', ', $conf['limit_by_category_daxko']) : [];
    }

    $activities = $backend->getCategories();

    // Remove empty programs and subprograms.
    $results = $backend->runProgramSearch([], 0);
    $facets = $results['facets']['field_activity_category'];
    $activeSubPrograms = [];
    foreach ($facets as $item) {
      if (isset($item['id']) && !empty($item['id'])) {
        $activeSubPrograms[] = $item['id'];
      }
    }
    foreach ($activities as $indexProgram => $program) {
      if (isset($program['value'])) {
        foreach ($program['value'] as $indexSubProgram => $subProgram) {
          if (!in_array($subProgram['value'], $activeSubPrograms) ||
            ($limit_by_category && !in_array($subProgram['value'], $limit_by_category))) {
            unset($activities[$indexProgram]['value'][$indexSubProgram]);
          }
        }
      }
    }
    foreach ($activities as $indexProgram => $program) {
      if (empty($program['value'])) {
        unset($activities[$indexProgram]);
      }
    }

    // Sort activity groups and activities in alphabetical order.
    usort($activities, function ($a, $b) {
      return $a['label'] <=> $b['label'];
    });
    foreach ($activities as &$activity) {
      usort($activity['value'], function ($a, $b) {
        return $a['label'] <=> $b['label'];
      });
    }

    $sort_options = $backend->getSortOptions();

    $locations = $backend->getLocations();
    \Drupal::moduleHandler()->alter('activity_finder_location_list', $locations);
    return [
      '#theme' => 'openy_activity_finder_4_block',
      '#backend_service' => $backend_service_id,
      '#label' => $conf['label'],
      '#label_display' => $conf['label_display'] == 'visible',
      '#ages' => $backend->getAges(),
      '#days' => $backend->getDaysOfWeek(),
      '#times' => $backend->getPartsOfDay(),
      '#days_times' => $backend->getDaysTimes(),
      '#weeks' => $backend->getWeeks(),
      '#categories' => $backend->getCategories(),
      '#categories_type' => $backend->getCategoriesType(),
      '#activities' => $activities,
      '#locations' => $locations,
      '#disable_search_box' => (bool) $activity_finder_settings->get('disable_search_box'),
      '#disable_spots_available' => (bool) $activity_finder_settings->get('disable_spots_available'),
      '#sort_options' => $sort_options,
      // TODO: make default sort option configurable.
      '#default_sort_option' => array_keys($sort_options)[0],
      '#relevance_sort_option' => 'search_api_relevance__DESC',
      '#filters_section_config' => $backend->getFiltersSectionConfig(),
      '#limit_by_category' => $limit_by_category,
      '#exclude_by_category' => $conf['exclude_by_category'],
      '#exclude_by_location' => $conf['exclude_by_location'],
      '#legacy_mode' => (bool) $conf['legacy_mode'],
      '#weeks_filter' => (bool) $conf['weeks_filter'],
      '#hide_home_branch_block' => (bool) $conf['hide_home_branch_block'],
      '#background_image' => [
        'mobile' => $image_mobile,
        'desktop' => $image_desktop,
      ],
      '#bs_version' => (int) $activity_finder_settings->get('bs_version'),
      '#attached' => [
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
    list($activity_finder_settings, $backend_service_id, $backend) = $this->getBackend();
    $conf = $this->getConfiguration();

    // Store Daxko limit fields separately since they're strings and not references.
    if ($backend_service_id == 'openy_daxko2.openy_activity_finder_backend') {
      $form['limit_by_category_daxko'] = [
        '#type' => 'textfield',
        '#description' => $this->t('Separate multiple values by a comma and a space, like "ABC123, DEF234".'),
        '#title' => $this->t('Limit by category (Daxko)'),
        '#default_value' => $conf['limit_by_category_daxko'],
      ];
    }
    else {
      $base_by_category = [
        '#type' => 'entity_autocomplete',
        '#description' => $this->t('Separate multiple values by comma.'),
        '#target_type' => 'node',
        '#tags' => TRUE,
        '#selection_settings' => [
          'target_bundles' => ['program_subcategory'],
        ],
        '#size' => 100,
        '#maxlength' => 2048,
      ];
      $base_by_location = [
        '#type' => 'entity_autocomplete',
        '#description' => $this->t('Separate multiple values by comma. Search for title from Branch, Camp, Facility types.'),
        '#target_type' => 'node',
        '#tags' => TRUE,
        '#selection_settings' => [
          'target_bundles' => ['branch', 'camp', 'facility'],
        ],
        '#size' => 100,
        '#maxlength' => 2048,
      ];

      $form['exclude_by_location'] = $base_by_location + [
          '#title' => $this->t('Exclude by location'),
          '#default_value' => $conf['exclude_by_location']
            ? $this->entityTypeManager->getStorage('node')->loadMultiple($conf['exclude_by_location'])
            : NULL,
        ];
      $form['limit_by_category'] = $base_by_category + [
          '#title' => $this->t('Limit by category'),
          '#default_value' => $conf['limit_by_category']
            ? $this->entityTypeManager->getStorage('node')->loadMultiple($conf['limit_by_category'])
            : NULL,
        ];

      $form['exclude_by_category'] = $base_by_category + [
          '#title' => $this->t('Exclude by category'),
          '#default_value' => $conf['exclude_by_category']
            ? $this->entityTypeManager->getStorage('node')->loadMultiple($conf['exclude_by_category'])
            : NULL,
        ];
    }

    $form['legacy_mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Legacy mode'),
      '#description' => $this->t('Enable legacy mode for Activity Finder.'),
      '#default_value' => $conf['legacy_mode'],
    ];

    $form['weeks_filter'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Weeks filter'),
      '#description' => $this->t('Replace date/time filter with weeks filter.'),
      '#default_value' => $conf['weeks_filter'],
    ];

    $form['hide_home_branch_block'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide Home Branch info block'),
      '#default_value' => $conf['hide_home_branch_block'],
    ];

    // Entity Browser element for background image.
    $form['background_image'] = $this->getEntityBrowserForm(
      'images_library',
      $conf['background_image'],
      1,
      'preview'
    );
    // Convert the wrapping container to a details element.
    $form['background_image']['#type'] = 'details';
    $form['background_image']['#title'] = $this->t('Background image');
    $form['background_image']['#open'] = TRUE;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['limit_by_category_daxko'] = $form_state->getValue('limit_by_category_daxko');
    $this->configuration['limit_by_category'] = $form_state->getValue('limit_by_category')
      ? array_column($form_state->getValue('limit_by_category'), 'target_id')
      : [];
    $this->configuration['exclude_by_category'] = $form_state->getValue('exclude_by_category')
      ? array_column($form_state->getValue('exclude_by_category'), 'target_id')
      : [];
    $this->configuration['exclude_by_location'] = $form_state->getValue('exclude_by_location')
      ? array_column($form_state->getValue('exclude_by_location'), 'target_id')
      : [];
    $this->configuration['legacy_mode'] = $form_state->getValue('legacy_mode');
    $this->configuration['weeks_filter'] = $form_state->getValue('weeks_filter');
    $this->configuration['hide_home_branch_block'] = $form_state->getValue('hide_home_branch_block');
    $this->configuration['background_image'] = $this->getEntityBrowserValue($form_state, 'background_image');
  }

  /**
   * @return array
   */
  public function getBackend(): array
  {
    $activity_finder_settings = $this->configFactory->get('openy_activity_finder.settings');
    $backend_service_id = $activity_finder_settings->get('backend');
    /** @var OpenyActivityFinderBackendInterface $backend */
    $backend = \Drupal::service($backend_service_id);
    return array($activity_finder_settings, $backend_service_id, $backend);
  }

}
