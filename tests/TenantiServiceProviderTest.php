<?php namespace Orchestra\Tenanti\TestCase;

use Mockery as m;
use Orchestra\Tenanti\TenantiServiceProvider;

class TenantiServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test \Orchestra\Tenanti\TenantiServiceProvider is deferred.
     *
     * @test
     */
    public function testServiceProviderIsDeferred()
    {
        $stub = new TenantiServiceProvider(null);

        $this->assertTrue($stub->isDeferred());
    }

    /**
     * Test \Orchestra\Tenanti\TenantiServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app = m::mock('\Illuminate\Container\Container[singleton]');
        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository', '\ArrayAccess');

        $config->shouldReceive('offsetGet')->once()->with('orchestra.tenanti')->andReturn([]);

        $app->shouldReceive('singleton')->once()->with('orchestra.tenanti', m::type('Closure'))
            ->andReturnUsing(function ($n, $c) use ($app) {
                $app[$n] = $c($app);
            });

        $stub = new TenantiServiceProvider($app);

        $this->assertNull($stub->register());
        $this->assertInstanceOf('\Orchestra\Tenanti\TenantiManager', $app['orchestra.tenanti']);
    }

    /**
     * Test \Orchestra\Tenanti\TenantiServiceProvider::boot() method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $stub = m::mock('\Orchestra\Tenanti\TenantiServiceProvider[addConfigComponent,bootUsingLaravel]', [null])
                    ->shouldAllowMockingProtectedMethods();
        $path = realpath(__DIR__.'/../resources');

        $stub->shouldReceive('addConfigComponent')->once()
                ->with('orchestra/tenanti', 'orchestra/tenanti', $path.'/config')->andReturnNull()
            ->shouldReceive('bootUsingLaravel')->once()
                ->with($path)->andReturnNull();

        $this->assertNull($stub->boot());
    }

    /**
     * Test \Orchestra\Tenanti\TenantiServiceProvider::provides() method.
     *
     * @test
     */
    public function testProvidesMethod()
    {
        $stub = new TenantiServiceProvider(null);

        $this->assertContains('orchestra.tenanti', $stub->provides());
    }
}
