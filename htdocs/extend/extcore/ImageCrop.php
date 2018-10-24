<?php


namespace extcore;


class ImageCrop
{

    protected $file;
    protected $options;
    public function __construct($file='',$options=[])
    {
        $this->file=trim($file);
        $this->options=$options;
    }

    /**
     * 根据参数裁剪图片
     * @param null|string $savepath
     * @param $opts array
     * @return \think\Response
     */
    public function crop($savepath=null,$opts=[]){
        $opts=array_merge($opts, $this->options);
        $img = $this->file;
        $imgWidth = (int)$opts['w'];
        $imgHeight = (int)$opts['h'];
        $imgQuality = (int)$opts['q'];
        $imgMode = strtolower(trim($opts['m']));

        if (empty($img)){
            exit();
        }
        if($imgWidth<1 && $imgHeight<1){
            $imgWidth = config('upload.default_size');
        }
        if($imgQuality<1){
            $imgQuality = config('upload.default_quality');
        }

        $imgData=$this->getImgData($img);

        if($imgData!==false && !empty($imgData)) {
            $imageinfo = getimagesizefromstring($imgData);
            $image = imagecreatefromstring($imgData);

            $photoWidth = $imageinfo[0];
            $photoHeight = $imageinfo[1];

            if ($photoWidth > 0 And $photoHeight > 0) {
                if ($photoWidth > $imgWidth Or $photoHeight > $imgHeight) {
                    $photoScale = $photoWidth / $photoHeight;
                    if ($imgWidth > 0 And $imgHeight > 0) {
                        $imgScale = $imgWidth / $imgHeight;
                    } else {
                        $imgScale = $photoScale;
                    }
                    $clipLeft = 0;
                    $clipTop = 0;
                    switch ($imgMode) {
                        case "o":
                        case "1":
                        case "outer":
                            if ($photoScale == $imgScale) {
                                if ($imgWidth > 0) {
                                    $tempWidth = $photoWidth;
                                    $tempHeight = $tempWidth / $imgScale;
                                } else {
                                    $tempHeight = $photoHeight;
                                    $tempWidth = $tempHeight * $imgScale;
                                }
                            } elseif ($photoScale > $imgScale) {
                                $tempHeight = $photoHeight;
                                $tempWidth = $tempHeight * $imgScale;
                                $clipLeft = ($photoWidth - $tempWidth) * .5;
                            } else {
                                $tempWidth = $photoWidth;
                                $tempHeight = $tempWidth / $imgScale;
                                $clipTop = ($photoHeight - $tempHeight) * .5;
                            }
                            break;
                        default:
                            if ($photoScale == $imgScale) {
                                if ($imgWidth > 0) {
                                    $tempWidth = $imgWidth;
                                    $tempHeight = $imgWidth / $imgScale;
                                } else {
                                    $tempHeight = $imgHeight;
                                    $tempWidth = $imgHeight * $imgScale;
                                }
                            } elseif ($photoScale > $imgScale) {
                                $tempWidth = $imgWidth;
                                $tempHeight = $imgWidth / $photoScale;
                            } else {
                                $tempHeight = $imgHeight;
                                $tempWidth = $imgHeight * $photoScale;
                            }
                    }

                    if ($clipLeft > 0 Or $clipTop > 0) {
                        $newimg = $this->createImage($imgWidth, $imgHeight);
                        imagecopyresampled($newimg, $image, 0, 0, $clipLeft, $clipTop, $imgWidth, $imgHeight, $tempWidth, $tempHeight);
                        //imagecopyresized($newimg, $image, 0, 0, $clipLeft, $clipTop, $imgWidth, $imgHeight, $tempWidth, $tempHeight);
                    } else {
                        $newimg = $this->createImage($tempWidth, $tempHeight);
                        imagecopyresampled($newimg, $image, 0, 0, 0, 0, $tempWidth, $tempHeight, $photoWidth, $photoHeight);
                        //imagecopyresized($newimg, $image, 0, 0, 0, 0, $tempWidth, $tempHeight, $photoWidth, $photoHeight);
                    }
                    imagedestroy($image);

                    return $this->output($newimg, $imageinfo['mime'], $savepath, $imgQuality);
                } else {
                    return $this->output($image, $imageinfo['mime'], $savepath, $imgQuality);
                }
            }
        }
        return redirect(ltrim(config('upload.default_img'),'.'));
    }

    private function createImage($width,$height){
        $newimg=imagecreatetruecolor($width, $height);
        imagesavealpha($newimg,true);
        $trans_colour = imagecolorallocatealpha($newimg, 0, 0, 0, 127);
        imagefill($newimg, 0, 0, $trans_colour);
        return $newimg;
    }

    /**
     * 输出图片
     * @param $image
     * @param $mime
     * @param $savepath
     * @param $imgQuality
     * @return \think\Response
     */
    private function output($image,$mime='image/jpeg',$savepath=null,$imgQuality=80){
        ob_start();
        switch (strtolower($mime)){
            case 'image/png':
                imagepng($image,$savepath);
                break;
            case 'image/gif':
                imagegif($image,$savepath);
                break;
            default:
                imagejpeg($image,$savepath,$imgQuality);
        }
        $content = ob_get_clean();
        imagedestroy($image);
        return response($content, 200, ['Content-Length' => strlen($content)])->contentType($mime);
    }


    /**
     * 获取文件内容
     * @param $img
     * @return bool|string
     */
    private function getImgData($img){
        if(strripos($img, 'http://')!==FALSE OR strripos($img,'https://') !==FALSE) {	//站外图片
            $data=file_get_contents($img);
        }else{	//站内图片
            $file=DOC_ROOT.'/'.$img;
            if(is_file($file)) {
                $data = file_get_contents($file);
            }else{
                return false;
            }
        }
        return $data;
    }
}