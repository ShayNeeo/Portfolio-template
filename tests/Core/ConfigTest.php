<?php

namespace PortfolioTools\Tests\Core;

use PHPUnit\Framework\TestCase;
use PortfolioTools\Core\Config;

class ConfigTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        $this->config = Config::getInstance();
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
        $this->assertArrayHasKey('site', $allConfig);
        $this->assertArrayHasKey('paths', $allConfig);
    }
}
