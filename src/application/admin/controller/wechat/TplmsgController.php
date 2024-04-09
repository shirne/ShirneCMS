<?php


namespace app\admin\controller\wechat;


use app\common\model\WechatTemplateMessageModel;

class TplmsgController extends WechatBaseController
{
    public function index()
    {
        $tpls = WechatTemplateMessageModel::getTpls($this->wid);
        $reserveTpls = $this->reserveTpls();

        if ($this->request->isPost()) {
            $datas = $this->request->post('tpls');
            foreach ($reserveTpls as $key => $msg) {
                if (isset($tpls[$key])) {
                    WechatTemplateMessageModel::update($datas[$key], ['wechat_id' => $this->wid, 'type' => $key]);
                } elseif (!empty($datas[$key]['template_id'])) {
                    $datas[$key]['wechat_id'] = $this->wid;
                    $datas[$key]['type'] = $key;
                    WechatTemplateMessageModel::create($datas[$key]);
                }
            }
            $this->success('保存成功');
        }

        $this->assign('tpls', $tpls);
        $this->assign('msgs', $reserveTpls);
        return $this->fetch();
    }

    private function reserveTpls()
    {
        if ($this->currentWechat['account_type'] == 'miniprogram') {
            $reserveTpls = WechatTemplateMessageModel::miniprogramTpls();
        } elseif ($this->currentWechat['account_type'] == 'service') {
            $reserveTpls = WechatTemplateMessageModel::serviceTpls();
        } else {
            $this->error('暂不支持该类型账号');
        }
        return $reserveTpls;
    }

    public function sync()
    {
        if ($this->currentWechat['account_type'] == 'miniprogram') {
            $result = $this->miniprogramSync();
        } elseif ($this->currentWechat['account_type'] == 'service') {
            $result = $this->serviceSync();
        } else {
            $this->error('暂不支持该类型账号');
        }

        $this->error($result['errmsg'] ?: '同步失败');
    }

    private function miniprogramSync()
    {
        $offset = (int)$this->request->param('offset');
        try {
            $result = $this->wechatApp->subscribe_message->getTemplates($offset, 20);
        } catch (\Exception $e) {
            $this->apiException($e);
        }
        if (!empty($result) && empty($result['errcode'])) {
            if (empty($result['list'])) {
                $this->success('未添加过消息模板');
            } else {
                $tpls = $result['list'];
                $tplsbyworkds = array_column($tpls, NULL, 'title');
                $existstpls = WechatTemplateMessageModel::getTpls($this->wid);
                $reserveTpls = WechatTemplateMessageModel::miniprogramTpls();
                $count = 0;
                $countfail = 0;
                foreach ($reserveTpls as $key => $tpl) {
                    if (isset($tplsbyworkds[$tpl['title']])) {
                        $tpl['wechat_id'] = $this->wid;
                        $tpl['type'] = $key;
                        $tpl['template_id'] = $tplsbyworkds[$tpl['title']]['template_id'];
                        $keywords = preg_replace('/\{\{[\w\d\.]+\}\}/', '', $tplsbyworkds[$tpl['title']]['content']);
                        $keywords = preg_split('/\s+/', trim($keywords));
                        $keywords = implode("、", $keywords);
                        $tpl['keywords'] = $keywords;
                        if (isset($existstpls[$key])) {
                            WechatTemplateMessageModel::update($tpl, ['type' => $key, 'wechat_id' => $this->wid]);
                        } else {
                            WechatTemplateMessageModel::create($tpl);
                        }
                        $count++;
                    } else {
                        $countfail++;
                    }
                }

                if (count($tpls) >= 20) {
                    $this->success('正在同步，匹配到模板:' . $count . ($countfail > 0 ? (' 未使用模板:' . $countfail) : ''), url('sync', ['offset' => $offset + 20]), ['next' => true]);
                } else {
                    $this->success('同步完成，匹配到模板:' . $count . ($countfail > 0 ? (' 未使用模板:' . $countfail) : ''), '');
                }
            }
        }
        return empty($result) ? ['errmsg' => '同步失败'] : $result;
    }

    private function serviceSync()
    {
        try {
            $result = $this->wechatApp->template_message->getPrivateTemplates();
        } catch (\Exception $e) {
            $this->apiException($e);
        }
        if (!empty($result) && empty($result['errcode'])) {
            if (empty($result['template_list'])) {
                $this->success('未添加过消息模板');
            } else {
                $tpls = $result['template_list'];
                $tplsbyworkds = array_column($tpls, NULL, 'title');
                $existstpls = WechatTemplateMessageModel::getTpls($this->wid);
                $reserveTpls = WechatTemplateMessageModel::serviceTpls();
                $count = 0;
                $countfail = 0;
                foreach ($reserveTpls as $key => $tpl) {
                    if (isset($tplsbyworkds[$tpl['title']])) {
                        $tpl['wechat_id'] = $this->wid;
                        $tpl['type'] = $key;
                        $tpl['template_id'] = $tplsbyworkds[$tpl['title']]['template_id'];
                        $keywords = preg_replace('/\{\{[\w\d\.]+\}\}/', '', $tplsbyworkds[$tpl['title']]['content']);
                        $keywords = preg_split('/\s+/', trim($keywords));
                        $keywords = implode("、", $keywords);
                        $tpl['keywords'] = $keywords;
                        $tpl['content'] = $tplsbyworkds[$tpl['title']]['content'];
                        if (isset($existstpls[$key])) {
                            WechatTemplateMessageModel::update($tpl, ['type' => $key, 'wechat_id' => $this->wid]);
                        } else {
                            WechatTemplateMessageModel::create($tpl);
                        }
                        $count++;
                    } else {
                        $countfail++;
                    }
                }
                $this->success('同步完成，匹配到模板:' . $count . ($countfail > 0 ? (' 未使用模板:' . $countfail) : ''), '', $tpls);
            }
        }
        return empty($result) ? ['errmsg' => '同步失败'] : $result;
    }

    public function add($id)
    {
        $reserveTpls = $this->reserveTpls();
        $key = '';
        foreach ($reserveTpls as $k => $row) {
            if ($row['tid'] == $id) {
                $key = $k;
                break;
            }
        }
        if (empty($key)) {
            $this->error('非预设模板不能添加');
        }
        $tpl = $reserveTpls[$key];
        if ($this->currentWechat['account_type'] == 'miniprogram') {
            $result = $this->miniprogramAdd($key, $tpl);
        } elseif ($this->currentWechat['account_type'] == 'service') {
            $result = $this->serviceAdd($key, $tpl);
        } else {
            $this->error('暂不支持该类型账号');
        }

        $this->success($result['errmsg'], '', $result);
    }

    private function miniprogramAdd($key, $tpl)
    {
        try {
            $tpllib = $this->wechatApp->subscribe_message->get($tpl['tid']);
            if (!empty($tpllib) && empty($tpllib['errcode'])) {
                $keywords = explode('、', $tpl['keywords']);
                $ids = [];
                $keymap = array_column($tpllib['keyword_list'], 'keyword_id', 'name');
                foreach ($keywords as $k) {
                    if (isset($keymap[$k])) {
                        $ids[] = $keymap[$k];
                    }
                }
                $result = $this->wechatApp->subscribe_message->add($tpl['tid'], $ids);
            }
        } catch (\Exception $e) {
            $this->apiException($e);
        }
        if (!empty($result) && empty($result['errcode'])) {
            $exists = WechatTemplateMessageModel::where('type', $key)->where('wechat_id', $this->wid)->find();
            if (empty($exists)) {
                $tpl['wechat_id'] = $this->wid;
                $tpl['type'] = $key;
                $tpl['template_id'] = $result['template_id'];
                WechatTemplateMessageModel::create($tpl);
            } else {
                $tpl['template_id'] = $result['template_id'];
                WechatTemplateMessageModel::update($tpl, ['type' => $key, 'wechat_id' => $this->wid]);
            }
        }
        return empty($result) ? ['errmsg' => '添加失败'] : $result;
    }

    private function serviceAdd($key, $tpl)
    {
        try {
            $result = $this->wechatApp->template_message->addTemplate($tpl['tid']);
        } catch (\Exception $e) {
            $this->apiException($e);
        }
        if (!empty($result) && empty($result['errcode'])) {
            $exists = WechatTemplateMessageModel::where('type', $key)->where('wechat_id', $this->wid)->find();
            if (empty($exists)) {
                $tpl['wechat_id'] = $this->wid;
                $tpl['type'] = $key;
                $tpl['template_id'] = $result['template_id'];
                WechatTemplateMessageModel::create($tpl);
            } else {
                $tpl['template_id'] = $result['template_id'];
                WechatTemplateMessageModel::update($tpl, ['type' => $key, 'wechat_id' => $this->wid]);
            }
        }
        return empty($result) ? ['errmsg' => '添加失败'] : $result;
    }

    /**
     * 删除模板消息
     * @param $id
     */
    public function del($id)
    {
        $exists = WechatTemplateMessageModel::where('id|type', $id)->where('wechat_id', $this->wid)->find();
        if (empty($exists)) {
            $this->error('模板消息未添加');
        }
        if (empty($exists['template_id'])) {
            WechatTemplateMessageModel::where('id', $exists['id'])->where('wechat_id', $this->wid)->delete();
            $this->error('模板消息未添加');
        }
        try {
            if ($this->currentWechat['account_type'] == 'miniprogram') {
                $result = $this->wechatApp->template_message->delete($exists['template_id']);
            } elseif ($this->currentWechat['account_type'] == 'service') {
                $result = $this->wechatApp->template_message->deletePrivateTemplate($exists['template_id']);
            } else {
                $this->error('暂不支持该类型账号');
            }
        } catch (\Exception $e) {
            $this->apiException($e);
        }
        if (!empty($result) && empty($result['errcode'])) {
            WechatTemplateMessageModel::where('id', $exists['id'])->where('wechat_id', $this->wid)->delete();
            $this->success('删除成功');
        }
        $this->success($result['errmsg'] ?: '删除失败', '', $result);
    }
}
