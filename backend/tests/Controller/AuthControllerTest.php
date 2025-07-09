<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testRegisterSuccess(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'newuser_' . uniqid() . '@example.com',
            'password' => 'newpassword123'
        ]));
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
    }

    public function testRegisterDuplicateEmail(): void
    {
        $client = static::createClient();
        // Utilise un email déjà existant (fixtures)
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'user@example.com',
            'password' => 'password'
        ]));
        $this->assertResponseStatusCodeSame(409);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Email déjà utilisé', $data['error']);
    }

    public function testRegisterInvalidEmail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'notanemail',
            'password' => 'password'
        ]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('email', $client->getResponse()->getContent());
    }

    public function testLoginSuccess(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'user@example.com',
            'password' => 'password'
        ]));
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('set-cookie', $client->getResponse()->headers->all(), 'Session cookie attendu');
    }

    public function testLoginWrongPassword(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'user@example.com',
            'password' => 'wrongpassword'
        ]));
        $this->assertResponseStatusCodeSame(401);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Email ou mot de passe incorrect', $data['error']);
    }

    public function testMeAuthenticated(): void
    {
        $client = static::createClient();
        // Login d'abord
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'user@example.com',
            'password' => 'password'
        ]));
        $client->request('GET', '/api/me');
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertStringContainsString('user@example.com', $client->getResponse()->getContent());
    }

    public function testMeNotAuthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/me');
        $this->assertResponseStatusCodeSame(401);
        $this->assertStringContainsString('authentication', strtolower($client->getResponse()->getContent()));
    }

    public function testLogout(): void
    {
        $client = static::createClient();
        // Login d'abord
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'user@example.com',
            'password' => 'password'
        ]));
        $client->request('POST', '/api/logout');
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
        // Après logout, /api/me doit renvoyer 401
        $client->request('GET', '/api/me');
        $this->assertResponseStatusCodeSame(401);
    }
} 