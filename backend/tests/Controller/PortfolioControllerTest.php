<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PortfolioControllerTest extends WebTestCase
{
    public function testRegister(): void
    {
        $client = static::createClient();
        $uniqueEmail = 'testuser_' . uniqid() . '@example.com';
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => $uniqueEmail,
            'password' => 'testpass123'
        ]));
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('"success":true', $client->getResponse()->getContent());
    }

    public function testLoginAndSession(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'user@example.com',
            'password' => 'password'
        ]));
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('set-cookie', $client->getResponse()->headers->all(), 'Session cookie attendu');
    }

    public function testScpiList(): void
    {
        $client = static::createClient();
        // Login d'abord
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'user@example.com',
            'password' => 'password'
        ]));
        $this->assertResponseIsSuccessful();
        
        // Récupérer le cookie de session
        $cookies = $client->getCookieJar()->all();
        $sessionCookie = null;
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'PHPSESSID') {
                $sessionCookie = $cookie->getValue();
            }
        }
        $this->assertNotNull($sessionCookie, 'Le cookie PHPSESSID doit être présent après login');
        
        // Ajouter le cookie manuellement à la requête suivante
        $client->request('GET', '/api/scpis', [], [], [
            'HTTP_COOKIE' => 'PHPSESSID=' . $sessionCookie,
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('nom', $data[0]);
        $this->assertArrayHasKey('tauxRendementAnnuel', $data[0]);
    }

    public function testPortfolioSimulation(): void
    {
        $client = static::createClient();
        // Login d'abord
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'user@example.com',
            'password' => 'password'
        ]));
        $this->assertResponseIsSuccessful();
        
        // Récupérer le cookie de session
        $cookies = $client->getCookieJar()->all();
        $sessionCookie = null;
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'PHPSESSID') {
                $sessionCookie = $cookie->getValue();
            }
        }
        $this->assertNotNull($sessionCookie, 'Le cookie PHPSESSID doit être présent après login');
        
        // Ajouter le cookie manuellement à la requête suivante
        $client->request('POST', '/api/portfolio', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_COOKIE' => 'PHPSESSID=' . $sessionCookie,
        ], json_encode([
            'portefeuille' => [
                ['scpiId' => 1, 'montant' => 10000],
                ['scpiId' => 2, 'montant' => 5000]
            ]
        ]));
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('montantTotal', $data);
        $this->assertArrayHasKey('rendementMoyen', $data);
        $this->assertArrayHasKey('revenuAnnuel', $data);
        $this->assertArrayHasKey('details', $data);
    }
}
