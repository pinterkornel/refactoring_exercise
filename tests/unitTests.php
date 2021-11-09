<?php

namespace App\Tests;
use App\getFulfillableOrders;
use Exception;
use PHPUnit\Framework\TestCase;

final class unitTests extends TestCase
{
    public function testExtraParams()
    {
        $argCount = 3;
        $args = [];
        $args[0] = "";
        $args[1] = "";
        $args[2] = "";
        $error = '';

        $class = new getFulfillableOrders($argCount, $args);
        try{
            $class->validateParams();
        } catch(Exception $ex){
            $error = $ex->getMessage();
        }

        $this->assertStringContainsString($error, 'Ambiguous number of parameters!' );
    }

    public function testNotValidJson()
    {
        $argCount = 2;
        $args = [];
        $args[0] = "";
        $args[1] = '{"a":1, "b": 1, }';
        $error = '';

        $class = new getFulfillableOrders($argCount, $args);
        try{
            $class->validateParams();
        } catch(Exception $ex){
            $error = $ex->getMessage();
        }

        $this->assertStringContainsString('Invalid json!', $error);
    }

    public function testValidJson()
    {
        $argCount = 2;
        $args = [];
        $args[0] = "";
        $args[1] = '{"1":8,"2":4,"3":5}';
        $error = '';

        $class = new getFulfillableOrders($argCount, $args);
        try{
            $class->validateParams();
        } catch(Exception $ex){
            $error = $ex->getMessage();
        }

        $this->assertEquals('', $error);
    }

    public function testDisplay()
    {
        $argCount = 2;
        $args = [];
        $args[0] = "";
        $args[1] = '{"1":8,"2":4,"3":5}';
        $error = '';

        $class = new getFulfillableOrders($argCount, $args);
        try{
            $class->displayOrdersOnConsole();
        } catch(Exception $ex){
            $error = $ex->getMessage();
        }

        $this->assertEquals('', $error);
    }

    public function testAddQuotesToJsonKeys()
    {
        $argCount = 2;
        $args = [];
        $args[0] = "";
        $args[1] = '{"1":8,"2":4,"3":5}';

        $class = new getFulfillableOrders($argCount, $args);
        $json1 = json_decode($class->addQuotesToJsonKeys('{"1":8,"2":4,"3":5}'));
        $json2 = json_decode($class->addQuotesToJsonKeys('{1:8,2:4,3:5}'));
        $json3 = json_decode($class->addQuotesToJsonKeys('{1:8,"2":4,3:5}'));
        $json4 = json_decode($class->addQuotesToJsonKeys('{a:8,"2":"b",3:5}'));

        $this->assertNotNull($json1);
        $this->assertNotNull($json2);
        $this->assertNotNull($json3);
        $this->assertNotNull($json4);
    }

}
