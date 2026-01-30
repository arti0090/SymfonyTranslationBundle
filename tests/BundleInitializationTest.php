<?php

namespace Locastic\SymfonyTranslationBundle\Tests;

use Locastic\SymfonyTranslationBundle\Cli\WriteTranslationValuesCommand;
use Locastic\SymfonyTranslationBundle\Factory\TranslationMigrationFactory;
use Locastic\SymfonyTranslationBundle\Factory\TranslationMigrationFactoryInterface;
use Locastic\SymfonyTranslationBundle\LocasticSymfonyTranslationBundle;
use Locastic\SymfonyTranslationBundle\Provider\TranslationsProvider;
use Locastic\SymfonyTranslationBundle\Provider\TranslationsProviderInterface;
use Locastic\SymfonyTranslationBundle\Twig\TranslationExtension;
use Nyholm\BundleTest\TestKernel;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\KernelInterface;

class BundleInitializationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /** @var TestKernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(LocasticSymfonyTranslationBundle::class);
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testInitBundle(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $serviceId = TranslationsProvider::class;
        $this->assertTrue($container->has($serviceId));

        $service = $container->get($serviceId);
        $this->assertInstanceOf(TranslationsProvider::class, $service);
    }

    public function testInterfaceAliases(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->assertTrue($container->has(TranslationsProviderInterface::class));

        $service = $container->get(TranslationsProviderInterface::class);
        $this->assertInstanceOf(TranslationsProvider::class, $service);
    }

    public function testTwigExtensionIsRegistered(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        if ($container->has('twig')) {
            $twig = $container->get('twig');
            $extension = $container->get(TranslationExtension::class);
            Assert::assertNotNull($extension);

            $this->assertTrue($twig->hasExtension($extension::class));
        }
    }

    public function testConsoleCommandsAreRegistered(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->assertTrue($container->has(WriteTranslationValuesCommand::class));

        $command = $container->get(WriteTranslationValuesCommand::class);
        $this->assertInstanceOf(Command::class, $command);
    }

    public function testMigrationFactoryConfiguration(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $factory = $container->get(TranslationMigrationFactoryInterface::class);
        $this->assertInstanceOf(TranslationMigrationFactory::class, $factory);
    }
}
