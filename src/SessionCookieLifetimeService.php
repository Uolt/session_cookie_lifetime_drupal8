<?php

namespace Drupal\session_cookie_lifetime;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Provide additional methods for validations of postal code field.
 */
class SessionCookieLifetimeService implements SessionCookieLifetimeServiceInterface {

  private $config;

  private $sessionManager;

  private $session;


  /**
   * SessionCookieLifetimeService constructor.
   *
   * @param $config
   * @param $session_manager
   *
   * @param $session
   *
   * @internal param \Drupal\Core\Config\ConfigFactory $config_factory
   */
  public function __construct($config, $session_manager, $session) {
    $this->config = $config->get('session_cookie_lifetime.settings');
    $this->sessionManager = $session_manager;
    $this->session = $session;
  }

  /**
   * {@inheritdoc}
   */
//  public static function create(ContainerInterface $container) {
//    return new static(
//      $container->get('session')
//    );
//  }

  /**
   * {@inheritdoc}
   */
  public function setLifetime($cookie_lifetime) {
    $session_manager = $this->sessionManager;
    if ($session_manager->isStarted()) {
      dsm('SET:');
      dsm($cookie_lifetime);
//      $session_manager->save();
////    session_write_close();
////    $session_manager->clear();
//      $session_manager->start();
//      $this->session->invalidate($cookie_lifetime);
      $session_manager->setOptions(array('cookie_lifetime' => $cookie_lifetime));
//      $session_manager->regenerate();
      dsm($this->session);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getLifetime() {
    $config = $this->config;
    $type = $config->get('lifetime_type');

    $lifetime = 0;

    switch ($type) {
      case 'default':
        $lifetime = NULL;
        break;

      case 'browser_session':
        $lifetime = 0;
        break;

      case 'time':
        $amount = $config->get('lifetime_amount');
        $multiplier = $config->get('lifetime_multiplier');

        $lifetime = $amount * $multiplier;
        break;
    }

    return $lifetime;
  }

  /**
   * {@inheritdoc}
   */
  public function setOriginalLifetime($lifetime = NULL) {
    static $original_lifetime = NULL;
    if ($lifetime !== NULL) {
      $original_lifetime = $lifetime;
    }

    return $original_lifetime;
  }

  /**
   * {@inheritdoc}
   */
  public function getOriginalLifetime() {
    return $this->setOriginalLifetime();
  }
}