<?php

namespace DXMB\Modules\Tests;

use DXMB\Modules\Contracts\RepositoryInterface;
use DXMB\Modules\Laravel\LaravelFileRepository;

class ContractsServiceProviderTest extends BaseTestCase
{
    /** @test */
    public function it_binds_repository_interface_with_implementation()
    {
        $this->assertInstanceOf(LaravelFileRepository::class, app(RepositoryInterface::class));
    }
}
