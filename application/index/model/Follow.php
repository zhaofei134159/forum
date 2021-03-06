<?php 
namespace app\index\model;

class Follow extends Common
{
	protected $table = 'forum_follow';

	# 分用户统计
	public function userCount(){
		$followCount = $this::get_query("SELECT follow_uid,count(1) as count FROM forum_follow group by follow_uid");
     	$followCount = objToArray($followCount,'follow_uid');
		return $followCount;
	}
}