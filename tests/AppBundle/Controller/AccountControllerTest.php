<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountControllerTest extends WebTestCase
{
    private $entityManager;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testNewAccount()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/account/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'The account creation page should load.');

        $form = $crawler->selectButton('Create')->form();

        $form['appbundle_account[username]'] = 'Test';
        $form['appbundle_account[email]'] = 'test@test.test';
        $form['appbundle_account[password][first]'] = 'test';
        $form['appbundle_account[password][second]'] = 'test';

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'The account creation should redirect to main page.');

        $this->entityManager->getRepository('AppBundle:Account')->createQueryBuilder('A')
            ->delete()
            ->where('A.slug = :slug')
            ->setParameter('slug', 'test')
            ->getQuery()
            ->execute();
        
    }

    /*
     *
      public function testCompleteScenario()
      {
      // Create a new client to browse the application
      $client = static::createClient();

      // Create a new entry in the database
      $crawler = $client->request('GET', '/account/');
      $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /account/");
      $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

      // Fill in the form and submit it
      $form = $crawler->selectButton('Create')->form(array(
      'appbundle_account[field_name]'  => 'Test',
      // ... other fields to fill
      ));

      $client->submit($form);
      $crawler = $client->followRedirect();

      // Check data in the show view
      $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

      // Edit the entity
      $crawler = $client->click($crawler->selectLink('Edit')->link());

      $form = $crawler->selectButton('Update')->form(array(
      'appbundle_account[field_name]'  => 'Foo',
      // ... other fields to fill
      ));

      $client->submit($form);
      $crawler = $client->followRedirect();

      // Check the element contains an attribute with value equals "Foo"
      $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

      // Delete the entity
      $client->submit($crawler->selectButton('Delete')->form());
      $crawler = $client->followRedirect();

      // Check the entity has been delete on the list
      $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
      }

     */

    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
