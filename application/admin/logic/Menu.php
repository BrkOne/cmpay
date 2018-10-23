<?php
/**
 *  +----------------------------------------------------------------------
 *  | 草帽支付系统 [ WE CAN DO IT JUST THINK ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: Brian Waring <BrianWaring98@gmail.com>
 *  +----------------------------------------------------------------------
 */

namespace app\admin\logic;
use app\common\library\enum\CodeEnum;

/**
 * 菜单逻辑
 */
class Menu extends BaseAdmin
{
    
    // 面包屑
    public static $crumbs       = [];
    
    // 菜单Select结构
    public static $menuSelect   = [];
    
    /**
     * 菜单转视图
     */
    public function menuToView($menu_list = [], $child = 'child')
    {

        $menu_view = '';
        
        //遍历菜单列表
        foreach ($menu_list as $menu_info) {
            
            if (!empty($menu_info[$child])) {
             
                $icon = empty($menu_info['icon']) ? 'home' : $menu_info['icon'];

                $menu_view.= "<li data-id='".$menu_info['id']."' class='layui-nav-item'>
                                  <a href='javascript:;' lay-tips='".$menu_info['name']."' lay-direction='2'>
                                    <i class='layui-icon layui-icon-$icon'></i>
                                    <cite>".$menu_info['name']."</cite>
                                  </a>
                                  <dl class='layui-nav-child'>
                                  ".$this->menuToView($menu_info[$child],  $child)."
                                  </dl>
                                </li>";
                
            } else {
                $url = url($menu_info['url']);

                if ($menu_info['id'] == 1){

                    $icon = empty($menu_info['icon']) ? 'home' : $menu_info['icon'];

                    $menu_view.= "<li data-id='".$menu_info['id']."' class='layui-nav-item'>
                                  <a href='javascript:;' lay-tips='".$menu_info['name']."' lay-direction='2'>
                                    <i class='layui-icon layui-icon-$icon'></i>
                                    <cite>".$menu_info['name']."</cite>
                                  </a>
                                </li>";
                }else{
                    $menu_view.= "<dd data-id='".$menu_info['id']."'>
                                 <a lay-href='$url'>".$menu_info['name']."</a>
                               </dd>";
                }
            }
       }
       
       return $menu_view;
    }
    
    /**
     * 菜单转Select
     */
    public function menuToSelect($menu_list = [], $level = 0, $name = 'name', $child = 'child')
    {
        
        foreach ($menu_list as $info) {
            
            $tmp_str = str_repeat("&nbsp;", $level * 4);
            
            $tmp_str .= "├";

            $info['level'] = $level;
            
            $info[$name] = empty($level) || empty($info['pid']) ? $info[$name]."&nbsp;" : $tmp_str . $info[$name] . "&nbsp;";
            
            if (!array_key_exists($child, $info)) {

                array_push(self::$menuSelect, $info);
            } else {
                
                $tmp_ary = $info[$child];
                
                unset($info[$child]);
                
                array_push(self::$menuSelect, $info);
                
                $this->menuToSelect($tmp_ary, ++$level, $name, $child);
            }
        }
        
        return self::$menuSelect;
    }
    
    /**
     * 菜单转Checkbox
     */
    public function menuToCheckboxView($menu_list = [], $child = 'child')
    {
        
        $menu_view = '';
        
        $id = input('id');
        
        $auth_group_info = $this->logicAuthGroup->getGroupInfo(['id' => $id], 'rules');
        
        $rules_array = str2arr($auth_group_info['rules']);
        //遍历菜单列表
        foreach ($menu_list as $menu_info) {

            $checkbox_select = in_array($menu_info['id'], $rules_array) ? "checked='checked'" : '';
            
            if (!empty($menu_info[$child])) {
                
                $menu_view.=  "<div class='layui-card-body layui-row layui-col-space12'>
                                  <div class='layui-col-md12'>
                                    <input lay-filter='checkbox-parent' lay-skin='primary'  type='checkbox' data-pid='".$menu_info['pid']."' data-id='".$menu_info['id']."' name='rules[]' value='".$menu_info['id']."' $checkbox_select title=' ".$menu_info['name']." '>
                                        <div class='layui-card-body layui-row layui-col-space10'>".$this->menuToCheckboxView($menu_info[$child],  $child)." </div>
                                  </div>
                                </div>";
                
            } else {
                
                $menu_view.= "<div class='layui-col-md12 checkbox-child'><input lay-filter='checkbox-child' type='checkbox' data-pid='".$menu_info['pid']."' data-id='".$menu_info['id']."' lay-skin='primary' name='rules[]' value='".$menu_info['id']."'  $checkbox_select title=' ".$menu_info['name']." '></div>";
            }
       }


       return $menu_view;
    }
    
    /**
     * 菜单选择
     */
    public function selectMenu($menu_view = '')
    {
        
        $map['url']    = request()->url();
        $map['module'] = request()->module();
                
        $menu_info = $this->getMenuInfo($map);
        
        // 获取自己及父菜单列表
        $this->getParentMenuList($menu_info['id']);

        // 选中面包屑中的菜单

        foreach (self::$crumbs as $menu_info) {

            $replace_data = "menu_id='".$menu_info['id']."'";
            
            $menu_view = str_replace($replace_data, " class='active' ", $menu_view);
        }
        
       return $menu_view;
    }
    
    /**
     * 获取自己及父菜单列表
     */
    public function getParentMenuList($menu_id = 0)
    {
        
        $menu_info = $this->getMenuInfo(['id' => $menu_id]);
        
        !empty($menu_info['pid']) && $this->getParentMenuList($menu_info['pid']);
        
        self::$crumbs [] = $menu_info;
    }
    
    /**
     * 获取面包屑
     */
    public function getCrumbsView()
    {
        
        $crumbs_view = "<ol class='breadcrumb'>";
      
        foreach (self::$crumbs as $menu_info) {
            
            $icon = empty($menu_info['icon']) ? 'fa-circle-o' : $menu_info['icon'];
            
            $crumbs_view .= "<li><a><i class='fa $icon'></i> ".$menu_info['name']."</a></li>";
        }
        
        $crumbs_view .= "</ol>";
        
        return $crumbs_view;
    }
    
    /**
     * 获取菜单列表
     */
    public function getMenuList($where = [], $field = true, $order = '', $paginate = false)
    {
        
        return $this->modelMenu->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 获取菜单信息
     */
    public function getMenuInfo($where = [], $field = true)
    {
        
        return $this->modelMenu->getInfo($where, $field);
    }
    
    /**
     * 菜单添加
     */
    public function menuAdd($data = [])
    {
        
        $validate_result = $this->validateMenu->scene('add')->check($data);
        
        if (!$validate_result) {
            
            return [CodeEnum::ERROR, $this->validateMenu->getError()];
        }
        
        $result = $this->modelMenu->setInfo($data);

        $url = url('menuList', ['pid' => $data['pid'] ? $data['pid'] : 0]);
        
        return $result ? [CodeEnum::SUCCESS, '菜单添加成功', $url] : [CodeEnum::ERROR, $this->modelMenu->getError()];
    }
    
    /**
     * 菜单编辑
     */
    public function menuEdit($data = [])
    {
        
        $validate_result = $this->validateMenu->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [CodeEnum::ERROR, $this->validateMenu->getError()];
        }
        
        $url = url('menuList', ['pid' => $data['pid'] ? $data['pid'] : 0]);
        
        $result = $this->modelMenu->setInfo($data);
        
        return $result ? [CodeEnum::SUCCESS, '菜单编辑成功', $url] : [CodeEnum::ERROR, $this->modelMenu->getError()];
    }
    
    /**
     * 菜单删除
     */
    public function menuDel($where = [])
    {
        
        $result = $this->modelMenu->deleteInfo($where);

        return $result ? [CodeEnum::SUCCESS, '菜单删除成功'] : [CodeEnum::ERROR, $this->modelMenu->getError()];
    }
    
    /**
     * 获取默认页面标题
     */
    public function getDefaultTitle()
    {
        
        return $this->modelMenu->getValue(['module' => request()->module(), 'url' => request()->url()], 'name');
    }
}
