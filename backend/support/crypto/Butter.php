<?php

declare(strict_types=1);

namespace support\crypto;

final class Butter
{
    public function __construct(
        public int $lob,       // lines of business = 业务线, 相同业务线的主播和排行榜相同，对应同一用户群体 1:大秀 2:绿播
        public int $rid,       // release id = 业务终端标识, 同一业务线发布一个或多个终端，不同终端的皮肤不同 = 1:小可爱 2:热风 3:初见
        public string $issued, //  发行代码 full(完整端) | lite(观众端) | pusher(主播端, 不归属任何业务平台)
        public string $uuid,   // xxx 设备号
        public int $dist,      // 0:自有平台分发(老版本没有写这个版本，因为NULL也认为是自有平台) 1:推广员1 2:推广员2
        public string $plat,   // android | ios
        public string $ver,    // 1.0.0 软件版本号
        public bool $emulator, // 0表示真机 1表示模拟器
        public string $pver,   // 系统版本号
        public string $nonce,  // 4aq87lz9 随机数 发版可变换，用于打击破解 begin ios *.81/android *.72
        public int $time,      // unix timestamp 时间戳（加了可以防机器人，但要考虑时区，未加）
    ) {
    }

    public static function decode(string $json): self
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            if (!\is_array($data)) {
                throw new \InvalidArgumentException('Decoded data is not array');
            }

            return new self(
                lob: (int)($data['lob'] ?? 1),
                rid: (int)($data['rid'] ?? 1),
                issued: (string)($data['issued'] ?? ''),
                uuid: (string)($data['uuid'] ?? ''),
                dist: (int)($data['dist'] ?? 0),
                plat: (string)($data['plat'] ?? ''),
                ver: (string)($data['ver'] ?? ''),
                emulator: (bool)($data['emulator'] ?? false),
                pver: (string)($data['pver'] ?? ''),
                nonce: (string)($data['nonce'] ?? ''),
                time: (int)($data['time'] ?? 0)
            );
        } catch (\Throwable $e) {
            throw ButterException::jsonDecodingFailed($e);
        }
    }

    public static function empty(): self
    {
        return new self(
            lob: 1,
            rid: 1,
            issued: '',
            uuid: '',
            dist: 0,
            plat: '',
            ver: '',
            emulator: false,
            pver: '',
            nonce: '',
            time: 0
        );
    }

    public function releaseId(): int
    {
        return $this->rid;
    }

    public function distributionMode(): int
    {
        return $this->dist;
    }

    public function systemVersion(): string
    {
        return $this->pver;
    }
}
