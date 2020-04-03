<?php

namespace app\index\controller\member;


use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use think\facade\Db;

/**
 * 代理控制器
 * Class AgentController
 * @package app\index\controller\member
 */
class AgentController extends BaseController
{
    public function initialize()
    {
        parent::initialize();
        if(!$this->user['is_agent']){
            $this->error('您还不是代理，请先下单购买');
        }
    }

    public function shares(){
        $shareurl=url('index/index/share',['agent'=>$this->user['agentcode']],true,true);

        $qrurl='/uploads/qrcode/'.($this->userid % 32).'/'.$this->user['agentcode'].'.png';
        if(!file_exists('.'.$qrurl)) {
            $dir='.'.dirname($qrurl);
            if(!file_exists($dir))@mkdir($dir,0777,true);
            $qrCode = new QrCode($shareurl);
            $qrCode->setSize(300);
            $qrCode->setWriterByName('png')
                ->setMargin(10)
                ->setEncoding('UTF-8')
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
                ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0])
                ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255])
                //->setLabel('Scan the code', 16, __DIR__.'/../assets/noto_sans.otf', LabelAlignment::CENTER)
                ->setLogoPath('./static/images/qrlogo.png')
                ->setLogoWidth(150)
                ->setValidateResult(false);
            $qrCode->writeFile('.' . $qrurl);
        }
        $this->assign('qrtime',filemtime('.'.$qrurl));
        $this->assign('qrurl',$qrurl);
        $this->assign('shareurl',$shareurl);

        return $this->fetch();
    }

    public function team($pid=0){
        $levels=getMemberLevels();
        $curLevel=$levels[$this->user['level_id']];
        if($pid==0){
            $pid=$this->userid;
        }elseif($pid!=$this->userid){
            $member=Db::name('Member')->find($pid);
            if(empty($member)){
                $this->error('会员不存在');
            }
            $paths=[$member];
            $maxlayer=$curLevel['commission_layer'];
            while($member['id']!=$this->userid){
                $member=Db::name('Member')->find($member['referer']);
                $paths[]=$member;
                if(count($paths)>$maxlayer){
                    $this->error('您只能查看'.$maxlayer.'层的会员');
                }
            }
            $paths=array_reverse($paths);
            $this->assign('paths',$paths);
        }
        $users=Db::name('Member')->where('referer',$pid)->paginate(10);
        $uids=array_column($users->all(),'id');
        $soncounts=[];
        if(!empty($uids)) {
            $sondata = Db::name('Member')->where('referer' ,'in', $uids)
                ->group('referer')->field('referer,COUNT(id) as count_member')->select();
            $soncounts=[];
            foreach ($sondata as $row){
                $soncounts[$row['referer']]=$row['count_member'];
            }
        }

        $this->assign('levels',$levels);
        $this->assign('users',$users);
        $this->assign('page',$users->render());
        $this->assign('soncounts',$soncounts);
        return $this->fetch();
    }
}