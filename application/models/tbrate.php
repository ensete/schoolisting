<?php

class tbRate extends DataMapper {

    var $table = 'tbRate';
    /**
	 * init model
	 * @param init: dictionary to filter
	 * @param code_to_throw: if any, an exception will be thrown with this code
	 * @access static
	 */
	static function get_model($init = array(), $code_to_throw = FALSE) {
		$model = new tbRate;
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

    function get_self_evaluation($school_user_id) {
        $this->db->select('t1.createtime');
        $this->db->select('(t2.total_mark / t2.rating_categories) as average_rate');
        $this->db->join("(SELECT rate_id, COUNT(id) As rating_categories, COALESCE(SUM(average_mark),0) as total_mark FROM tbRate_category GROUP BY rate_id) t2", "t1.id = t2.rate_id");
        $this->db->where('t1.school_id', $school_user_id);
        $this->db->where('t1.parent_id', -1);
        $this->db->group_by('t1.id');
        $this->db->order_by('t1.createtime', "DESC");
        return $this->db->get("$this->table t1")->result_array();
    }

    function get_parent_evaluation($school_user_id, $display = 0, $position = 0) {
        $this->db->select('t1.createtime, t3.username, t3.uuid');
        $this->db->select('(t2.total_mark / t2.rating_categories) as average_rate');
        $this->db->select('t4.full_name');
        $this->db->join("(SELECT rate_id, COUNT(id) As rating_categories, COALESCE(SUM(average_mark),0) as total_mark FROM tbRate_category GROUP BY rate_id) t2", "t1.id = t2.rate_id");
        $this->db->join("plusauthentication_user t3", "t1.parent_id = t3.id");
        $this->db->join("tbParents t4", "t3.id = t4.user_id");
        $this->db->where('t1.school_id', $school_user_id);
        $this->db->where('t1.parent_id !=', -1);
        if($display) {
            $this->db->limit($display, $position);
        }
        $this->db->group_by('t1.id');
        $this->db->order_by('t1.createtime', "DESC");
        return $this->db->get("$this->table t1")->result_array();
    }

    function get_unique_parent_evaluation($school_user_id) {
        $this->db->select('t1.createtime, t3.username, t3.uuid');
        $this->db->select('(t2.total_mark / t2.rating_categories) as average_rate');
        $this->db->select('t4.full_name');
        $this->db->join("(SELECT rate_id, COUNT(id) As rating_categories, COALESCE(SUM(average_mark),0) as total_mark FROM tbRate_category GROUP BY rate_id) t2", "t1.id = t2.rate_id");
        $this->db->join("plusauthentication_user t3", "t1.parent_id = t3.id");
        $this->db->join("tbParents t4", "t3.id = t4.user_id");
        return $this->db->get("(SELECT * FROM (SELECT * FROM tbRate ORDER BY id DESC) tbRate WHERE parent_id != -1 AND school_id = $school_user_id GROUP BY parent_id) t1")->result_array();
    }

    function get_all_parent_evaluation($parent_user_id) {
        $this->db->select('t1.createtime, t3.school_name, t5.uuid');
        $this->db->select('(t2.total_mark / t2.rating_categories) as average_rate');
        $this->db->join("(SELECT rate_id, COUNT(id) As rating_categories, COALESCE(SUM(average_mark),0) as total_mark FROM tbRate_category GROUP BY rate_id) t2", "t1.id = t2.rate_id");
        $this->db->join("tbSchools t3", "t1.school_id = t3.user_id");
        $this->db->join("plusauthentication_user t5", "t3.user_id = t5.id");
        $this->db->where('t1.parent_id', $parent_user_id);
        $this->db->group_by('t1.id');
        $this->db->order_by('t1.createtime', "DESC");
        return $this->db->get("$this->table t1")->result_array();
    }

    function get_parent_rating($school_user_id) {
        $this->db->select('COUNT(t1.id) as total_rate, ROUND( SUM(t2.total_mark / t2.rating_categories) / COUNT(t2.rating_categories), 1) as average_rating', false);
        $this->db->join("(SELECT rate_id, COUNT(id) As rating_categories, COALESCE(SUM(average_mark),0) as total_mark FROM tbRate_category GROUP BY rate_id) t2", "t1.id = t2.rate_id");
        return $this->db->get("(SELECT * FROM (SELECT * FROM tbRate ORDER BY id DESC) tbRate WHERE parent_id != -1 AND school_id = $school_user_id GROUP BY parent_id) t1")->row_array();
    }

    function get_school_rating($school_user_id) {
        $this->db->select("ROUND(t2.total_mark / t2.rating_categories, 1) as average_rating", false);
        $this->db->join("(SELECT rate_id, COUNT(id) As rating_categories, COALESCE(SUM(average_mark),0) as total_mark FROM tbRate_category GROUP BY rate_id) t2", 't1.id = t2.rate_id');
        $this->db->where('t1.school_id', $school_user_id);
        $this->db->where('t1.parent_id', -1);
        $this->db->order_by('t1.createtime', 'DESC');
        $this->db->limit(1);
        return $this->db->get("$this->table t1")->row_array();
    }

}
