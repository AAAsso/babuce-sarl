<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Account;
use PHPUnit\Framework\TestCase;

class AccounTest extends TestCase
{
    public function testIsAdmin()
    {
        $account = new Account();
        $account->setAdmin();

        $result = $account->isAdmin();
        $result2 = $account->isGuest();
        $result3 = $account->isUser();


        $this->assertTrue($result, 'An admin user should have admin privileges');
        $this->assertTrue($result2, 'An admin user is also a guest user and should have guest privileges');
        $this->assertTrue($result3, 'An admin user is also a common user and should have common user privileges');
    }

    public function testIsGuest()
    {
        $account = new Account();
        $account->setGuest();

        $result = $account->isAdmin();
        $result2 = $account->isGuest();
        $result3 = $account->isUser();


        $this->assertFalse($result, 'A guest user can\'t have admin privileges');
        $this->assertTrue($result2, 'A guest user should have guest privileges');
        $this->assertTrue($result3, 'An guest user is also a common user and should have common user privileges');
    }

    public function testIsUser()
    {
        $account = new Account();
        $account->setUser();

        $result = $account->isAdmin();
        $result2 = $account->isGuest();
        $result3 = $account->isUser();


        $this->assertFalse($result, 'A common user can\'t have admin privileges');
        $this->assertFalse($result2, 'A common user can\'t have guest privileges');
        $this->assertTrue($result3, 'A common user should have common user privileges');
    }


}


