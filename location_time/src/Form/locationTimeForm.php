<?php
  namespace Drupal\location_time\Form;
  use Drupal\Core\Form\ConfigFormBase;
  use Drupal\Core\Form\FormStateInterface;
  use Drupal\Core\Config\ConfigFactoryInterface;
  use Drupal\Core\Cache\CacheBackendInterface;
  use Symfony\Component\DependencyInjection\ContainerInterface;

  class locationTimeForm extends ConfigFormBase {

    /**
     * The cache render service.
     *
     * @var \Drupal\Core\Cache\CacheBackendInterface
     */
    protected $cacheRender;

    /**
     * Construct a locationTimeForm object.
     *
     * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
     *   The factory for configuration objects.
     *
     * @param \Drupal\Core\Cache\CacheBackendInterface $cache_render
     *   A cache backend interface instance.
     *
     */
    public function __construct(ConfigFactoryInterface $config_factory, 
      CacheBackendInterface $cache_render) {
      parent::__construct($config_factory);
      $this->cacheRender = $cache_render;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
      return new static(
        $container->get('config.factory'),
        $container->get('cache.config'),
      );
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
      return ['location_time.settings'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
      return 'location_time';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
      $config = $this->config('location_time.settings');
      $timezone = array(
        'America/Chicago' => $this->t('America/Chicago'), 
        'America/New_York' => $this->t('America/New_York'), 
        'Asia/Tokyo' => $this->t('Asia/Tokyo'), 
        'Asia/Dubai' => $this->t('Asia/Dubai'), 
        'Asia/Kolkata' => $this->t('Asia/Kolkata'), 
        'Europe/Amsterdam' => $this->t('Europe/Amsterdam'),
        'Europe/Oslo' => $this->t('Europe/Oslo'),
        'Europe/London' => $this->t('Europe/London')
      );

      $form['country'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Country'),
        '#required' => TRUE,
        '#default_value' => $config->get('country'),
      ];
      $form['city'] = [
        '#type' => 'textfield',
        '#title' => $this->t('City'),
        '#required' => TRUE,
        '#default_value' => $config->get('city'),
      ];
      $form['timezone'] = array (
        '#type' => 'select',
        '#title' => $this->t('Timezone'),
        '#options' => $timezone,
        '#default_value' => $config->get('timezone'),
        '#required' => TRUE,
      );

      return parent::buildForm($form, $form_state);
    }

    /**  
     * {@inheritdoc}
     */  
    public function submitForm(array &$form, FormStateInterface $form_state) {
      parent::submitForm($form, $form_state);

      $country = $form_state->getValue('country');
      $city = $form_state->getValue('city');
      $timezone = $form_state->getValue('timezone');

      // Set the posted values
      $this->configFactory->getEditable('location_time.settings')
        ->set('country', $country)
        ->set('city', $city)
        ->set('timezone', $timezone)
        ->save();
    }

  }
