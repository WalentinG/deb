<?php

declare(strict_types=1);

namespace support\image;

use Respect\Validation\Validator as v;
use Webman\Http\UploadFile;

final class Image
{
    public const FORMAT = 'jpg';
    public const QUALITY = 80;
    public const DEFAULT_WIDTH = 252;
    public const DEFAULT_HEIGHT = 188;
    public const SUPPORTED_FORMATS = ['jpg', 'jpeg', 'png'];

    public function __construct(public UploadFile $file)
    {
        v::trueVal()->check($this->file->isValid());
        v::in(self::SUPPORTED_FORMATS)->check($this->file->getUploadExtension());
    }
}
