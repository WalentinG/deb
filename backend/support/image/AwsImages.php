<?php

declare(strict_types=1);

namespace support\image;

use Aws\S3\S3Client;
use Intervention\Image\ImageManager;

final class AwsImages implements Images
{
    public function __construct(
        private S3Client $s3Client,
        private ImageManager $imageManager,
        private string $bucket,
    ) {
    }

    public function store(Image $image, string $path, int $width = Image::DEFAULT_WIDTH, int $height = Image::DEFAULT_HEIGHT): ImageUrl
    {
        $image = $this->imageManager->make($image->file)->resize($width, $height)->encode(Image::FORMAT, Image::QUALITY);

        $result = $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $path,
            'Body' => $image->getEncoded(),
        ]);

        return new ImageUrl(toStr($result->get('ObjectURL')));
    }
}
