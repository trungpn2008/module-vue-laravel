<?php

namespace DXMB\Modules\Tests\Commands;

use Illuminate\Support\Facades\Schema;
use DXMB\Modules\FileRepository;
use DXMB\Modules\Laravel\LaravelFileRepository;
use DXMB\Modules\Tests\BaseTestCase;

abstract class MigrateCommandTest extends BaseTestCase
{
    /**
     * @var FileRepository
     */
    private $repository;
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $finder;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new LaravelFileRepository($this->app);
        $this->finder = $this->app['files'];
    }

    /** @test */
    public function it_migrates_a_module()
    {
        $this->repository->addLocation(__DIR__ . '/../stubs/Recipe');

        $this->artisan('module:migrate', ['module' => 'Recipe']);

        dd(Schema::hasTable('recipe__recipes'), $this->app['db']->table('recipe__recipes')->get());
    }
}
