<?php

namespace shirne\common;

/**
 * Class Image todo 功能开发中
 * @package shirne\common
 * @require gd2
 */
class Image
{
    /**
     * @var resource
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

    public function __construct()
    {
    }

    public function getResource()
    {
        return $this->image;
    }

    /**
     * 创建空白图片
     * @param $width int
     * @param $height int
     * @param $bg array
     * @return $this
     */
    public function create($width, $height, $bg)
    {
        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreate($width, $height);
        $this->type = 'png';
        if(is_array($bg)){
            $color=null;
            if(count($bg)==4){
                $color = imagecolorallocatealpha($this->image,$bg[0],$bg[1],$bg[2],$bg[3]);
            }elseif(count($bg)==3){
                $color = imagecolorallocate($this->image,$bg[0],$bg[1],$bg[2]);
            }
            if($color){
                imagefill($this->image, 0, 0, $color);
            }
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

    public function getImage()
    {
        return $this->image;
    }

    /**
     * 设定图像类型,用于输出
     * @param $type
     * @return $this
     */
    public function type($type)
    {
        $this->type=$type;
        return $this;
    }

    /**
     * 按比例缩放图片
     * @param $percent
     * @return $this
     */
    public function scale($percent)
    {
        return $this;
    }

    /**
     * 裁剪图片
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * @return $this
     */
    public function crop($x1, $y1, $x2, $y2)
    {
        return $this;
    }

    /**
     * 按宽高缩放图片
     * @param $width
     * @param $height
     * @param $keepRatio bool 是否保持比例
     * @return $this
     */
    public function resize($width, $height, $keepRatio = true)
    {
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
    public function paste($src, $x, $y, $width, $height)
    {
        return $this;
    }

    /**
     * 图片上打印文本
     * @param $text
     * @param $x
     * @param $y
     * @param $ttf
     * @param $color array [r, g, b, a]
     * @param $size
     * @return $this
     */
    public function text($text, $x, $y, $ttf, $color, $size)
    {
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
        switch ($this->type){
            case 'gif':
                imagegif($this->image,$file);
                break;
            case 'wbmp':
                imagewbmp($this->image,$file);
                break;
            case 'png':
                imagepng($this->image,$file);
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
    public function output($quality=70)
    {
        header('Content-Type: image/'.$this->type);
        $this->save(NULL,$quality);

        return $this;
    }

    public function __destruct()
    {
        if($this->image){
            imagedestroy($this->image);
        }
    }
}