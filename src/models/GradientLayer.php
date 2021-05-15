<?php

namespace alps\sharepreviews\models;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Fill\Gradient\Horizontal;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

class GradientLayer extends AbstractLayer
{
    public array $from = [0,0,0];
    public array $to = [100,100,100];
    public int $angle = 0;

    public function apply(ImageInterface $image): ImageInterface
    {
        $angle = $this->angle;
        $from = $this->toColor($this->from);
        $to = $this->toColor($this->to);

        $width = $this->width - $this->paddingLeft - $this->paddingRight;
        $height = $this->height - $this->paddingTop - $this->paddingBottom;

        $gradientImage = (new Imagine)->create(new Box($width + 2, $height + 2));

        if ($angle !== 0) {
            $gradientImage->rotate($angle * -1);
        }

        $fill = new Horizontal($width, $from, $to);

        $gradientImage->fill($fill);

        if ($angle !== 0) {
            $gradientImage->rotate($angle);
        }

        $size = $gradientImage->getSize();
        $x = $size->getWidth() / 2 - ($width / 2);
        $y = $size->getHeight() / 2 - ($height / 2);
        $gradientImage->crop(new Point($x, $y), new Box($width, $height));

        return $image->paste($gradientImage, new Point($this->paddingLeft, $this->paddingTop));
    }
}