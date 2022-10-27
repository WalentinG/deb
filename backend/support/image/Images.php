<?php

declare(strict_types=1);

namespace support\image;

interface Images
{
    public function store(Image $image, string $path, int $width = Image::DEFAULT_WIDTH, int $height = Image::DEFAULT_HEIGHT): ImageUrl;
}
