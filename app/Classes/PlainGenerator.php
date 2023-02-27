<?php

declare(strict_types = 1);

namespace App\Classes;

use Illuminate\Support\Str;
use Intervention\Image\AbstractFont;
use Intervention\Image\Gd\Font;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Symfony\Component\Finder\SplFileInfo;

/**
 * 生成基本文字
 */
class PlainGenerator
{

    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager();
    }

    public static function pathName($text): string
    {
        $path = Str::before($text, '-');
        $dir  = explode('.', $path);
        $name = Str::after($text, '-');
        return storage_path('app/' . implode('/', $dir) . '/' . $name);
    }

    /**
     * 生成图片
     * @param string $text
     * @param string $fc
     * @return Image
     */
    public function gen(string $text = '', $fc = '#eae0d0'): Image
    {

        $savePath = self::pathName($text);
        if (file_exists($savePath)) {
            return $this->manager->make($savePath);
        }

        $files = app('files')->allFiles(resource_path('images'));
        shuffle($files);
        if (isset($files[0]) && $files[0] instanceof SplFileInfo) {
            $path = $files[0]->getPathname();
        }
        else {
            die('错误的地址');
        }
        $img      = $this->manager->make($path);
        $width    = $img->width();
        $height   = $img->height();
        $fontFile = resource_path('fonts/sarasa-mono-light.ttf');

        // min: 20 /max 50
        $size = (($width / 10) <= 14)
            ? 14
            : (($width / 10) >= 50 ? 50 : round($width / 10));

        // write size
        $text     = Str::after($text, '-');
        $text     = basename($text, '.' . self::ext($text));
        $sizeFont = new Font();
        $sizeFont->text($text);
        $sizeFont->size($size);
        $sizeFont->file($fontFile);
        $box        = $sizeFont->getBoxSize();
        $fontHeight = $box['height'];
        $fontWidth  = $box['width'];
        $y          = ($height - $fontHeight) / 2 + $fontHeight;
        $x          = ($width - $fontWidth) / 2;

        $img->text($text, $x, $y, function (AbstractFont $font) use ($fontFile, $size, $fc) {
            $font->align('left');
            $font->color($fc);
            $font->size($size);
            $font->file($fontFile);
        });
        app('files')->makeDirectory(dirname($savePath), 0755, true);
        $img->save($savePath);
        return $img;
    }

    public static function ext($filename): string
    {
        return strtolower(trim(substr(strrchr($filename, '.'), 1)));
    }
}
