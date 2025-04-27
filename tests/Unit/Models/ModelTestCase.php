<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Mockery\MockInterface;



class ModelTestCase extends TestCase
{
    protected function createPartialMockModel(string $className, array $mockMethods = []): MockInterface
    {
        $mockModel = Mockery::mock($className)->makePartial();

        foreach ($mockMethods as $method => $returnValue) {
            $mockModel->allows($method)->andReturn($returnValue);
        }

        return $mockModel;
    }

    protected function createMockBuilderReturnsData(array $returnData = [], string $getMethod = 'get'): MockInterface
    {
        $mockBuilder = Mockery::mock(Builder::class);

        $mockBuilder->allows($getMethod)
            ->andReturns(new Collection($returnData));

        return $mockBuilder;
    }

    protected function createMockBuilderReturnsObject(object $returnObject, string $getMethod = 'get'): MockInterface
    {
        $mockBuilder = Mockery::mock(Builder::class);

        $mockBuilder->allows($getMethod)
            ->andReturns($returnObject);

        return $mockBuilder;
    }

    protected function ignoreQueryConditions(MockInterface $mockBuilder): void
    {
        $mockBuilder->allows('where')
            ->andReturnSelf();

        $mockBuilder->allows('orderBy')
            ->andReturnSelf();
    }

    protected function applyExpectedMockQueryConditions(MockInterface $mockBuilder, array $conditions = []): void
    {
        foreach ($conditions as $condition) {
            $mockBuilder->expects($condition['expects'])
                ->with(...$condition['arguments'])
                ->andReturnSelf();
        }
    }

    protected function createCollection($data): Collection
    {
        return new Collection($data);
    }

    protected function createTestDataCollection(): Collection
    {
        return $this->createCollection(self::TEST_DATA);
    }
}
