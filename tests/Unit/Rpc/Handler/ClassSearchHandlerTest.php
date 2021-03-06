<?php

namespace Phpactor\Tests\Unit\Rpc\Handler;

use Phpactor\Application\ClassSearch;
use Phpactor\Rpc\Handler\ClassSearchHandler;
use Phpactor\Rpc\Handler;
use Phpactor\Rpc\Response\EchoResponse;
use Phpactor\Rpc\Response\ReturnResponse;
use Phpactor\Rpc\Response\ReturnChoiceResponse;

class ClassSearchHandlerTest extends HandlerTestCase
{
    /**
     * @var ObjectProphecy
     */
    private $classSearch;

    public function setUp()
    {
        $this->classSearch = $this->prophesize(ClassSearch::class);
    }

    public function createHandler(): Handler
    {
        return new ClassSearchHandler(
            $this->classSearch->reveal()
        );
    }

    /**
     * If not results are found, echo a message
     */
    public function testNoResults()
    {
        $this->classSearch->classSearch('composer', 'AAA')
            ->willReturn([]);

        $action = $this->handle('class_search', [
            'short_name' => 'AAA',
        ]);

        $this->assertInstanceOf(EchoResponse::class, $action);
        $this->assertContains('No classes found', $action->message());
    }

    /**
     * If 1 result is found, return the value.
     */
    public function testOneResult()
    {
        $this->classSearch->classSearch('composer', 'AAA')
            ->willReturn([
                [
                    'class' => 'Foobar',
                ]
            ]);

        $action = $this->handle('class_search', [
            'short_name' => 'AAA',
        ]);

        $this->assertInstanceOf(ReturnResponse::class, $action);
        $this->assertEquals([
            'class' => 'Foobar',
        ], $action->value());
    }

    /**
     * Many results, show a choice
     */
    public function testManyResult()
    {
        $this->classSearch->classSearch('composer', 'AAA')
            ->willReturn([
                [
                    'class' => 'AAA',
                ],
                [
                    'class' => 'BBB',
                ],
            ]);

        $action = $this->handle('class_search', [
            'short_name' => 'AAA',
        ]);

        $this->assertInstanceOf(ReturnChoiceResponse::class, $action);
        $this->assertCount(2, $action->options());
    }
}
