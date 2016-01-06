<?php
namespace Seo\Test\TestCase\Shell;

use Cake\TestSuite\TestCase;
use Seo\Shell\SeoShell;

/**
 * Seo\Shell\SeoShell Test Case
 */
class SeoShellTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMock('Cake\Console\ConsoleIo');
        $this->Seo = new SeoShell($this->io);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Seo);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
