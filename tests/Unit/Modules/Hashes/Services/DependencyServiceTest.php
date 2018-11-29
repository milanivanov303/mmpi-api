<?php

use Modules\Hashes\Services\DependencyService;
use App\Models\ImxTable;
use App\Models\Source;
use Modules\Core\Models\Model;

class DependencyServiceTest extends TestCase
{
    public function test_validates_table_dependency()
    {
        $table = 'G_INDIVIDU';
        $depId = 12;

        // Mock ImxTable
        $imxTableMock = Mockery::mock(ImxTable::class);
        $imxTableMock
            ->shouldReceive('where')
            ->andReturn(
                Mockery::mock(['where' => Mockery::mock(['first' => (new Model)->setAttribute('id', $depId)])])
            )
            ->once();

        $this->app->instance(ImxTable::class, $imxTableMock);

        $dependencyService = new DependencyService($table);

        $this->assertEquals($table, $dependencyService->name);
        $this->assertEquals('table', $dependencyService->type);
        $this->assertNull($dependencyService->revision);
        $this->assertNull($dependencyService->path);
        $this->assertEquals($depId, $dependencyService->depId);
    }

    public function test_validates_source_dependency()
    {
        $depId = 12;

        // Mock Source
        $sourceMock = Mockery::mock(Source::class);
        $sourceMock
            ->shouldReceive('where')
            ->andReturn(
                Mockery::mock([
                    'withRevision' => Mockery::mock([
                        'first' => (new Model)->setRawAttributes(['source_id' => $depId, 'source_path' => 'pl/pack'])
                    ])
                ])
            )
            ->once();

        $this->app->instance(Source::class, $sourceMock);

        $dependencyService = new DependencyService('$IMX_HOME/pl/pack/acc_univ_curr_conv.pck >= 1.2');

        $this->assertEquals('acc_univ_curr_conv.pck', $dependencyService->name);
        $this->assertEquals('package', $dependencyService->type);
        $this->assertEquals('1.2', $dependencyService->revision);
        $this->assertEquals('pl/pack', $dependencyService->path);
        $this->assertEquals($depId, $dependencyService->depId);
    }

    public function test_source_dependency_with_invalid_revision()
    {
        // Mock Source
        $sourceMock = Mockery::mock(Source::class);
        $sourceMock
            ->shouldReceive('where')
            ->andReturn(
                Mockery::mock([
                    'withRevision' => Mockery::mock([
                        'first' => null
                    ])
                ])
            )
            ->once();

        $this->app->instance(Source::class, $sourceMock);

        $dependencyService = new DependencyService('$IMX_HOME/pl/pack/acc_univ_curr_conv.pck revision from refbg');

        $this->assertEquals('acc_univ_curr_conv.pck', $dependencyService->name);
        $this->assertEquals('package', $dependencyService->type);
        $this->assertNull($dependencyService->revision);
        $this->assertNull($dependencyService->path);
        $this->assertNull($dependencyService->depId);
    }

    public function test_invalid_table_dependency()
    {
        $table = 'TABLE_NAME';

        // Mock ImxTable
        $imxTableMock = Mockery::mock(ImxTable::class);
        $imxTableMock
            ->shouldReceive('where')
            ->andReturn(
                Mockery::mock(['where' => Mockery::mock(['first' => null])])
            )
            ->once();

        $this->app->instance(ImxTable::class, $imxTableMock);

        $dependencyService = new DependencyService($table);

        $this->assertEquals($table, $dependencyService->name);
        $this->assertNull($dependencyService->type);
        $this->assertNull($dependencyService->revision);
        $this->assertNull($dependencyService->path);
        $this->assertNull($dependencyService->depId);
    }

    public function test_table_dependency_with_column()
    {
        $table = 'G_INDIVIDU.NAME';
        $depId = 12;

        // Mock ImxTable
        $imxTableMock = Mockery::mock(ImxTable::class);
        $imxTableMock
            ->shouldReceive('where')
            ->andReturn(
                Mockery::mock(['where' => Mockery::mock(['first' => (new Model)->setAttribute('id', $depId)])])
            )
            ->once();

        $this->app->instance(ImxTable::class, $imxTableMock);

        $dependencyService = new DependencyService($table);

        $this->assertEquals($table, $dependencyService->name);
        $this->assertEquals('table', $dependencyService->type);
        $this->assertNull($dependencyService->revision);
        $this->assertNull($dependencyService->path);
        $this->assertEquals($depId, $dependencyService->depId);
    }
}
