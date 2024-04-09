<?php

namespace shirne\common;

/**
 * 图像处理综合类, 暂不支持多侦gif格式
 * @package shirne\common
 * @require gd2
 */
class Image
{
    /**
     * @var resource|GdImage
     */
    private $image;

    /**
     * @var array
     */
    private $imageInfo;

    /**
     * @var string
     */
    private $type;

    /**
     * @var integer
     */
    private $width;

    /**
     * @var integer
     */
    private $height;

    public function __construct($init = array())
    {
        if (!empty($init)) {
            if (!is_array($init)) {
                if (is_numeric($init)) {
                    $init = [
                        'width' => $init,
                        'height' => $init,
                    ];
                } else {
                    $init = ['file' => $init];
                }
            }
            if (isset($init['file'])) {
                if (isset($init['type'])) {
                    $this->loadFromFile($init['file'], $init['type']);
                } else {
                    $this->loadFromFile($init['file']);
                }
            } elseif (isset($init['width'])) {
                if (isset($init['bg'])) {
                    $this->create($init['width'], $init['height'], $init['bg']);
                } else {
                    $this->create($init['width'], $init['height']);
                }
            }
        }
    }

    /**
     * 当前图像资源标识符
     * @return resource
     */
    public function getResource()
    {
        return $this->image;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return NULL;
    }

    /**
     * 设定图像类型,用于输出
     * @param $type
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * 创建空白图片
     * @param $width int
     * @param $height int
     * @param $bgColor int|string|array
     * @return $this
     */
    public function create($width, $height, $bgColor = 0)
    {
        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreatetruecolor($width, $height);
        $this->type = 'png';

        if (!is_int($bgColor)) $bgColor = $this->hex2color($bgColor);
        imagefill($this->image, 0, 0, $bgColor);

        return $this;
    }

    /**
     * 填充图像指定区域，默认全图, 如果指定区域是一个坐标，则使用fill模式
     * @param $color
     * @param null $range
     * @return $this
     */
    public function fill($color, $range = null)
    {
        if (is_string($color) || is_array($color)) {
            $color = $this->hex2color($color);
        }

        if ($range == null) {
            $range = [0, 0, $this->width, $this->height];
        }
        if (count($range) == 2) {
            imagefill($this->image, $range[0], $range[1], $color);
        } else {
            imagefilledrectangle($this->image, $range[0], $range[1], $range[2], $range[3], $color);
        }

        return $this;
    }

    /**
     * 从文件加载图片
     * @param $path
     * @param string $type 指定的类型，默认按扩展名自动识别
     * @return $this
     */
    public function loadFromFile($path, $type = '')
    {
        if (empty($type)) {
            $pathinfo = pathinfo($path);
            if (!empty($pathinfo['extension'])) {
                $ext = strtolower($pathinfo['extension']);
                if ($ext == 'jpg') $ext = 'jpeg';
                if (in_array($ext, ['jpeg', 'png', 'bmp', 'gif', 'gd2', 'gd', 'wbmp', 'webp', 'xbm', 'xpm'])) {
                    $type = $ext;
                }
            }
        }
        $func = 'imagecreatefrom' . $type;
        $this->type = $type;
        $this->imageInfo = getimagesize($path);
        $this->width = $this->imageInfo[0];
        $this->height = $this->imageInfo[1];
        $this->image = $func($path);
        return $this;
    }

    /**
     * 按比例缩放图片
     * @param $percent
     * @param $resampled bool 是否重新采样
     * @return $this
     */
    public function scale($percent, $resampled = true)
    {
        if ($percent != 100) {
            $newWidth = round($this->width * $percent * .01);
            $newHeight = round($this->height * $percent * .01);

            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            if ($resampled) {
                imagecopyresampled(
                    $newImage,
                    $this->image,
                    0,
                    0,
                    0,
                    0,
                    $newWidth,
                    $newHeight,
                    $this->width,
                    $this->height
                );
            } else {
                imagecopyresized(
                    $newImage,
                    $this->image,
                    0,
                    0,
                    0,
                    0,
                    $newWidth,
                    $newHeight,
                    $this->width,
                    $this->height
                );
            }

            imagedestroy($this->image);
            $this->image = $newImage;
            $this->width = $newWidth;
            $this->height = $newHeight;
        }

        return $this;
    }

    /**
     * 裁剪图片
     * @param $x
     * @param $y
     * @param $width
     * @param $height
     * @return $this
     */
    public function crop($x, $y, $width, $height)
    {

        $newImage = imagecrop($this->image, compact('x', 'y', 'width', 'height'));
        if ($newImage) {
            $this->image = $newImage;
            $this->width = $width;
            $this->height = $height;
        }

        return $this;
    }

    const SCALE_MODE_CONTAIN = 'contain';
    const SCALE_MODE_COVER = 'cover';
    const SCALE_MODE_FILL = 'fill';

    /**
     * 按宽高缩放图片
     * @param $width
     * @param $height
     * @param $mode string 裁剪模式, 如果原始比例与新比例不一致，会按照指定的模式进行裁剪
     * @param $bgColor string 包含模式需要填充底色
     * @return $this
     */
    public function resize($width, $height, $mode = self::SCALE_MODE_CONTAIN, $bgColor = '000000')
    {

        if ($width != $this->width || $height != $this->height) {
            $newImage = imagecreatetruecolor($width, $height);

            if ($mode == self::SCALE_MODE_CONTAIN) {
                $bgColor = $this->hex2color($bgColor);
                imagefill($newImage, 0, 0, $bgColor);

                $scale = min($width / $this->width, $height / $this->height);
                $newWidth = round($this->width * $scale);
                $newHeight = round($this->height * $scale);

                $left = round(($width - $newWidth) * .5);
                $top = round(($height - $newHeight) * .5);
                imagecopyresampled(
                    $newImage,
                    $this->image,
                    $left,
                    $top,
                    0,
                    0,
                    $this->width,
                    $this->height,
                    $newWidth,
                    $newHeight
                );
            } elseif ($mode == self::SCALE_MODE_COVER) {

                $scale = min($this->width / $width, $this->height / $height);
                $newWidth = round($width * $scale);
                $newHeight = round($height * $scale);

                $left = round(($this->width - $newWidth) * .5);
                $top = round(($this->height - $newHeight) * .5);
                imagecopyresampled(
                    $newImage,
                    $this->image,
                    0,
                    0,
                    $left,
                    $top,
                    $width,
                    $height,
                    $this->width - $left * 2,
                    $this->height - $top * 2
                );
            } else {
                imagecopyresampled(
                    $newImage,
                    $this->image,
                    0,
                    0,
                    0,
                    0,
                    $width,
                    $height,
                    $this->width,
                    $this->height
                );
            }

            imagedestroy($this->image);
            $this->image = $newImage;
            $this->width = $width;
            $this->height = $height;
        }

        return $this;
    }

    /**
     * 强制缩放
     * @param $width
     * @param $height
     * @param bool $resampled
     * @return $this
     */
    public function forceResize($width, $height, $resampled = true)
    {
        if ($width != $this->width || $height != $this->height) {
            $newImage = imagecreatetruecolor($width, $height);
            if ($resampled) {
                imagecopyresampled(
                    $newImage,
                    $this->image,
                    0,
                    0,
                    0,
                    0,
                    $width,
                    $height,
                    $this->width,
                    $this->height
                );
            } else {
                imagecopyresized(
                    $newImage,
                    $this->image,
                    0,
                    0,
                    0,
                    0,
                    $width,
                    $height,
                    $this->width,
                    $this->height
                );
            }

            imagedestroy($this->image);
            $this->image = $newImage;
            $this->width = $width;
            $this->height = $height;
        }

        return $this;
    }

    /**
     * 图片上帖另一张图
     * @param $src
     * @param $x
     * @param $y
     * @param $width
     * @param $height
     * @return $this
     */
    public function paste($src, $x, $y, $width = 0, $height = 0)
    {

        list($pwidth, $pheight, $type, $attr) = getimagesize($src);
        $image = new Image($src);
        if (!$width) {
            $width = $pwidth;
        }
        if (!$height) {
            $height = $pheight;
        }
        imagecopyresized($this->image, $image->getResource(), $x, $y, 0, 0, $width, $height, $pwidth, $pheight);
        $image = null;

        return $this;
    }

    /**
     * 图片上打印文本
     * @param $text
     * @param $size
     * @param $x
     * @param $y
     * @param $angle
     * @param $ttf
     * @param $color array|int|string [r, g, b]
     * @return $this
     */
    public function text($text, $size, $x, $y, $angle = 0, $ttf = '', $color = 0)
    {
        $font = app()->getRuntimePath() . $ttf;
        if (!is_file($font)) {
            exit('字体文件' . $font . '不存在');
        }

        if (!is_int($color)) {
            $color = $this->hex2color($color);
        }

        imagettftext($this->image, $size, $angle, $x, $y, $color, $font, $text);

        return $this;
    }

    /**
     * 保存图片
     * @param $file
     * @param int $quality
     * @return $this
     */
    public function save($file, $quality = 70)
    {
        switch ($this->type) {
            case 'gif':
                imagegif($this->image, $file);
                break;
            case 'wbmp':
                imagewbmp($this->image, $file);
                break;
            case 'png':
                imagepng($this->image, $file);
                break;
            default:
                imagejpeg($this->image, $file, $quality);
        }
        return $this;
    }

    /**
     * 输出图片
     * @param int $quality jpeg品质,其它格式无效
     * @return $this
     */
    public function output($quality = 70)
    {
        header('Content-Type: image/' . $this->type);
        $this->save(NULL, $quality);

        return $this;
    }



    /**
     * 由hex或rgb生成颜色
     * @param $hex
     * @param null $image
     * @return int
     */
    private function hex2color($hex, $image = null)
    {
        if ($image == null) $image = $this->image;

        if (is_array($hex)) {
            $rgb = $hex;
        } else {
            $rgb = $this->hex2rgb($hex);
        }
        $color = 0;
        if (count($rgb) == 4) {
            $color = imagecolorallocatealpha($image, $rgb[0], $rgb[1], $rgb[2], $rgb[3]);
        } elseif (count($rgb) == 3) {
            $color = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
        }

        return $color;
    }
    private function hex2rgb($hex)
    {
        $hex = trim($hex, '# ');
        $rgb[0] = hexdec(substr($hex, 0, 2));
        $rgb[1] = hexdec(substr($hex, 2, 2));
        $rgb[2] = hexdec(substr($hex, 4, 2));
        if (strlen($hex) > 7) {
            $rgb[3] = hexdec(substr($hex, 6, 2));
        }
        return $rgb;
    }

    public function __destruct()
    {
        if ($this->image) {
            imagedestroy($this->image);
        }
    }
}
