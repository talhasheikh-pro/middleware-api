<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ResourceControllerTest extends TestCase
{
    /**
     * Test to make sure response is expected if institute is found
     *
     * @return void
     */
    public function testInstituteIsFound()
    {
        $keyword = 'basic';
        $keywordCount = 2;
        $this->get("/verify-resource/{$keyword}");

        $this->assertEquals(
            $keywordCount, $this->response->getContent()
        );
    }

    /**
     * Test to make sure response is expected if institute is not found
     *
     * @return void
     */
    public function testInstituteIsNotFound()
    {
        $keyword = 'basic_not_found';
        $this->get("/verify-resource/{$keyword}");

        $this->assertEquals(
            'Institute not found, Relevant Ticket has been created', $this->response->getContent()
        );
    }
}
