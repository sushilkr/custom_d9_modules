<?php
  /**
   * Provide a Block
   */
  namespace Drupal\location_time\Plugin\Block;

  use Drupal\Core\Block\BlockBase;
  use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
  use Drupal\Core\Cache\Cache;
  use Drupal\Core\Config\ConfigFactoryInterface;
  use Drupal\Core\Datetime\DateFormatterInterface;
  use Symfony\Component\DependencyInjection\ContainerInterface;

  /**
   * Provides a 'Location & Time' block.
   *
   * @Block(
   *   id = "location_time",
   *   admin_label = @Translation("Location & Time"),
   *   category = @Translation("General"),
   * )
   */
  class locationTimeBlock extends BlockBase implements ContainerFactoryPluginInterface {

    /**
     * The date formatter service.
     *
     * @var \Drupal\Core\Datetime\DateFormatterInterface
     */
    protected $dateFormatter;

    /**
     * The config factory service.
     *
     * @var \Drupal\Core\Config\ConfigFactoryInterface
     */
    protected $configFactory;

    /**
     * Constructs a new WorkspaceSwitcherBlock instance.
     *
     * @param array $configuration
     *   The plugin configuration.
     *
     * @param string $plugin_id
     *   The plugin ID.
     *
     * @param mixed $plugin_definition
     *   The plugin definition.
     *
     * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
     *   The config factory.
     *
     * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
     *   The date formatter service.
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, 
      ConfigFactoryInterface $config_factory, DateFormatterInterface  $date_formatter) {
      parent::__construct($configuration, $plugin_id, $plugin_definition);
      $this->configFactory = $config_factory;
      $this->dateFormatter = $date_formatter;
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
        $container->get('date.formatter'),
      );
    }

    /**
     * {@inheritdoc}
     */
    public function build() {
      $admin_config = $this->configFactory->get('location_time.settings');
      $country = $admin_config->get('country');
      $city = $admin_config->get('city');
      $timezone = $admin_config->get('timezone');

      $items = [];
      if (!empty($timezone)) {
        // Set the default timezone value
        date_default_timezone_set($timezone);

        $date_formatted = $this->dateFormatter->format(time(), 'custom', 'dS M Y - g:i A');

        $items = array(
          'country' => $country,
          'city' => $city,
          'date_formatted' => $date_formatted,
        );
      }

      $data['#theme'] = 'location_time';
      $data['#data'] = $items;
      
      return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheMaxAge() {
      return 0;
    }
    
  }
