<?php

require_once __DIR__.'/../vendor/autoload.php';

define("DBNAME", __DIR__."/../src/db.sqlite");

$app = new \Silex\Application();
$app['debug'] = true;

$app['pdo'] = new \PDO("sqlite:".DBNAME);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\ValidatorServiceProvider());

// Doctrine config
use Doctrine\ORM\Tools\Setup,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\EventManager as EventManager,
    Doctrine\ORM\Events,
    Doctrine\ORM\Configuration,
    Doctrine\Common\Cache\ArrayCache as Cache,
    Doctrine\Common\Annotations\AnnotationRegistry,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\ClassLoader;

$cache = new Doctrine\Common\Cache\ArrayCache;
$annotationReader = new Doctrine\Common\Annotations\AnnotationReader;

$cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
    $annotationReader, // use reader
    $cache // and a cache driver
);

$annotationDriver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver(
    $cachedAnnotationReader, // our cached annotation reader
    array(__DIR__ . DIRECTORY_SEPARATOR)
);

$driverChain = new Doctrine\ORM\Mapping\Driver\DriverChain();
$driverChain->addDriver($annotationDriver,'DevWellington');

$config = new Doctrine\ORM\Configuration;
$config->setProxyDir('/tmp');
$config->setProxyNamespace('Proxy');
$config->setAutoGenerateProxyClasses(true); // this can be based on production config.
// register metadata driver
$config->setMetadataDriverImpl($driverChain);
// use our allready initialized cache driver
$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

AnnotationRegistry::registerFile(__DIR__.DIRECTORY_SEPARATOR.'..'. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'orm' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'ORM' . DIRECTORY_SEPARATOR . 'Mapping' . DIRECTORY_SEPARATOR . 'Driver' . DIRECTORY_SEPARATOR . 'DoctrineAnnotations.php');

$evm = new Doctrine\Common\EventManager();
$em = EntityManager::create(
    array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__.'/../src/db.sqlite'
    ),
    $config,
    $evm
);

use Symfony\Component\Serializer\Encoder\JsonEncode,
    Symfony\Component\Serializer\Encoder\JsonEncoder,
    Symfony\Component\Serializer\Normalizer\AbstractNormalizer,
    Symfony\Component\Serializer\Normalizer\ObjectNormalizer,
    Symfony\Component\Serializer\Serializer
;

$normalizer = new ObjectNormalizer();
$encoder = new JsonEncoder();

$app['serializer'] = new Serializer([$normalizer], [$encoder]);