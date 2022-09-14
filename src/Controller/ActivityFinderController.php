<?php

namespace Drupal\openy_activity_finder\Controller;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Site\Settings;
use Drupal\openy_activity_finder\Entity\ProgramSearchLog;
use Drupal\openy_activity_finder\OpenyActivityFinderBackendInterface;
use Drupal\openy_activity_finder\Entity\ProgramSearchCheckLog;
use Drupal\openy_activity_finder\OpenyActivityFinderSolrBackend;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * {@inheritdoc}
 */
class ActivityFinderController extends ControllerBase {

  // Cache queries for 5 minutes.
  const CACHE_LIFETIME = 300;

  /**
   * @var OpenyActivityFinderBackendInterface
   */
  protected $backend;

  /**
   * @var CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * @var TimeInterface
   */
  protected $time;

  /**
   * @var ImmutableConfig
   */
  protected $config;

  /**
   * Creates a new ActivityFinderController.
   */
  public function __construct(
    OpenyActivityFinderBackendInterface $backend,
    CacheBackendInterface $cacheBackend,
    TimeInterface $time,
    ImmutableConfig $config
  ) {
    $this->backend = $backend;
    $this->cacheBackend = $cacheBackend;
    $this->time = $time;
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $config = $container->get('config.factory')->get('openy_activity_finder.settings');

    return new static(
      $container->get($config->get('backend')),
      $container->get('cache.default'),
      $container->get('datetime.time'),
      $config
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getData(Request $request) {
    $ip = $request->getClientIp();
    $user_agent = $request->headers->get('User-Agent', '');
    $hash_ip_agent = substr($user_agent, 0, 50) . '   ' . $ip;
    $record = [
      'hash_ip_agent' => $hash_ip_agent,
      'ages' => $request->get('ages'),
      'days' => $request->get('days'),
      'times' => $request->get('times'),
      'daystimes' => $request->get('daystimes'),
      'weeks' => $request->get('weeks'),
      'locations' => $request->get('locations'),
      'categories' => $request->get('categories'),
      'page' => $request->get('page'),
      'sort' => $request->get('sort'),
      'keywords' => $request->get('keywords'),
      'exclude' => $request->get('exclude'),
    ];
    $record['hash'] = md5(json_encode($record));

    $record_cache_key = $record;
    unset($record_cache_key['hash']);
    unset($record_cache_key['hash_ip_agent']);
    $cid = md5(json_encode($record_cache_key));

    if (!$this->config->get('disable_program_search_log')) {
      $log = ProgramSearchLog::create($record);
      $log->save();
      $log_id = $log->id();
    }
    else {
      $log_id = 0;
    }


    $parameters = $request->query->all();

    foreach ($parameters as &$value) {
      $value = urldecode($value);
    }

    $data = NULL;
    $debugMsg = 'Tried to get data from cache for get-data endpoint.';
    if ($cache = $this->cacheBackend->get($cid)) {
      $debugMsg .= " Result: hit, cid: $cid.";
      $data = $cache->data;
    }
    else {
      $data = $this->backend->runProgramSearch($parameters, $log_id);

      /* @var $expanderSectionsConfig \Drupal\Core\Config\Config */
      $expanderSectionsConfig = $this->config('openy_activity_finder.settings');
      $data['expanderSectionsConfig'] = $expanderSectionsConfig->getRawData();

      // Allow other modules to alter the search results.
      $this->moduleHandler()->alter('activity_finder_program_search_results', $data);

      // Cache for 5 minutes.
      $expire = $this->time->getRequestTime() + self::CACHE_LIFETIME;
      $debugMsg .= " Result: miss, cid: $cid.";
      $this->cacheBackend->set($cid, $data, $expire, [OpenyActivityFinderSolrBackend::ACTIVITY_FINDER_CACHE_TAG]);
      $debugMsg .= " Setting new cache, cid: $cid, expiration: $expire";
    }
    if (!$this->config->get('disable_cache_debug_log')) {
      \Drupal::logger('openy_activity_finder')->debug($debugMsg);
    }
    return new JsonResponse($data);
  }

  /**
   * Redirect to register.
   */
  public function redirectToRegister(Request $request, $log) {
    $details = $request->get('details');
    $url = $request->get('url');

    if (empty($url)) {
      throw new NotFoundHttpException();
    }

    // Validate redirect request against trusted host patterns.
    $host_patterns = Settings::get('activity_finder_trusted_redirect_host_patterns', []);
    $trusted = FALSE;
    if (empty($host_patterns)) {
      $trusted = TRUE;
    }
    else {
      $host = parse_url($url, PHP_URL_HOST);
      foreach ($host_patterns as $host_pattern) {
        if (preg_match('/' . $host_pattern . '/i', $host)) {
          $trusted = TRUE;
          break;
        }
      }
    }

    if (!$trusted) {
      throw new NotFoundHttpException();
    }

    if (!empty($details) && !empty($log)) {
      $details_log = ProgramSearchCheckLog::create([
        'details' => $details,
        'log_id' => $log,
        'type' => ProgramSearchCheckLog::TYPE_REGISTER,
      ]);
      $details_log->save();
    }

    return new TrustedRedirectResponse($url, 301);
  }

  /**
   * Callback to retrieve programs full information.
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function ajaxProgramsMoreInfo(Request $request) {
    $parameters = $request->query->all();
    $cid = md5(json_encode($parameters));
    $data = NULL;
    if ($cache = $this->cacheBackend->get($cid)) {
      $data = $cache->data;
    }
    else {
      $data = $this->backend->getProgramsMoreInfo($request);

      // Allow other modules to alter the search results.
      $this->moduleHandler()->alter('activity_finder_program_more_info', $data);

      // Cache for 5 minutes.
      $expire = $this->time->getRequestTime() + self::CACHE_LIFETIME;
      $this->cacheBackend->set($cid, $data, $expire, [OpenyActivityFinderSolrBackend::ACTIVITY_FINDER_CACHE_TAG]);
    }

    return new JsonResponse($data);
  }

}
