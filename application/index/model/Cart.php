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
	public function userCartPlate($uid){

        $CartWhere = array();
        $CartWhere['forum_cart.is_del'] = 0;
        $CartWhere['forum_cart.userid'] = $uid;
        $CartWhere['forum_cart.cartId'] = 0;
        $field = 'forum_plate.id as pid,forum_plate.name,forum_plate.cateid,forum_plate.userid as puserid,forum_plate.is_del,forum_plate.ctime as pctime,forum_cart.id as id,forum_cart.plateId,forum_cart.title,forum_cart.userid,forum_cart.see,forum_cart.is_del,forum_cart.is_hot,forum_cart.is_elite,forum_cart.is_top,forum_cart.floor,forum_cart.ctime';
        $carts = $this->join('forum_plate','forum_cart.plateId = forum_plate.id','LEFT')->field($field)->where($CartWhere)->order('forum_cart.ctime','desc')->paginate(20, false);
        // ,['query'=>['uid'=>$uid],]
        $page = $carts->render();

        $PlateCart = array();
        $Plates = array();
       	foreach($carts as $cart){
       		$PlateCart[$cart['plateId']][$cart['id']] = $cart;
       		$Plates[$cart['plateId']] = $cart;
       	}

       	return ['PlateCart'=>$PlateCart,'Plates'=>$Plates,'page'=>$page];
	}

  public function userSendCartPlate($uid){

        $CartWhere = array();
        $CartWhere['forum_cart.is_del'] = 0;
        $CartWhere['forum_cart.userid'] = $uid;
        $CartWhere['forum_cart.cartId'] = 0;
        $field = 'forum_plate.id as pid,forum_plate.name,forum_plate.cateid,forum_plate.userid as puserid,forum_plate.is_del,forum_plate.ctime as pctime,forum_cart.plateId ';
        $carts = $this->join('forum_plate','forum_cart.plateId = forum_plate.id','LEFT')->field($field)->where($CartWhere)->order('forum_cart.ctime','desc')->limit(10)->select();
        // ,['query'=>['uid'=>$uid],]
        $Plates = array();
        foreach($carts as $cart){
          $Plates[$cart['plateId']] = $cart;
        }

        return ['Plates'=>$Plates];
  }

  public function userPalteCart($plateId,$uid){

        $CartWhere = array();
        $CartWhere['forum_cart.is_del'] = 0;
        $CartWhere['forum_cart.userid'] = $uid;
        $CartWhere['forum_cart.cartId'] = 0;
        $CartWhere['forum_cart.plateId'] = $plateId;
        $field = 'forum_plate.id as pid,forum_plate.name,forum_plate.cateid,forum_plate.userid as puserid,forum_plate.is_del,forum_plate.ctime as pctime,forum_cart.id as id,forum_cart.plateId,forum_cart.title,forum_cart.userid,forum_cart.see,forum_cart.is_del,forum_cart.is_hot,forum_cart.is_elite,forum_cart.is_top,forum_cart.floor,forum_cart.ctime';
        $carts = $this->join('forum_plate','forum_cart.plateId = forum_plate.id','LEFT')->field($field)->where($CartWhere)->order('forum_cart.ctime','desc')->paginate(15, false ,['query'=>['uid'=>$uid,'plateId'=>$plateId],]);
        $page = $carts->render();

        foreach($carts as $cKey=>$cart){
          $reply = $this->where(['cartId'=>$cart['id'],'is_del'=>0])->count();
          $carts[$cKey]['reply'] = $reply;
        }


        return ['carts'=>$carts,'page'=>$page];
  }

  public function PlateCart(){
        $plateWhere = array();
        $plateWhere['is_del'] = 0;
        $plateWhere['is_check'] = 1;
        // $plateWhere['is_home'] = 1;
        $plates = Plate::where($plateWhere)->order('ctime','desc')->limit(6)->select();
        $plates = objToArray($plates);

        foreach($plates as $plateId=>$plate){
            $cartWhere = array();
            $cartWhere['is_del'] = 0;
            $cartWhere['cartId'] = 0;
            $cartWhere['plateId'] = $plateId;
            $carts = $this->where($cartWhere)->order('is_elite,is_hot,is_top','desc')->limit(4)->select();
            $carts = objToArray($carts);
            if(empty($carts)){
              unset($plates[$plateId]);
              continue;
            }
            foreach($carts as $cartId=>$cart){
              $reply = $this->where(['cartId'=>$cart['id'],'is_del'=>0])->count();
              $carts[$cartId]['reply'] = $reply;
            }
            $plates[$plateId]['carts'] = $carts;
        }

        return $plates;
  }

  public function userCount(){
    $userCartCount = $this::get_query("SELECT userid,count(1) as count FROM forum_cart where cartId=0 and is_del=0 group by userid");
    $userCartCount = objToArray($userCartCount,'userid');
    return $userCartCount;
  }
}