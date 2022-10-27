<?php

declare(strict_types=1);

namespace tests\unit\support\marshal;

use PHPUnit\Framework\TestCase;

use support\marshal\InvalidConstructor;
use support\marshal\ParameterNotFound;

use function unmarshal;

/**
 * @internal
 * @coversNothing
 */
final class UnmarshalTest extends TestCase
{
    public function testSuccess(): void
    {
        $data = ['string' => 'text', 'int' => 100500, 'enum' => 'a', 'value' => 'text'];

        $object = unmarshal(PublicConstructor::class, $data);

        TestCase::assertEquals(new PublicConstructor('text', 100500, EnumStub::a, new SomeValue('text')), $object);
    }

    public function testVariadic(): void
    {
        $data = ['values' => ['1', '2']];

        $object = unmarshal(VariadicConstructor::class, $data);
        TestCase::assertEquals(new VariadicConstructor(new SomeValue('1'), new SomeValue('2')), $object);
    }


    public function testNoParameter(): void
    {
        TestCase::expectException(ParameterNotFound::class);

        $data = ['string' => 'text'];

        unmarshal(PublicConstructor::class, $data);
    }

    public function testInvalidParameterType(): void
    {
        TestCase::expectException(\TypeError::class);

        $data = ['string' => 'text', 'int' => '100500', 'float' => 100.500, 'enum' => 'a', 'value' => 'text'];

        unmarshal(PublicConstructor::class, $data);
    }

    public function testNoConstructor(): void
    {
        TestCase::expectException(InvalidConstructor::class);

        $data = ['string' => 'text', 'int' => 100500, 'float' => 100.500, 'enum' => 'a'];

        unmarshal(NoConstructorStub::class, $data);
    }

    public function testConstructorIsPrivate(): void
    {
        TestCase::expectException(InvalidConstructor::class);

        $data = ['string' => 'text', 'int' => 100500, 'float' => 100.500, 'enum' => 'a'];

        unmarshal(PrivateConstructorStub::class, $data);
    }
}
