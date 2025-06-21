<?php

namespace PortfolioTools\Tests\Core;

use PHPUnit\Framework\TestCase;
use PortfolioTools\Core\Config;

class ConfigTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        // Ensure config can be instantiated
        $this->config = Config::getInstance();
    }

    public function testSingletonPattern()
    {
        $config1 = Config::getInstance();
        $config2 = Config::getInstance();
        $this->assertSame($config1, $config2);
    }

    public function testGetDefaultValue()
    {
        $this->assertEquals('default', $this->config->get('nonexistent.key', 'default'));
    }

    public function testSetAndGet()
    {
        $this->config->set('test.key', 'test_value');
        $this->assertEquals('test_value', $this->config->get('test.key'));
    }

    public function testNestedKeys()
    {
        $this->config->set('nested.deep.key', 'deep_value');
        $this->assertEquals('deep_value', $this->config->get('nested.deep.key'));
    }

    public function testGetAll()
    {
        $allConfig = $this->config->all();
        $this->assertIsArray($allConfig);
        // Basic structure should exist
        $this->assertArrayHasKey('site', $allConfig);
        $this->assertArrayHasKey('paths', $allConfig);
    }

    public function testConfigHasBasicStructure()
    {
        $this->assertIsString($this->config->get('site.name', ''));
        $this->assertIsString($this->config->get('site.url', ''));
        $this->assertIsString($this->config->get('paths.root', ''));
    }
}
