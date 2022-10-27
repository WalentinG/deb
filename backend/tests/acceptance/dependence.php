<?php

declare(strict_types=1);

namespace tests\acceptance;

use support\image\Images;
use support\sms\Sms;
use support\socket\Sockets;
use tests\acceptance\stubs\support\ImageStoreStub;
use tests\acceptance\stubs\support\SmsStub;
use tests\acceptance\stubs\support\SocketsStub;

return [
    Images::class => static fn () => new ImageStoreStub(),
    Sms::class => static fn () => new SmsStub(),
    Sockets::class => static fn () => new SocketsStub(),
];
