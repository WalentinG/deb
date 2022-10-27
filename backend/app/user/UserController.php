<?php

declare(strict_types=1);

namespace app\user;

use support\Db;
use support\Response;

final class UserController
{
    public function all(): Response
    {
        return ok(Db::table('users')->get());
    }
}
