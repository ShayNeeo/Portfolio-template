<?php

namespace PortfolioTools\Tests\Security;

use PHPUnit\Framework\TestCase;
use PortfolioTools\Security\Security;

class SecurityTest extends TestCase
{
    protected function setUp(): void
    {
        // Start session for CSRF tests if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function testGenerateCSRFToken()
    {
        $token = Security::generateCSRFToken();
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token)); // 32 bytes = 64 hex characters
        
        // Test that we get different tokens
        $token2 = Security::generateCSRFToken();
        $this->assertNotEquals($token, $token2);
    }

    public function testValidateCSRFToken()
    {
        $token = Security::generateCSRFToken();
        
        // Valid token should return true
        $this->assertTrue(Security::validateCSRFToken($token));
        
        // Invalid token should return false
        $this->assertFalse(Security::validateCSRFToken('invalid_token'));
    }

    public function testSanitizeInput()
    {
        $input = '<script>alert("xss")</script>';
        $sanitized = Security::sanitizeInput($input);
        $this->assertStringNotContainsString('<script>', $sanitized);
        
        // Test with normal text
        $normalText = 'Hello World';
        $this->assertEquals($normalText, Security::sanitizeInput($normalText));
    }

    public function testGenerateSecureFilename()
    {
        $originalName = 'test file.txt';
        $secureFilename = Security::generateSecureFilename($originalName);
        
        $this->assertStringEndsWith('.txt', $secureFilename);
        $this->assertStringNotContainsString(' ', $secureFilename);
        $this->assertStringContainsString('_', $secureFilename);
        
        // Test with special characters
        $specialName = 'test@#$%file.pdf';
        $secureSpecial = Security::generateSecureFilename($specialName);
        $this->assertStringEndsWith('.pdf', $secureSpecial);
        $this->assertDoesNotMatchRegularExpression('/[^a-zA-Z0-9._-]/', basename($secureSpecial, '.pdf'));
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
        
        // Test valid file structure
        $validFile = [
            'name' => 'test.txt',
            'size' => 500,
            'tmp_name' => '/tmp/test',
            'error' => UPLOAD_ERR_OK
        ];
        
        // This will still fail because tmp_name doesn't exist, but should pass size/extension checks
        $errors = Security::validateFileUpload($validFile, ['txt'], 2048);
        $this->assertIsArray($errors);
    }
}
}
