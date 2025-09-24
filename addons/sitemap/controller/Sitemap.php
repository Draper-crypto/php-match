<?php

namespace addons\sitemap\controller;


use think\addons\Controller;
use think\facade\Request;

class Sitemap  extends Controller
{


    /**
     * sitemap地图生成
     * @return
     */
    public function index() {

        //设置时区的方法
        date_default_timezone_set('prc');
        //获取准确的时间
        $data = date('y-m-d h:i:s', time());


        

        //获取当前域名
        $articleList = Request::instance()->domain();
        $html = '';
        $html .= '<urlset>';
        $html .= '<url>';
        $html .= '<loc>' . $articleList . '</loc>';
        $html .= ' <lastmod>' . $data .'</lastmod>';
        $html .= '  <priority>1</priority>';
        $html .= '</url>';

        // 获取所有栏目
        $cate = \app\index\model\cms\Category::where('model_id','>',0)->append(['fullurl'])->select()->toArray();
        // 多条可以遍历出来
        foreach ($cate as $ks=>$vs) {
            $html .= '<url>';
            $html .= ' <loc>'.$vs['fullurl'].'</loc>';
            $html .= ' <lastmod>' . $data . '</lastmod>';
            $html .= '  <priority>8</priority>';
            $html .= '</url>';
        }
        // 获取每个栏目里面的文档
        foreach ($cate as $key=>$value) {
            // 获取栏目下的所有文档
            $all = controller($value, function ($obj, $model, $category) {
                // url=链接地址，fullurl=带域名
                return $obj->where(['category_id'=>$category['id']])->append(['publish_time_text','fullurl'])->select()->toArray();
                // 多条
            }
                , 'category');
            // 多条可以遍历出来
            foreach ($all as $k=>$v) {
                $html .= '<url>';
                $html .= ' <loc>'.$v['fullurl'].'</loc>';
                $html .= ' <lastmod>'.$v['create_time'].'</lastmod>';
                $html .= '  <priority>0.64</priority>';
                $html .= '</url>';
            }
        }
        $html .= '</urlset>';
        //最后一个参数是去掉tp字典的根节点，只输出自己的内容
        $result = xml($html, 200, [], ['root_node' => 'xml']);
        return ($result);


    }





}