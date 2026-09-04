<?php

/**
 * This file is included very early. See autoload.files in composer.json and
 * https://getcomposer.org/doc/04-schema.md#files
 */

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\Adapter\PutenvAdapter;

/**
 * Load any .env file. See /.env.example.
 */
$repository = RepositoryBuilder::createWithDefaultAdapters()
  ->addAdapter(PutenvAdapter::class)
  ->immutable()
  ->make();

$dotenv = Dotenv::create($repository, __DIR__);
try {
  $dotenv->load();
}
catch (InvalidPathException $e) {
  // Do nothing. Production environments rarely use .env files.
}
