<?php 
namespace app\index\model;

class Cart extends Common
{
	protected $table = 'forum_cart';

	/*
	* @desc查询 该用户每个帖子 所属的版块 以及 回复数量
	* param uid
	* return arr
	*/
	protected function userCartPlate($uid){

        $CartWhere = array();
        $CartWhere['is_del'] = 0;
        $CartWhere['userid'] = $uid;
        $carts = $this->join('forum_plate','forum_cart.plateId = forum_plate.id')->where($CartWhere)->order('forum_cart.ctime','desc')->paginate(20, false,[
                'query'=>['uid'=>$uid],
            ]);
        $page = $carts->render();
        var_dump($carts);die;



	}
}