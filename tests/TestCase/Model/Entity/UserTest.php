<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\User;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\User Test Case
 */
class UserTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\User
     */
    public $User;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->User = new User();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->User);

        parent::tearDown();
    }

    /**
     * Test log method
     *
     * @return void
     */
    public function testLog()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testGetName()
    {
        $this->User->firstname = "Robert";
        $this->User->lastname = "Wilson";
        $name = $this->User->name;
        $this->assertEquals('Robert Wilson', $name);
    }
}
