<?php

namespace Drupal\session_cookie_lifetime;


/**
 * Interface SessionCookieLifetimeServiceInterface.
 * Provide interface with additional methods for validations of postal code field.
 */
interface SessionCookieLifetimeServiceInterface {
  /**
   * Set the session cookie lifetime.
   *
   * @param $cookie_lifetime
   */
  public function setLifetime($cookie_lifetime);

  /**
   * Get the session cookie lifetime.
   *
   * @return int|null
   *   NUll is server default, otherwise it's the expiry time in seconds.
   */
  public function getLifetime();

  /**
   * Sets the original session cookie lifetime.
   *
   * @see session_cookie_lifetime_init()
   *
   * @param int $lifetime
   *   Session cookie lifetime in seconds.
   *
   * @return null|int
   *   Session cookie expiry date in seconds.
   */
  public function setOriginalLifetime($lifetime = NULL);

  /**
   * Gets the original session cookie lifetime.
   *
   * @return null|int
   *   Session cookie expiry date in seconds.
   */
  public function getOriginalLifetime();
}