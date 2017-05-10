<?php

// +----------------------------------------------------------------------
// | Author: Jayin Ton <tonjayin@gmail.com>
// +----------------------------------------------------------------------

namespace Area\Controller;


use Common\Controller\AdminBase;

class IndexController extends AdminBase {

    public function index() {
        $this->display();

    }

    /**
     * 导出json文件
     */
    public function export() {
        header("Content-type: application/json;charset=utf-8");
        $export_province = I('post.export_province');
        $export_type = (int)I('post.export_type');
        $where = $export_province ? ['id' => $export_province] : [];
        $province = M('AreaProvince')->where($where)->select();
        $res_p = [];
        foreach ($province as $p_k => $p) {
            $new = [
                'name' => $p['areaname'],
                'code' => $p['id']
            ];
            //查找所属的城市
            if ($export_type >= 2) {
                $new['sub'] = [];
                $citys = M('AreaCity')->where(['parentid' => $p['id']])->select();
                foreach ($citys as $c_k => $c) {
                    $new_c = [
                        'name' => $c['areaname'],
                        'code' => $c['id']
                    ];
                    //查找所属的区/县
                    if ($export_type >= 3) {
                        $new_c['sub'] = [];
                        $districts = M('AreaDistrict')->where(['parentid' => $c['id']])->select();
                        foreach ($districts as $d_k => $d) {
                            $new_d = [
                                'name' => $d['areaname'],
                                'code' => $d['id']
                            ];
                            //查找所属的镇/街道
                            if ($export_type >= 4) {
                                $streets = M('AreaStreet')->where(['parentid' => $d['id']])->select();
                                $new_d['sub'] = [];
                                foreach ($streets as $s_k => $s) {
                                    $new_s = [
                                        'name' => $s['areaname'],
                                        'code' => $s['id']
                                    ];
                                    $new_d['sub'][] = $new_s;
                                }
                            }
                            $new_c['sub'][] = $new_d;
                        }
                    }
                    $new['sub'][] = $new_c;
                }
            }
            $res_p[] = $new;
        }
        echo json_encode($res_p, JSON_UNESCAPED_UNICODE);
    }

}