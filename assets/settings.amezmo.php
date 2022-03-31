<?php
/**
 * @file
 * Amezmo settings.
 */

// Configure the database.
if (isset($_ENV['APP_HOSTNAME'])) {
  $databases['default']['default'] = [
    'database' => $_ENV['DB_DATABASE'],
    'driver' => 'mysql',
    'host' =>  $_ENV['DB_HOST'],
    'password' => $_ENV['DB_PASSWORD'],
    'port' => $_ENV['DB_PORT'],
    'prefix' => '',
    'username' => $_ENV['DB_USER']
  ];

  // Configure private and temporary file paths.
  $settings['file_private_path'] = $_ENV['STORAGE_DIRECTORY'] . '/private';

  // Configure the default PhpStorage and Twig template cache directories.
  $settings['php_storage']['default']['directory'] = $settings['file_private_path'];
  $settings['php_storage']['twig']['directory'] = $settings['file_private_path'];

  $settings['file_chmod_directory'] = 02775;
  $settings['file_chmod_file'] = 0664;

  // Set the project-specific entropy value, used for generating one-time
  // keys and such.
  // TODO: FIND A WAY TO REPLACE THIS WITH APP_KEY
  $settings['hash_salt'] = $_ENV['APP_KEY'];
}

// Set redis configuration.
if (!empty($_ENV['REDIS_HOST'])) {
  if (!\Drupal\Core\Installer\InstallerKernel::installationAttempted() && extension_loaded('redis') && class_exists('Drupal\redis\ClientFactory')) {
    // Set Redis as the default backend for any cache bin not otherwise specified.
    $settings['cache']['default'] = 'cache.backend.redis';
    $settings['redis.connection']['host'] = $_ENV['REDIS_HOST'];
    $settings['redis.connection']['port'] = $_ENV['REDIS_PORT'];

    // Apply changes to the container configuration to better leverage Redis.
    // This includes using Redis for the lock and flood control systems, as well
    // as the cache tag checksum. Alternatively, copy the contents of that file
    // to your project-specific services.yml file, modify as appropriate, and
    // remove this line.
    $settings['container_yamls'][] = 'modules/contrib/redis/example.services.yml';

    // Allow the services to work before the Redis module itself is enabled.
    $settings['container_yamls'][] = 'modules/contrib/redis/redis.services.yml';

    // Manually add the classloader path, this is required for the container cache bin definition below
    // and allows to use it without the redis module being enabled.
    $class_loader->addPsr4('Drupal\\redis\\', 'modules/contrib/redis/src');

    // Use redis for container cache.
    // The container cache is used to load the container definition itself, and
    // thus any configuration stored in the container itself is not available
    // yet. These lines force the container cache to use Redis rather than the
    // default SQL cache.
    $settings['bootstrap_container_definition'] = [
      'parameters' => [],
      'services' => [
        'redis.factory' => [
          'class' => 'Drupal\redis\ClientFactory',
        ],
        'cache.backend.redis' => [
          'class' => 'Drupal\redis\Cache\CacheBackendFactory',
          'arguments' => ['@redis.factory', '@cache_tags_provider.container', '@serialization.phpserialize'],
        ],
        'cache.container' => [
          'class' => '\Drupal\redis\Cache\PhpRedis',
          'factory' => ['@cache.backend.redis', 'get'],
          'arguments' => ['container'],
        ],
        'cache_tags_provider.container' => [
          'class' => 'Drupal\redis\Cache\RedisCacheTagsChecksum',
          'arguments' => ['@redis.factory'],
        ],
        'serialization.phpserialize' => [
          'class' => 'Drupal\Component\Serialization\PhpSerialize',
        ],
      ],
    ];
  }
}
