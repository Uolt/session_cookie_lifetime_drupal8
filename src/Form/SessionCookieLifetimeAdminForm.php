<?php

namespace Drupal\session_cookie_lifetime\Form;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\session_cookie_lifetime\SessionCookieLifetimeServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Session cookie lifetime admin settings form.
 */
class SessionCookieLifetimeAdminForm extends ConfigFormBase {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The session cookie lifetime service.
   *
   * @var \Drupal\session_cookie_lifetime\SessionCookieLifetimeServiceInterface
   */
  protected $sclService;

  /**
   * Constructs a MessageForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface                            $config_factory
   * @param \Drupal\Core\Datetime\DateFormatterInterface                          $date_formatter
   *   The date service.
   *
   * @param \Drupal\session_cookie_lifetime\SessionCookieLifetimeServiceInterface $scl_service
   *
   * @internal param \Drupal\Core\Config\ConfigFactoryInterface $config_factory Config factory interface.*   Config factory interface.
   */
  public function __construct(ConfigFactoryInterface $config_factory, DateFormatterInterface $date_formatter, SessionCookieLifetimeServiceInterface $scl_service) {
    parent::__construct($config_factory);
    $this->dateFormatter = $date_formatter;
    $this->sclService = $scl_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('date.formatter'),
      $container->get('session_cookie_lifetime.service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'session_cookie_lifetime_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['session_cookie_lifetime.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = array();
    $config = $this->config('session_cookie_lifetime.settings');

    $default_lifetime = $this->dateFormatter->formatInterval($this->sclService->getOriginalLifetime());

    $form['session_cookie_lifetime_type'] = array(
      '#type' => 'radios',
      '#title' => $this->t("Set when the user's session expires"),
      '#options' => array(
        'default' => $this->t("Default (%default_lifetime)", array('%default_lifetime' => $default_lifetime)),
        'browser_session' => $this->t('When the browser session is closed'),
        'time' => $this->t('After a specific period of time'),
      ),
      '#default_value' => $config->get('lifetime_type'),
    );

    dsm($config->get('lifetime_type'));
    dsm($config->get('lifetime_amount'));
    dsm($config->get('lifetime_multiplier'));


    $form['session_cookie_lifetime_container'] = array(
      '#type' => 'container',
      '#states' => array(
        'visible' => array(
          ':input[name="session_cookie_lifetime_type"]' => array('value' => 'time'),
        ),
      ),
    );

    $form['session_cookie_lifetime_container']['session_cookie_lifetime_amount'] = array(
      '#title' => $this->t('Expiry time'),
      '#title_display' => 'invisible',
      '#type' => 'textfield',
      '#size' => 15,
      '#default_value' => $config->get('lifetime_amount'),
//      '#element_validate' => array('element_validate_integer_positive'),
    );

    $form['session_cookie_lifetime_container']['session_cookie_lifetime_multiplier'] = array(
      '#type' => 'select',
      '#options' => array(
        60 => $this->t('minutes'),
        60 * 60 => $this->t('hours'),
        60 * 60 * 24 => $this->t('days'),
        60 * 60 * 24 * 7 => $this->t('weeks'),
        60 * 60 * 24 * 30 => $this->t('months'),
        60 * 60 * 24 * 365 => $this->t('years'),
      ),
      '#default_value' => $config->get('lifetime_multiplier'),
    );

    $form['session_cookie_lifetime_description'] = array(
      '#markup' => $this->t('Configure when the session cookies used by this site are set to expire. Upon expiring the user will be forced to re-authenticate with the site.'),
    );

    $form['#attached']['library'][] = 'session_cookie_lifetime/scl.styles';

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Positive integer validation.
    $value = $form_state->getValue('session_cookie_lifetime_amount');
    if ($value !== '' && (!is_numeric($value) || intval($value) != $value || $value <= 0)) {
      $form_state->setErrorByName('session_cookie_lifetime_amount', $this->t('%name must be a positive integer.', array('%name' => $this->t('Expiry time'))));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    dsm('__________________________________------');
    dsm($form_state->getValue('session_cookie_lifetime_type'));
    dsm($form_state->getValue('session_cookie_lifetime_amount'));
    dsm($form_state->getValue('session_cookie_lifetime_multiplier'));

    $config = $this->config('session_cookie_lifetime.settings');


    if ($form_state->hasValue('session_cookie_lifetime_type')) {
      $config->set('lifetime_type', $form_state->getValue('session_cookie_lifetime_type'));
    }
    if ($form_state->hasValue('session_cookie_lifetime_amount')) {
      $config->set('lifetime_amount', $form_state->getValue('session_cookie_lifetime_amount'));
    }
    if ($form_state->hasValue('session_cookie_lifetime_multiplier')) {
      $config->set('lifetime_multiplier', $form_state->getValue('session_cookie_lifetime_multiplier'));
    }
    $config->save();
  }
}