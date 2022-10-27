<?php

declare(strict_types=1);

namespace tests\unit\support\transformation;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class NestedReplaceTest extends TestCase
{
    public function testTwoFields(): void
    {
        $data = [
            (object)['user' => 1, 'id' => 1, 'sender' => 3],
            (object)['user' => 2, 'id' => 2],
            (object)['user' => 3, 'id' => 3],
        ];
        $user = fn (array $ids) => [
            1 => ['name' => 'user1', 'age' => 1],
            2 => ['name' => 'user2', 'age' => 2],
            3 => ['name' => 'user3', 'age' => 3],
        ];

        $result = nestedReplace(collect($data), ['user, sender' => $user]);

        TestCase::assertEquals(
            [
                (object)['id' => 1, 'user' => ['name' => 'user1', 'age' => 1], 'sender' => ['name' => 'user3', 'age' => 3]],
                (object)['id' => 2, 'user' => ['name' => 'user2', 'age' => 2]],
                (object)['id' => 3, 'user' => ['name' => 'user3', 'age' => 3]],
            ],
            $result->toArray()
        );
    }

    public function testReplaceWithDotInName(): void
    {
        $data = [
            (object)['m.user' => 1, 'id' => 1],
        ];
        $user = fn (array $ids) => [
            1 => ['name' => 'user1', 'age' => 1],
        ];

        $result = nestedReplace(collect($data), ['m.user' => $user]);

        TestCase::assertEquals(
            [
                (object)['id' => 1, 'm.user' => ['name' => 'user1', 'age' => 1]],
            ],
            $result->toArray()
        );
    }

    public function testReplaceAndCutId(): void
    {
        $data = [
            (object)['user_id' => 1, 'id' => 1],
        ];
        $user = fn (array $ids) => [
            1 => ['name' => 'user1', 'age' => 1],
        ];

        $result = nestedReplace(collect($data), ['user_id' => $user]);

        TestCase::assertEquals(
            [
                (object)['id' => 1, 'user' => ['name' => 'user1', 'age' => 1]],
            ],
            $result->toArray()
        );
    }

    public function testSelectAs(): void
    {
        $from = ['chat_id', 'id'];

        $data = selectAs('a', 'a.b', $from);

        TestCase::assertEquals(['a.chat_id as a.b.chat_id', 'a.id as a.b.id'], $data);
    }
}
