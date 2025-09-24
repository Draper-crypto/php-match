<?php

if (!function_exists('ColorHSLToRGB')) {
    /**
     * HSL转RGB
     * @param $h
     * @param $s
     * @param $l
     * @return array
     */
    function ColorHSLToRGB($h, $s, $l)
    {
        $r = $g = $b = 0;
        $v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
        if ($v > 0){
            $m = $l + $l - $v;
            $sv = ($v - $m ) / $v;
            $h *= 6.0;
            $sextant = floor($h);
            $fract = $h - $sextant;
            $vsf = $v * $sv * $fract;
            $mid1 = $m + $vsf;
            $mid2 = $v - $vsf;

            switch ($sextant)
            {
                case 0:
                    $r = $v;
                    $g = $mid1;
                    $b = $m;
                    break;
                case 1:
                    $r = $mid2;
                    $g = $v;
                    $b = $m;
                    break;
                case 2:
                    $r = $m;
                    $g = $v;
                    $b = $mid1;
                    break;
                case 3:
                    $r = $m;
                    $g = $mid2;
                    $b = $v;
                    break;
                case 4:
                    $r = $mid1;
                    $g = $m;
                    $b = $v;
                    break;
                case 5:
                    $r = $v;
                    $g = $m;
                    $b = $mid2;
                    break;
            }
        }
        return array(floor($r * 255.0), floor($g * 255.0), floor($b * 255.0));
    }
}

if (!function_exists('user_auth_check')) {
    /**
     * 规则判断
     * @param string $name 规则
     * @return mixed
     */
    function user_auth_check($name)
    {
        if (strstr($name,'.') || strstr($name, '/')) {
            $name = strstr($name,'.') ? str_replace('.','/', $name):$name;
            $name = ltrim($name, '/');
        } else {
            $controller = strtolower(\think\facade\Request::controller());
            $name = str_replace('.','/',$controller.'/').$name;
        }
        $user = \addons\user\library\User::instance();
        return $user->check($name, $user->id);
    }
}