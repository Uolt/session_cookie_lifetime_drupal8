<?php

namespace Drupal\session_cookie_lifetime\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SessionCookieLifetimeSubscriber implements EventSubscriberInterface {

  private $service;

  public function __construct($scl_service) {
    $this->service = $scl_service;
  }

  public function setCookieLifetame(GetResponseEvent $event) {
    $service = $this->service;
    $original_cookie_params = session_get_cookie_params();
    $service->setOriginalLifetime($original_cookie_params['lifetime']);
    dsm($original_cookie_params['lifetime']);

    $cookie_lifetime = $service->getLifetime();
    if ($cookie_lifetime !== NULL) {
      $service->setLifetime($cookie_lifetime);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('setCookieLifetame');
    return $events;
  }

}