<?php

namespace PortfolioTools\Tests\Security;

use PHPUnit\Framework\TestCase;
use PortfolioTools\Security\Security;

class SecurityTest extends TestCase
{
    public function testGenerateCSRFToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = Security::generateCSRFToken();
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token)); // 32 bytes = 64 hex characters
    }

    public function testSanitizeInput()
    {
        $input = '<script>alert("xss")</script>';
        $sanitized = Security::sanitizeInput($input);
        $this->assertStringNotContainsString('<script>', $sanitized);
    }

    public function testGenerateSecureFilename()
    {
        $originalName = 'test file.txt';
        $secureFilename = Security::generateSecureFilename($originalName);
        
        $this->assertStringEndsWith('.txt', $secureFilename);
        $this->assertStringNotContainsString(' ', $secureFilename);
        $this->assertStringContainsString('_', $secureFilename);
    }

    public function testValidateFileUpload()
    {
        $file = [
            'name' => 'test.txt',
            'size' => 1024,
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE
        ];

        $errors = Security::validateFileUpload($file, ['txt'], 2048);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('No file', $errors[0]);
    }
}
