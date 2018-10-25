<?php
/**
 * Created by IntelliJ IDEA.
 * User: shirne
 * Date: 2018/4/20
 * Time: 23:50
 */

namespace app\task\controller;


use app\common\command\Install;
use think\Console;
use think\console\Input;
use think\console\Output;
use think\Controller;
use think\Response;

class UtilController extends Controller
{
    public function cropimage($img){
        return crop_image($img,$_GET);
    }

    public function cacheimage($img){
        $paths=explode('.',$img);
        if(count($paths)==3) {
            preg_match_all('/(w|h|q|m)(\d+)(?:_|$)/', $paths[1], $matches);
            $args = [];
            foreach ($matches[1] as $idx=>$key){
                $args[$key]=$matches[2][$idx];
            }
            $response = crop_image($paths[0].'.'.$paths[2], $args);
            if($response->getCode()==200) {
                file_put_contents(DOC_ROOT . '/' . $img, $response->getData());
            }
            return $response;
        }else{
            return redirect(ltrim(config('upload.default_img'),'.'));
        }
    }

    public function daily()
    {
        # code...
    }

    public function install($sql='',$mode=''){

        $console=Console::init(false);
        $output=new Output('buffer');
        $args=['install'];
        if(!empty($sql)){
            $args[]='--sql';
            $args[]=$sql;
        }
        if(!empty($mode)){
            $args[]='--mode';
            $args[]=$mode;
        }
        $input=new Input($args);

        $console->doRun($input, $output);
        return $output->fetch();
    }

}