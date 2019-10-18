<?php


namespace shirne\common;
use think\facade\Log;

class Poster
{
    /**
     * Example data:
     * 'qrcode'=>[
            'type'=>'image',
            'x'=>0,
            'y'=>0,
            'width'=>710,
            'height'=>406
        ],
        'avatar'=>[
            'type'=>'image',
            'x'=>0,
            'y'=>0,
            'width'=>710,
            'height'=>406,
            'size'=>'cover'
        ],
        'nickname'=>[
            'align'=>'center',
            'x'=>301,
            'y'=>708,
            'size'=>55,
            'font'=>'Bold.otf'
        ]
     * @var array
     */
    protected $defaultConfig=[
        'background'=>'',
        'font'=>'./static/fonts/NotoSansCJKsc/',
        'data'=>[
        
        ]
    ];
    protected $config=[];
    protected $defaultTextSet=[
        'font'=>'Regular.otf',
        'size'=>14,
        'angle'=>0,
        'align'=>'left',
        'x'=>0,
        'y'=>0,
        'color'=>0
        ];
    protected $defaultImageSet=[
        'x'=>0,
        'y'=>0,
        'width'=>0,
        'height'=>0,
        'type'=>''
        ];
    
    protected $bg;
    public function __construct($config=[])
    {
        $this->config=array_merge($this->defaultConfig,$config);
    }
    
    public function generate($data){
        if(!$this->config['data'])return false;
        $bgpath = $this->config['background'];
        if(!file_exists($bgpath))return false;
        $bg = imagecreatefrompng($bgpath);
        //imagealphablending()
        if(!$bg)return false;
        
        $sizes=[];
        $fontpath=$this->config['font'];
        foreach ($this->config['data'] as $k=>$set){
            if(isset($data[$k])){
                if($set['type'] == 'image') {
                    $set = array_merge($this->defaultImageSet, $set);
                    if ($set['width'] <= 0 || $set['height'] <= 0) continue;
                    if(strpos($data[$k],'http://')===false && strpos($data[$k],'https://')===false) {
                        //$data[$k] = DOC_ROOT . ltrim($data[$k],'.');
                        if (!file_exists($data[$k])) continue;
                    }
                    //粘贴图片
                    $sub = @imagecreatefromstring(file_get_contents($data[$k]));
                    if ($sub) {
                        $w = imagesx($sub);
                        $h = imagesy($sub);
                        $sx = 0;
                        $sy = 0;
                        if ($w / $h != $set['width'] / $set['height']) {
                            if ($set['size'] == 'cover') {
                                list($ox, $oy) = $this->computSize($w, $h, $set['width'], $set['height'], 0);
                
                                $sx -= $ox;
                                $w += $ox * 2;
                
                                $sy -= $oy;
                                $h += $oy * 2;
                            } elseif ($set['size'] == 'contain') {
                                list($ox, $oy) = $this->computSize($w, $h, $set['width'], $set['height'], 1);
                
                                $set['x'] += $ox;
                                $set['width'] -= $ox * 2;
                
                                $set['y'] += $oy;
                                $set['height'] -= $oy * 2;
                            }
                        }
        
                        Log::record('帖图:'.$data[$k].'-'.json_encode([$set['x'],$set['y'],$sx,$sy,$set['width'],$set['height'],$w,$h],JSON_UNESCAPED_UNICODE),'info');
                        imagecopyresampled($bg, $sub, $set['x'], $set['y'], $sx, $sy, $set['width'], $set['height'], $w, $h);
                        imagedestroy($sub);
                    }
                }elseif($set['type']=='background'){
                    //利用重复帖一遍背景图的方式生成圆角，一定要注意顺序
                    $vbg = imagecreatefrompng($bgpath);
                    imagecopyresampled($bg, $vbg, 0, 0, 0, 0,imagesx($bg), imagesy($bg), imagesx($vbg), imagesy($vbg));
                    imagedestroy($vbg);
                }else{
                    $set = array_merge($this->defaultTextSet,$set);
                    
                    //写文字
                    if(file_exists($fontpath.$set['font'])) {
                        $set['font'] = $fontpath . $set['font'];
                    }elseif(in_array(strtolower($set['font']),['black','bold','medium','regular','demilight','light','thin'])){
                        $fontname = ucfirst(strtolower($set['font']));
                        if($fontname == 'Demilight')$fontname = 'DemiLight';
                        $set['font'] = $fontpath.$fontname.'.otf';
                    }else{
                        $set['font'] = $fontpath.$this->defaultTextSet['font'];
                    }
                    
                    if(!empty($set['offset']) && isset($sizes[$set['offset']['field']])){
                        //logging_simple('文字相对坐标:'.$[$k].'-'.logging_implode([$set['x'],$set['y']]),'info');
                        $box = $sizes[$set['offset']['field']];
                        //logging_simple('相对对象:'.$set['offset']['field'].'-'.logging_implode($box),'info');
                        switch (strtolower($set['offset']['type'])){
                            case 'lt':
                            case 'tl':
                                $set['x'] += $box[0];
                                $set['y'] += $box[1]-$box[3];
                                break;
                            case 'rt':
                            case 'tr':
                                $set['x'] += $box[0]+$box[2];
                                $set['y'] += $box[1]-$box[3];
                                break;
                            case 'lb':
                            case 'bl':
                                $set['x'] += $box[0];
                                $set['y'] += $box[1];
                                break;
                            case 'rb':
                            case 'br':
                                $set['x'] += $box[0]+$box[2];
                                $set['y'] += $box[1];
                                break;
                        }
                        //logging_simple('文字绝对坐标:'.$card[$k].'-'.logging_implode([$set['x'],$set['y']]),'info');
                    }
                    
                    $text = $this->filter_emoji($data[$k]);
                    if(isset($set['text'])){
                        $text = preg_replace_callback('/\{([\w\d]+)\}/',function ($matches)use($data, $k){
                            $key = $matches[1];
                            if($key=='value' && !isset($data['value'])){
                                $key = $k;
                            }
                            return isset($data[$key])?$data[$key]:'';
                        },$set['text']);
                    }
                    if(isset($set['prefix'])){
                        if(is_array($set['prefix'])){
                            if($this->parseIf($data,$set['prefix']['if'])){
                                $text = $set['prefix']['text'] . $text;
                            }
                        }else {
                            $text = $set['prefix'] . $text;
                        }
                    }
                    if(isset($set['sufix'])){
                        if(is_array($set['sufix'])){
                            if($this->parseIf($data,$set['sufix']['if'])){
                                $text .= $set['sufix']['text'];
                            }
                            
                        }else {
                            $text .= $set['sufix'];
                        }
                    }


                    //限制宽度
                    if(!empty($set['width'])){
                        $text = $this->autoWrap($text, $set['size'],$set['width']);
                        Log::record($text);
                    }
                    
                    $textbox = imagettfbbox($set['size'],$set['angle'],$set['font'],$text);
                    Log::record('文字盒:'.$text.'-'.json_encode($textbox,JSON_UNESCAPED_UNICODE),'info');
                    //左下角偏移
                    $set['x'] -= $textbox[0];
                    $set['y'] -= $textbox[1];
                    switch (strtolower($set['align'])){
                        case 'left':
                            $set['y'] += $textbox[1]-$textbox[5];
                            break;
                        case 'right':
                            $set['x'] -= $textbox[2]-$textbox[0];
                            $set['y'] += $textbox[1]-$textbox[5];
                            break;
                        case 'center':
                            $set['x'] -= ($textbox[2]-$textbox[0])*.5;
                            $set['y'] += ($textbox[1]-$textbox[5])*.5;
                            break;
                        default:
                            break;
                    }
                    $sizes[$k]=[$set['x'],$set['y'],$textbox[2]-$textbox[0],$textbox[1]-$textbox[5]];
                    $color = $this->transColor($set['color'],$bg);
                    
                    Log::record('打字:'.$text.'-'.json_encode([$set['size'],$set['angle'],$set['x'],$set['y'],$color,$set['font']], JSON_UNESCAPED_UNICODE),'info');
                    imagettftext($bg,$set['size'],$set['angle'],$set['x'],$set['y'],$color,$set['font'],$text);
                    
                    if($set['badge'] && is_array($set['badge']['images'])){
                        $th = $textbox[1]-$textbox[5];
                        $ypos = $set['y'] - $th;
                        $space = $set['badge']['space']?:10;
                        $start = $set['x'] + $textbox[2]-$textbox[0] + $space;
                        if(!empty($set['badge']['offset-x'])){
                            $start += $set['badge']['offset-x'];
                        }
                        if(!empty($set['badge']['offset-y'])){
                            $ypos += $set['badge']['offset-y'];
                        }
                        foreach ($set['badge']['images'] as $image){
                            if(!$image['image'])continue;
                            $image['image'] = local_media($image['image']);
                            if(!file_exists($image['image']))continue;
                            
                            if(!$image['if'] || $data[$image['if']]){
                                $sub = imagecreatefromstring(file_get_contents($image['image']));
                                if($sub) {
                                    $bw = imagesx($sub);
                                    $bh = imagesy($sub);
                                    $width = isset($set['badge']['width'])?$set['badge']['width']:$bw;
                                    $height = isset($set['badge']['height'])?$set['badge']['height']:$bh;
                                    $y = $ypos + ($th-$height)*.5;
                                    
                                    //logging_simple('徽标:'.$image['image'].'-'.logging_implode([$start, $y, 0, 0, $width, $height, $bw, $bh]),'info');
                                    imagecopyresampled($bg, $sub, $start, $y, 0, 0, $width, $height, $bw, $bh);
                                    imagedestroy($sub);
                                    $start += $width + $space;
                                }else{
                                    //logging_simple('徽标读取失败:'.$image['image'],'info');
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->bg=$bg;
        return true;
    }
    
    public function save($path,$type='jpeg'){
        if(!$this->bg)return false;
        imagejpeg($this->bg,$path,80);
        return true;
    }

    protected function autoWrap($text, $textSize, $width){
        if($width>0 && !empty($text)){
            $rowwidth=0;
            $strarr=preg_split('//u',$text);
            $text='';
            for($i=0;$i<count($strarr);$i++){
                if(empty($strarr[$i]))continue;
                $code = mb_ord($strarr[$i]);
                if($code>255){
                    $cwidth = $textSize;
                }else{
                    $cwidth = $textSize*.5;
                }
                if($rowwidth + $cwidth > $width){
                    $rowwidth = $cwidth;
                    $text .= "\n";
                }else{
                    $rowwidth += $cwidth;
                }
                
                $text .= $strarr[$i];
            }
        }
        return $text;
    }
    
    protected function filter_emoji($str){
        $str = preg_replace_callback(    //执行一个正则表达式搜索并且使用一个回调进行替换
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);
        
        return $str;
    }
    
    protected function parseIf($card, $if){
        if(!empty($if)){
            $condition = preg_replace_callback('/\b([a-zA-Z][a-zA-Z\-0-9_]*)\b/',function ($matches)use($card){
                $field = $matches[1];
                if(isset($card[$field])){
                    return '$card["'.$field.'"]';
                }
                return $field;
            },$if);
            
            try{
                //error_reporting(0);
                return @eval('return '.$condition.';');
            }catch(\Exception $e){
                return false;
            }
        }
        return true;
    }
    
    protected function transColor($color, $bg){
        $r=$g=$b=0;
        if(is_int($color)){
            $r = $color >> 16 & 0xff;
            $g = $color >> 8 & 0xff;
            $b = $color & 0xff;
        }else{
            $color = ltrim($color,'#');
            $a = -1;
            if(strlen($color) == 3){
                $r = hexdec($color{0}.$color{0});
                $g = hexdec($color{1}.$color{1});
                $b = hexdec($color{2}.$color{2});
            }elseif(strlen($color)==6){
                $r = hexdec($color{0}.$color{1});
                $g = hexdec($color{2}.$color{3});
                $b = hexdec($color{4}.$color{5});
            }elseif(strlen($color)==8){
                $r = hexdec($color{0}.$color{1});
                $g = hexdec($color{2}.$color{3});
                $b = hexdec($color{4}.$color{5});
                $a = hexdec($color{6}.$color{7});
            }
            if($a>-1){
                return imagecolorallocatealpha($bg,$r, $g, $b, $a);
            }
        }
        return imagecolorallocate($bg,$r, $g, $b);
    }
    protected function computSize($w, $h, $sw, $sh, $i = 1){
        $r = $w/$h;
        $sr = $sw/$sh;
        if($i) {
            if ($r >= $sr) {
                $resize = $w/$sw;
                $nw = $sw;
                $nh = $h /$resize;
            }else{
                $resize = $h/$sh;
                $nh = $sh;
                $nw = $w /$resize;
            }
            $x = ($sw - $nw)*.5;
            $y = ($sh - $nh)*.5;
        }else{
            if ($r >= $sr) {
                $resize = $sh/$h;
                $nh = $sh;
                $nw = $w *$resize;
            }else{
                $resize = $sw/$w;
                $nw = $sw;
                $nh = $h *$resize;
            }
            $x = ($sw - $nw)*.5/$resize;
            $y = ($sh - $nh)*.5/$resize;
        }
        
        return [$x, $y];
    }
    
    protected function imageBorder($srcimg, $border, $bordercolor='ffffff',$radius=0){
        if(!$srcimg)return $srcimg;
        $w = imagesx($srcimg);
        $h = imagesy($srcimg);
        $img = imagecreatetruecolor($w+$border*2,$h+$border*2);
        imagesavealpha($img,true);
        //imageantialias($img,true);
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $bg);
        imagecopyresampled($img, $srcimg, $border, $border, 0,0,
            $w,$h,$w,$h);
        imagedestroy($srcimg);
        
        $bcolor = $this->transColor($bordercolor,$img);
        
        //画四条边
        imagefilledpolygon($img,[
            $radius,0,
            $radius*.5+$border,$border-1,
            $w+$border-$radius*.5,$border-1,
            $w+$border*2-$radius,0
        ],4,$bcolor);
        
        imagefilledpolygon($img,[
            $w+$border*2,$radius,
            $w+$border,$border+$radius*.5,
            $w+$border,$h+$border-$radius*.5,
            $w+$border*2,$h+$border*2-$radius
        ],4,$bcolor);
        
        imagefilledpolygon($img,[
            $w+$border*2-$radius,$h+$border*2,
            $w+$border-$radius*.5,$h+$border,
            $border+$radius*.5,$h+$border,
            $radius,$h+$border*2
        ],4,$bcolor);
        
        imagefilledpolygon($img,[
            0,$h+$border*2-$radius,
            $border-1,$h+$border-$radius*.5,
            $border-1,$border+$radius*.5,
            0,$radius
        ],4,$bcolor);
        
        if($radius > 0){
            imagearc($img,$radius,$radius,$radius*2,$radius*2,180,270,$bcolor);
            imageline($img,$radius,0,$radius*.5+$border,$border-1,$bcolor);
            imagearc($img,$border+$radius*.5,$border+$radius*.5,$radius+1,$radius+1,180,270,$bcolor);
            imageline($img,$border,$border+$radius*.5,0,$radius,$bcolor);
            imagefilltoborder($img,$radius,$radius,$bcolor,$bcolor);
            
            imagearc($img,$w+$border*2-$radius,$radius,$radius*2,$radius*2,270,0,$bcolor);
            imageline($img,$w+$border*2,$radius,$w+$border,$border+$radius*.5,$bcolor);
            imagearc($img,$w+$border-$radius*.5,$border+$radius*.5,$radius+1,$radius+1,270,0,$bcolor);
            imageline($img,$w+$border-$radius*.5,$border-1,$w+$border*2-$radius,0,$bcolor);
            imagefilltoborder($img,$w+$border*2-$radius,$radius,$bcolor,$bcolor);
            
            imagearc($img,$w+$border*2-$radius,$h+$border*2-$radius,$radius*2,$radius*2,0,90,$bcolor);
            imageline($img,$w+$border*2-$radius,$h+$border*2,$w+$border-$radius*.5,$h+$border,$bcolor);
            imagearc($img,$w+$border-$radius*.5,$h+$border-$radius*.5,$radius+1,$radius+1,0,90,$bcolor);
            imageline($img,$w+$border,$h+$border-$radius*.5,$w+$border*2,$h+$border*2-$radius,$bcolor);
            imagefilltoborder($img,$w+$border*2-$radius,$h+$border*2-$radius,$bcolor,$bcolor);
            
            imagearc($img,$radius,$h+$border*2-$radius,$radius*2,$radius*2,90,180,$bcolor);
            imageline($img,0,$h+$border*2-$radius,$border-1,$h+$border-$radius*.5,$bcolor);
            imagearc($img,$border+$radius*.5,$h+$border-$radius*.5,$radius+1,$radius+1,90,180,$bcolor);
            imageline($img,$border+$radius*.5,$h+$border,$radius,$h+$border*2,$bcolor);
            imagefilltoborder($img,$radius,$h+$border*2-$radius,$bcolor,$bcolor);
        }
        return $img;
    }
    
    protected function parseLogo($qrimg, $logo){
        //logging_simple([$qrimg,$logo]);
        if(file_exists($qrimg) && file_exists($logo)) {
            $QR = imagecreatefromstring(file_get_contents($qrimg));
            $logo = imagecreatefromstring(file_get_contents($logo));
            
            if($QR && $logo) {
                $logo_width = imagesx($logo);
                $logo = $this->imageBorder($logo,$logo_width*.08,'ffffff',$logo_width*.08);
                
                $QR_width = imagesx($QR);//二维码图片宽度
                $QR_height = imagesy($QR);//二维码图片高度
                $logo_width = imagesx($logo);//logo图片宽度
                $logo_height = imagesy($logo);//logo图片高度
                
                if(!imageistruecolor($QR)){
                    $newqr=imagecreatetruecolor($QR_width,$QR_height);
                    imagecopy($newqr, $QR, 0,0,0,0,$QR_width,$QR_height);
                    imagedestroy($QR);
                    $QR=$newqr;
                }
                
                $logo_qr_width = intval($QR_width / 4.6);
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $from_width = ($QR_width - $logo_qr_width) * .5;
                
                //logging_simple([$QR_width,$QR_height,$logo_width,$logo_height]);
                
                /*$border=4;
                imagefilledrectangle($QR,
                    $from_width-$border,$from_width-$border,
                    $from_width+$logo_qr_width+$border,$from_width+$logo_qr_width+$border,
                    imagecolorallocate($QR,255,255,255)
                );*/
                
                imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                    $logo_qr_height, $logo_width, $logo_height);
                imagepng($QR, $qrimg);
                imagedestroy($logo);
                imagedestroy($QR);
            }
        }
    }
    
    public function __destruct()
    {
        if($this->bg && is_resource($this->bg)){
            imagedestroy($this->bg);
        }
    }
}