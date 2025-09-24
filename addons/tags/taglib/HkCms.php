<?php
// +----------------------------------------------------------------------
// | HkCms
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 http://www.hkcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 广州恒企教育科技有限公司 <admin@hkcms.cn>
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace addons\tags\taglib;

use think\template\TagLib;

class HkCms extends TagLib
{
    /**
     * 定义标签列表
     */
    protected $tags   =  [
        'taglist' => ['attr'=>'tid,arcid,model,order,num,where,id,empty,page,cache,currentstyle', 'close'=>1], // 标签列表
        'tagarclist' => ['attr'=>'tid,order,num,where,page,id,empty,cache', 'close'=>1], // 标签内容列表
        'contentpage2' => ['attr'=>'item,size,home,pre,next,last,mobile_item,name,info,emptxt,hasemp', 'close'=>0] // 设置分页格式
    ];

    /**
     * 设置分页格式,与HkCms一致，主要是解决HkCms旧版分页问题
     * home-首页,pre-上一页,pageno-页码,next-下一页,last-尾页,info-数量信息,jump-跳转页码
     * size 属性指定多少个页码则显示省略号。至少6个。
     * @param $tag
     * @return string
     */
    public function tagContentpage2($tag)
    {
        $tag['item'] = empty($tag['item']) ? 'pre,pageno,next' : $tag['item'];
        $tag['name'] = empty($tag['name']) ? '$__page__' : $this->autoBuildVar($tag['name']);
        //$tag['dots'] = isset($tag['dots']) && $tag['dots']==1 ? 0 : 1; // 是否显示 1-隐藏，0-显示
        if (request()->isMobile() && !empty($tag['mobile_item'])) {
            $tag['item'] = $tag['mobile_item'];
        }

        $parseStr = '<?php'."\r\n";
        $parseStr .= 'if (!empty('.$tag['name'].')) : '."\r\n";
        $parseStr .= '  $params = [];if(isset($Cate)) : $params = site("url_mode")==1?$Cate:["catname"=>$Cate["name"]];endif;'."\r\n";
        $parseStr .= '  if(isset($Info) && site("url_mode")==1) : $params["aid"] = $Info["id"];endif;'."\r\n";
        $parseStr .= '  if(isset($Info) && site("url_mode")==2) : $params["id"] = $Info["id"];endif;'."\r\n";
        $parseStr .= '  \app\common\library\Bootstrap::diyUrlResolver(function ($page, $options) use($params) {'."\r\n";
        $parseStr .= '      $params = array_merge($params, $options["query"])'.";\r\n";
        $parseStr .= '      $params["page"] = $page;'."\r\n";
        $parseStr .= '      $GLOBALS["JUMP_query"] = $params;'."\r\n";
        $parseStr .= '      $ruleParam = $options["rule"]??[];'."\r\n";
        $parseStr .= '      return index_url($options["path"],$params,true,false,"",$ruleParam,$options["query"]);'."\r\n";
        $parseStr .= '  });'."\r\n";
        $parseStr .= 'echo is_object('.$tag['name'].')?'.$tag['name'].'->render('.self::arrToHtml($tag).'):"";'."\r\n";
        $parseStr .= 'endif; ?>';

        return $parseStr;
    }

    /**
     * 获取所有标签
     * @param $tag
     * @param $content
     * @return string
     */
    public function tagTaglist($tag, $content)
    {
        $tag['id'] = !empty($tag['id']) ? $tag['id'] : 'item';
        $tag['where'] = !empty($tag['where']) ? $this->parseCondition($tag['where']) : '';
        $tag['empty'] = !empty($tag['empty']) ? $tag['empty'] : '';
        $tag['currentstyle'] = !empty($tag['currentstyle']) ? $tag['currentstyle'] : 'active';
        $tag['tid'] = $tag['tid'] ?? '';
        $tag['arcid'] = $tag['arcid'] ?? '';
        $tag['page'] = $tag['page'] ?? 0;
        $tag['model'] = $tag['model'] ??'';
        $tag['num'] = isset($tag['num']) ? ( (substr($tag['num'], 0, 1) == '$') ? $tag['num'] : (int) $tag['num'] ) : 10;

        $this->autoBuildVar($tag['arcid']);
        $this->autoBuildVar($tag['model']);

        $parseStr = '<?php'."\r\n";
        $parseStr .= '$__pagetaglist__=null;'."\r\n";
        $parseStr .= '$__TAGLIST__ = (new \app\admin\model\Tags)->getList('.self::arrToHtml($tag).',$__pagetaglist__);'."\r\n";
        $parseStr .= '$__TagsInfo__ = $Tags ?? [];'."\r\n";
        if ($tag['page']==1) {
            $parseStr .= '$__page__=$__pagetaglist__;'."\r\n";
        }
        $parseStr .= '?>';
        $parseStr .= '{volist name="__TAGLIST__" id="'.$tag['id'].'" empty="'.$tag['empty'].'"}';
        $parseStr .= '{php}$'.$tag['id'].'["currentstyle"]=!empty($__TagsInfo__)?($'.$tag['id'].'["id"]==$__TagsInfo__["id"]?"'.$tag['currentstyle'].'":""):"";{/php}';
        $parseStr .= $content;
        $parseStr .= '{/volist}';
        return $parseStr;
    }

    /**
     * 内容标签页
     * @param $tag
     * @param $content
     * @return string
     */
    public function tagTagarclist($tag, $content)
    {
        $tag['id'] = !empty($tag['id']) ? $tag['id'] : 'item';
        $tag['where'] = !empty($tag['where']) ? $this->parseCondition($tag['where']) : '';
        $tag['empty'] = !empty($tag['empty']) ? $tag['empty'] : '';
        $tag['page'] = $tag['page'] ?? 0;
        $tag['num'] = isset($tag['num']) ? ( (substr($tag['num'], 0, 1) == '$') ? $tag['num'] : (int) $tag['num'] ) : 10;

        $parseStr = '<?php'."\r\n";
        $parseStr .= '$__pagearclist__=null;'."\r\n";
        $parseStr .= '$__TAGCONT__ = (new \app\admin\model\Tags)->getContent('.self::arrToHtml($tag).',$__pagearclist__);'."\r\n";
        if ($tag['page']==1) {
            $parseStr .= '$__page__=$__pagearclist__;'."\r\n";
        }
        $parseStr .= '?>';
        $parseStr .= '{volist name="__TAGCONT__" id="'.$tag['id'].'" empty="'.$tag['empty'].'"}';
        $parseStr .= $content;
        $parseStr .= '{/volist}';
        return $parseStr;
    }

    /**
     * 转换数据为HTML代码
     * @param $data
     * @return string
     */
    private static function arrToHtml($data)
    {
        if (is_array($data)) {
            $str = '[';
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    $str .= "'$key'=>" . self::arrToHtml($val) . ",";
                } else {
                    //如果是变量的情况
                    if (is_int($val)) {
                        $str .= "'$key'=>$val,";
                    } else if (strpos($val, '$') === 0) {
                        $str .= "'$key'=>$val,";
                    } else if (preg_match("/^([a-zA-Z_].*)\(/i", $val, $matches)) {//判断是否使用函数
                        if (function_exists($matches[1])) {
                            $str .= "'$key'=>$val,";
                        } else {
                            $str .= "'$key'=>'" . self::newAddslashes($val) . "',";
                        }
                    } else {
                        $str .= "'$key'=>'" . self::newAddslashes($val) . "',";
                    }
                }
            }
            $str = rtrim($str,',');
            return $str . ']';
        }
        return '';
    }

    /**
     * 返回经addslashes处理过的字符串或数组
     * @param string $string 需要处理的字符串或数组
     * @return mixed
     */
    private static function newAddslashes($string)
    {
        if (!is_array($string)) {
            return addslashes($string);
        }
        foreach ($string as $key => $val) {
            $string[$key] = self::newAddslashes($val);
        }
        return $string;
    }
}