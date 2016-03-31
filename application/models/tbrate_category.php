<?php

class tbRate_category extends DataMapper {

    var $table = 'tbRate_category';
    /**
	 * init model
	 * @param init: dictionary to filter
	 * @param code_to_throw: if any, an exception will be thrown with this code
	 * @access static
	 */
	static function get_model($init = array(), $code_to_throw = FALSE) {
		$model = new tbRate_category;
		if (count($init) > 0) {
			foreach ($init as $key => $value) {
				$model -> where($key, $value);
			}
			$model -> get();
			if (!$model -> exists() && ($code_to_throw !== FALSE)) {
				throw new Exception("", $code_to_throw);
			}
		}
		return $model;
	}
	/**
	 * 
	 */
	function add ($data = array())
	{
		if (count($data) > 0)
		{
			foreach ($data as $key => $value) {
				$this->{$key} = $value;
			}
			$this->save();
			return $this->id;
		}
		return -1;
	}

    function get_average_rate($rate_id) {
        $this->db->select("AVG(t1.average_mark) as mark");
        $this->db->select("t3.token, t3.username, t3.uuid, t3.avatar, t2.createtime as create_time");
        $this->db->select("t4.is_active, t4.appearance, t4.full_name");
        $this->db->join("tbRate t2", "t1.rate_id = t2.id");
        $this->db->join("plusauthentication_user t3", "t2.parent_id = t3.id", "left");
        $this->db->join("tbParents t4", "t4.user_id = t2.parent_id", "left");
        $this->db->where('t1.rate_id', $rate_id);
        return $this->db->get("$this->table t1")->row_array();
    }
}
