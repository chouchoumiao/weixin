<?php
namespace Admin\Controller;
use Admin\Controller;
use APP\Model\ToolModel;

class AdviceController extends CommonController
{


    public function doAction()
    {
        //根据传入的事件进入对应各页面的显示处理
        $action = strval($_GET['action']);

        if (isset($action) && ('' != $action)) {

            //用于普通登录
            switch ($action) {
                case 'showAdvice':
                    $this->showAdvice();
                    break;
                case 'adviceSet':
                    $this->adviceSet();
                    break;
                case 'adviceSetData':
                    $this->adviceSetData();
                    break;
                case 'adviceReply':
                    $this->adviceReply();
                    break;
                default:
                    ToolModel::goBack('访问地址错误');
                    break;

            }
        }
    }

    /**
     * 回复内容画面显示
     */
    private function adviceReply(){

        //获取问题ID号传入
        if(!$_GET["adviceID"]){
            ToolModel::goBack('参数错误,请确认');
        }

        $adviceID=intval(addslashes($_GET["adviceID"]));
        $data = D('Advice')->getTheAdviceInfo($adviceID);
        if(!$data)
        {
            ToolModel::goBack('无此建言信息！');
        }

        if(isset($_GET['nowPage'])){
            $this->assign('nowPage',intval($_GET['nowPage']));
        }

        $this->assign('adviceInfoArr',$data);
        $this->display('Advice/adviceReply');
    }

    /**
     * 编辑提交
     */
    private function adviceSetData(){

        $flag = addslashes($_GET['flag']);
        $adviceID = addslashes($_POST["adviceID"]);
        
        switch ($flag){
            case 'Reply':

                //设置未通过审核
                $data['ADVICE_REPLY'] = addslashes($_POST["newBbsReply"]);
                $data['ADVICE_REPLYTIME'] = date("Y/m/d H:i:s",time());

                if(D('Advice')->updateAdviceInfo($adviceID,$data)){
                    ToolModel::jsonReturn(JSON_SUCCESS,'【回复建言内容】设置成功！');
                }
                ToolModel::jsonReturn(JSON_ERROR,'【回复建言内容】设置失败，请重新设置！');
                break;
            case 'NG':
                
                
                //设置未通过审核
                $NGData['ADVICE_ISOK'] = 2;
                $NGData['ADVICE_EDITETIME'] = date("Y/m/d H:i:s",time());
                $NGData['ADVICE_EVENT'] = 0;

                if(D('Advice')->updateAdviceInfo($adviceID,$NGData)){
                    ToolModel::jsonReturn(JSON_SUCCESS,'【未通过审核】设置成功！');
                }
                ToolModel::jsonReturn(JSON_ERROR,'【未通过审核】设置失败，请重新设置！');

                break;
            case 'ok':
                $OKData['ADVICE_ADVICE'] = addslashes($_POST["newBbsContent"]);
                $OKData['ADVICE_ISOK'] = 1;
                $OKData['ADVICE_EDITETIME'] = date("Y/m/d H:i:s",time());
                $OKData['ADVICE_EVENT'] = 0;

                if(D('Advice')->updateAdviceInfo($adviceID,$OKData)){
                    ToolModel::jsonReturn(JSON_SUCCESS,'【通过审核】设置成功！');
                }
                ToolModel::jsonReturn(JSON_ERROR,'【通过审核】设置失败，请重新设置！'.$aa);

                break;
            case 'okANDEvent':

                $okANDEvent['ADVICE_ADVICE'] = addslashes($_POST["newBbsContent"]);
                $okANDEvent['ADVICE_ISOK'] = 3;
                $okANDEvent['ADVICE_EDITETIME'] = date("Y/m/d H:i:s",time());
                $okANDEvent['ADVICE_EVENT'] = 1;

                if(D('Advice')->updateAdviceInfo($adviceID,$okANDEvent)){
                    ToolModel::jsonReturn(JSON_SUCCESS,'【通过审核并有抽奖资格】设置成功！');
                }
                ToolModel::jsonReturn(JSON_ERROR,'【通过审核并有抽奖资格】设置失败，请重新设置！');

                break;
            default:
                
                break;
        }
    }

    //进行审核操作
    private function adviceSet(){

        //获取问题ID号传入
        $adviceID=intval(addslashes($_GET["adviceID"]));
//        $page=intval(addslashes($_GET["page"]));
        
        //根据传入的advice查询对应的信息
        //判断是否修改，如果传入了问题ID，进行数据库查询获取全部内容
        if(!$adviceID){
            ToolModel::goBack('参数错误');
            exit;
        }

        $data = D('Advice')->getTheAdviceInfo($adviceID);
        if(!$data)
        {
            ToolModel::goBack('无此建言信息!');
            exit;
        }

        $this->assign('adviceInfoArr',$data);
        if(isset($_GET['nowPage'])){
            $this->assign('nowPage',intval($_GET['nowPage']));
        }


        $this->display('Advice/adviceSet');
    }

    /**
     * 显示建言献策的所有记录
     */
    private function showAdvice(){

        //获得建言的总条数
        $count = D('Advice')->getAdviceCunt();

        //分页
        import('ORG.Util.Page');
        $Page = new \Org\Util\Page($count, PAGE_SHOW_COUNT_10);

        $nowPage = intval($Page->parameter['p']);

        $limit = $Page->firstRow . ',' . $Page->listRows;

        //取得指定条数的信息
        $data = D('Advice')->getAllAdviceInfo($limit);

        $show = $Page->show();// 分页显示输出

        //如果有数据的情况
        if ($count > 0) {
            foreach ($data as $item=>$value){

                if($value['ADVICE_ISOK'] == 0){
                    $data[$item]['isOKFlag'] = "未审核";
                }else if($value['ADVICE_ISOK'] == 1){
                    $data[$item]['isOKFlag'] = "通过";
                }else if($value['ADVICE_ISOK'] == 2){
                    $data[$item]['isOKFlag'] = "未通过";
                }else if($value['ADVICE_ISOK'] == 3){
                    $data[$item]['isOKFlag'] = "通过有抽奖资格";
                }

                if($value['ADVICE_EVENT'] == 1){
                    $data[$item]['isEvent'] = "有";
                }else{
                    $data[$item]['isEvent'] = "无";
                }

                //获取Vip的总可刮奖次数和已经刮奖次数
                $openid = $value['ADVICE_OPENID'];

                $data[$item]['chanceCount'] = D('Advice')->getAdviceCountByOpenid($openid);

                //获得当前公众号最新的有效的刮刮卡获得ID
                $scratchcardID = D('Scratchcard')->getMaxEventID();
                if(intval($scratchcardID) > 0){
                    $data[$item]['userCount'] = D('Scratchcard')->getScratchcardUserCountByOpenid($openid,$scratchcardID);
                }else{
                    $data[$item]['userCount'] = 0;
                }
            }
            $this->assign('data', $data); //用户信息注入模板
            $this->assign('page', $show);    //赋值分页输出
            $this->assign('nowPage',$nowPage);
        }

        $this->display('Advice/adviceInfoSearch');
    }
}