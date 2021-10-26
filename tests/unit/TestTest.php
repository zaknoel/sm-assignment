<?php

declare(strict_types=1);

namespace Tests\unit;

use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use SocialPost\Driver\FictionalDriver;
use SocialPost\Dto\SocialPostTo;
use SocialPost\Hydrator\FictionalPostHydrator;
use Statistics\Calculator\NoopCalculator;
use Statistics\Dto\ParamsTo;
use Statistics\Dto\StatisticsTo;
use Throwable;

/**
 * Class ATestTest
 *
 * @package Tests\unit
 */
class TestTest extends TestCase
{
    /**
     * @test
     */
    public function testNothing(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @throws ReflectionException
     */
    protected static function getMethod($class, $method_name): \ReflectionMethod
    {
        $class = new \ReflectionClass($class);
        $method = $class->getMethod($method_name);
        $method->setAccessible(true);
        return $method;
    }
    ///test methods to existence
    public function testAccumulateMethod()
    {
        try {
            $obj = new NoopCalculator();
            $foo = self::getMethod($obj, 'doAccumulate');
            $foo->invokeArgs($obj, array(new SocialPostTo()));
            $this->assertTrue(true, 'Method doAccumulate exists');
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testCalculateMethod()
    {
        try {
            $obj = new NoopCalculator();
            $foo = self::getMethod($obj, 'doCalculate');
            $foo->invokeArgs($obj, array(new SocialPostTo()));
            $this->assertTrue(true, 'Method doCalculate exists');
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    //test calculator

    /**
     * @throws ReflectionException
     */
    public function testNoopCalculator(){
        $expectedValue=1;
        $expectedFloatValue=1.33;
        $data_path=str_replace('/unit', '/data', __DIR__);
        $json=file_get_contents($data_path.'/social-posts-response.json');
        $data=\GuzzleHttp\json_decode($json, true);
        $mockedInstance  = $this->getMockBuilder(FictionalDriver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $reflectedMethod = new \ReflectionMethod(
            $mockedInstance,
            'extractPosts'
        );
        $reflectedMethod->setAccessible(true);

        try{
            $posts=$reflectedMethod->invokeArgs(
                $mockedInstance,
                [$data]
            );
            $calc=new NoopCalculator();
            $params=(new ParamsTo())
                ->setStartDate(new DateTime('2018-08-01'))
                ->setEndDate(new DateTime('2018-08-31'))
                ->setStatName('Test calc')
            ;
            $calc->setParameters($params);
            $hydrator=new FictionalPostHydrator();
            foreach ($posts as $post){
                $calc->accumulateData($hydrator->hydrate($post));
            }
            $result=$calc->calculate();
            $this->assertInstanceOf(StatisticsTo::class, $result);
            $this->assertEquals($expectedValue, $result->getValue());
            $this->assertEquals($expectedFloatValue, $calc->getFloatResult());
        }catch (Throwable $e){
            $this->fail($e->getMessage());
        }

    }


}
