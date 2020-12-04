<?php

namespace Drupal\openy_activity_finder\Plugin\rest\resource;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\openy_activity_finder\OpenyActivityFinderBackendInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides Session Data resource.
 *
 * @RestResource(
 *   id = "openy_activity_finder_session_data",
 *   label = @Translation("Session Data"),
 *   uri_paths = {
 *     "canonical" = "/af/api/v1/session-data"
 *   }
 * )
 */
class SessionData extends ResourceBase {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Backend.
   *
   * @var \Drupal\openy_activity_finder\OpenyActivityFinderBackendInterface
   */
  protected $backend;

  /**
   * Constructs a Client Data resource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   Request Stack.
   * @param \Drupal\openy_activity_finder\OpenyActivityFinderBackendInterface $backend
   *   Backend.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    RequestStack $request_stack,
    OpenyActivityFinderBackendInterface $backend
  ) {
    parent::__construct($configuration,
      $plugin_id,
      $plugin_definition,
      $serializer_formats,
      $logger
    );
    $this->requestStack = $request_stack;
    $this->backend = $backend;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    $config = $container->get('config.factory')->get('openy_activity_finder.settings');
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('openy_activity_finder'),
      $container->get('request_stack'),
      $container->get($config->get('backend'))
    );
  }

  /**
   * Handles GET request.
   *
   * @return \Drupal\rest\ResourceResponse
   *   Response.
   *
   * @throws \Drupal\search_api\SearchApiException
   */
  public function get() {
    $disable_cache = new CacheableMetadata();
    $disable_cache->setCacheMaxAge(0)->addCacheContexts(['url.query_args', 'url.path']);
    $session_ids = $this->requestStack->getCurrentRequest()->query->get('session_ids');
    if (empty($session_ids)) {
      $response_data = [
        'error' => 'Required parameter session_ids is missing.',
      ];
      $response = new ResourceResponse($response_data, 400);
      $response->addCacheableDependency($disable_cache);
      return $response;
    }

    $sessions_array = explode(',', $session_ids);
    $response_data = [
      'sessions' => $this->backend->getSessions($sessions_array),
    ];

    $response = new ResourceResponse($response_data);
    $response->addCacheableDependency($disable_cache);
    return $response;
  }

}
