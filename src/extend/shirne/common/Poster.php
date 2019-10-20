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
        'width'=>0,
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
        $this->bg = imagecreatefrompng($bgpath);
        //imagealphablending()
        if(!$this->bg)return false;
        
        $fontpath=$this->config['font'];
        foreach ($this->config['data'] as $k=> &$set){
            //Log::record($k);
            if(isset($set['type']) && $set['type']=='background'){
                //利用重复帖一遍背景图的方式生成圆角，一定要注意顺序
                $vbg = imagecreatefrompng($bgpath);
                imagecopyresampled($this->bg, $vbg, 0, 0, 0, 0,imagesx($this->bg), imagesy($this->bg), imagesx($vbg), imagesy($vbg));
                imagedestroy($vbg);
                continue;
            }
            if(isset($data[$k])){
                //相对位置计算
                if(!empty($set['offset'])){
                    if(!is_array($set['offset'])){
                        $set['offset']=[
                            'field'=>$set['offset'],
                            'type'=>'lb'
                        ];
                    }
                    if(!isset($this->config['data'][$set['offset']['field']])){
                        Log::record('Poster: '.$k.' offset '.$set['offset']['field'].' not exists','error');
                        continue;
                    }
                    
                    $ofield=$set['offset']['field'];
                    $box = $this->config['data'][$ofield];
                    while(empty($box['width']) || empty($box['height'])){
                        if(isset($box['offset'])){
                            //代替上一个对象的位置
                            $set['x']=$box['x'];
                            $set['y']=$box['y'];

                            $ofield=$box['offset']['field'];
                            $box = $this->config['data'][$ofield];
                        }else{
                            Log::record('Poster: '.$k.' offset '.$ofield.' size error','error');
                            continue;
                        }
                    }
                    //Log::record('offset:'.json_encode($set));
                    //Log::record('offset target:'.json_encode($box));
                    switch (strtolower($set['offset']['type'])){
                        case 'lt':
                        case 'tl':
                            $set['x'] += $box['x'];
                            $set['y'] += $box['y'];
                            break;
                        case 'rt':
                        case 'tr':
                            $set['x'] += $box['x']+$box['width'];
                            $set['y'] += $box['y'];
                            break;
                        case 'lb':
                        case 'bl':
                            $set['x'] += $box['x'];
                            $set['y'] += $box['y']+$box['height'];
                            break;
                        case 'rb':
                        case 'br':
                            $set['x'] += $box['x']+$box['width'];
                            $set['y'] += $box['y']+$box['height'];
                            break;
                    }
                    //Log::record('offset result:'.json_encode($set));
                }

                if(isset($set['type']) && $set['type'] == 'image') {
                    $set = array_merge($this->defaultImageSet, $set);
                    if ($set['width'] <= 0 || $set['height'] <= 0) {
                        Log::record('Poster: '.$k.' size error','error');
                        continue;
                    }
                    if(strpos($data[$k],'http://')===false && strpos($data[$k],'https://')===false) {
                        
                        if (!file_exists($data[$k])){
                            Log::record('Poster: file '.$data[$k].' not exists','error');
                            continue;
                        }
                    }
                    //粘贴图片
                    $this->paintImage($data[$k],$set);
                }else{
                    $set = array_merge($this->defaultTextSet,$set);
                    
                    //识别字体
                    if(file_exists($fontpath.$set['font'])) {
                        $set['font'] = $fontpath . $set['font'];
                    }elseif(in_array(strtolower($set['font']),['black','bold','medium','regular','demilight','light','thin'])){
                        $fontname = ucfirst(strtolower($set['font']));
                        if($fontname == 'Demilight')$fontname = 'DemiLight';
                        $set['font'] = $fontpath.$fontname.'.otf';
                    }else{
                        $set['font'] = $fontpath.$this->defaultTextSet['font'];
                    }
                    $set['font'] = realpath($set['font']);
                    
                    $text = $this->filter_emoji($data[$k]);
                    if(empty($text) && empty($set['force'])){
                        //文本为空不打印
                        continue;
                    }
                    //文本限宽换行
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
                    //文本为空，说明是需要强制保留位置的
                    if(empty($text)){
                        $set['height']=$set['size'];
                        $set['width']=$set['size'];
                        continue;
                    }
                    
                    $color = $this->transColor($set['color'],$this->bg);

                    //限制宽度
                    if(!empty($set['width'])){
                        $text = $this->autoWrap($text, $set['size'],$set['width']);
                    }
                    //是否多行打印
                    if(mb_strpos($text,"\n")!==false){
                        $textes=explode("\n",$text);
                        if(!empty($set['maxline']) && count($textes)>$set['maxline']){
                            $textes = array_slice($textes,0, $set['maxline']);
                            $lasttext=$textes[$set['maxline']-1];
                            $textes[$set['maxline']-1]=mb_substr($lasttext,0,mb_strlen($lasttext)-2).'...';
                        }
                        $set['height']=0;
                        if(!isset($set['linespace']))$set['linespace']=5;
                        $start_y=$set['y'];
                        $start_x=$set['x'];
                        $lineSet=array_intersect_key($set,array_flip(['x','y','angle','align','size','font','width']));
                        foreach($textes as $txt){
                            $newset = $this->paintText($txt,$lineSet,$color);
                            if($set['height']<=0){
                                $set['x']=$newset['lt_x'];
                                $set['y']=$newset['lt_y'];
                            }else{
                                $set['height']+= $set['linespace'];
                            }
                            
                            $set['width']=max($set['width'],$newset['width']);
                            $set['height']+= $newset['height'];
                            $lineSet['x'] = $start_x;
                            $lineSet['y'] = $start_y+$set['height']+$set['linespace'];
                        }
                    }else{
                        $newset = $this->paintText($text,$set,$color);
                        $set['x']=$newset['lt_x'];
                        $set['y']=$newset['lt_y'];
                        $set['width'] = max($set['width'],$newset['width']);
                        $set['height'] = $newset['height'];
                    }
                    
                }
            }
        }
        
        return true;
    }

    private function paintImage($image, $set){
        $sub = @imagecreatefromstring(file_get_contents($image));
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

            //Log::record('帖图:'.$image.'-'.json_encode([$set['x'],$set['y'],$sx,$sy,$set['width'],$set['height'],$w,$h],JSON_UNESCAPED_UNICODE),'info');
            imagecopyresampled($this->bg, $sub, $set['x'], $set['y'], $sx, $sy, $set['width'], $set['height'], $w, $h);
            imagedestroy($sub);
        }
    }

    private function paintText($text, $set, $color){
        $textbox = imagettfbbox($set['size'],$set['angle'],$set['font'],$text);
        //Log::record('文字盒:'.$text.'-'.json_encode($textbox,JSON_UNESCAPED_UNICODE),'info');
        //左上角转换为左下角并计算偏移
        $set['lt_x'] = $set['x'] -= $textbox[0];
        $set['lt_y'] = $set['y'] - $textbox[1];
        $set['y'] -= $textbox[5];
        switch (strtolower($set['align'])){
            case 'right':
            case 'rt':
            case 'tr':
                $set['x'] -= $textbox[2]-$textbox[0];
                break;
            case 'rb':
            case 'br':
                $set['x'] -= $textbox[2]-$textbox[0];
                $set['y'] -= $textbox[1]-$textbox[5];
                break;
            case 'lb':
            case 'bl':
                $set['y'] -= $textbox[1]-$textbox[5];
                break;
            case 'ct':
                $set['x'] -= ($textbox[2]-$textbox[0])*.5;
                break;
            case 'cb':
                $set['x'] -= ($textbox[2]-$textbox[0])*.5;
                $set['y'] -= $textbox[1]-$textbox[5];
                break;
            case 'center':
                $set['x'] -= ($textbox[2]-$textbox[0])*.5;
                $set['y'] -= ($textbox[1]-$textbox[5])*.5;
                break;
            case 'left':
            case 'lt':
            case 'tl':
            default:
                break;
        }

        if(!isset($set['width']) || $set['width']<$textbox[2]-$textbox[0]){
            $set['width']=$textbox[2]-$textbox[0];
        }
        $set['height']=$textbox[1]-$textbox[5];
        
        //Log::record('打字:'.$text.'-'.json_encode([$set['size'],$set['angle'],$set['x'],$set['y'],$color,$set['font']], JSON_UNESCAPED_UNICODE),'info');
        imagettftext($this->bg,$set['size'],$set['angle'],$set['x'],$set['y'],$color,$set['font'],$text);
        return $set;
    }
    
    public function save($path,$type='jpeg'){
        if(!$this->bg)return false;
        switch(strtolower($type)){
            case 'webp':
                imagewebp($this->bg,$path);
                break;
            case 'gif':
                imagegif($this->bg,$path);
                break;
            case 'png':
                imagepng($this->bg,$path);
                break;
            case 'jpg':
            case 'jpeg':
            default:
                imagejpeg($this->bg,$path,80);
        }
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