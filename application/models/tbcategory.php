<?php

class tbCategory extends DataMapper {

    var $table = 'tbCategory';
	/**
	 * init model
	 * @param init: dictionary to filter
	 * @param code_to_throw: if any, an exception will be thrown with this code
	 * @access static
	 */
	static function get_model($init = array(), $code_to_throw = FALSE) {
		$model = new tbCategory;
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
	 * get all categories with their elements
	 */
	function get_categories_detail ()
	{
		$this->db->select("category.name as category_name, element.id as element_id, element.name as element_name");
		$this->db->from("tbCategory category");
		$this->db->join("tbElement element", "category.id = element.category_id", "left");
        $this->db->join("tbElement_detail detail", "element.id = detail.element_id");
        $this->db->group_by("element.id");
		$result = $this->db->get()->result();
		return $this->convert_categories_detail($result);
	}
	/**
	 * change array result from get_categories_detail into specific array format
	 */
	private function convert_categories_detail ($data)
	{
		$dataset = array();
		foreach ($data as $d) 
		{
			$dataset[$d->category_name][] = array(
				"element_id" => $d->element_id,
				"element_name" => $d->element_name
			);
		}	
		return $dataset;
	}

    function get_all_categories() {
        $this->db->select("id, name");
        return $this->db->get($this->table)->result_array();
    }

    /**
     * @param type: 0=>parent, 1=>school
     * @return mixed
     */
    function get_proper_categories($grade, $type = 1) {
        $this->db->select("t5.id, t5.name");
        $this->db->from("tbQuestionnaire t1");
        $this->db->join("tbQuestionnaire_question t2", "t1.id = t2.questionnaire_id");
        $this->db->join("tbElement_detail t3", "t2.question_id = t3.element_id");
        $this->db->join("tbElement t4", "t3.element_id = t4.id");
        $this->db->join("$this->table t5", "t4.category_id = t5.id");
        $this->db->where('t1.target_type', $type);
        $this->db->where('t1.school_grade', $grade);
        $this->db->group_by('t4.category_id');
        return $this->db->get()->result_array();
    }

    function get_category_mark($school_user_id, $category_id, $month = 0, $year = 0, $time = null) {
        $this->db->select('COALESCE(AVG(t2.average_mark),0) as total, COUNT(t1.id) as count', false);
        $this->db->join("tbRate_category t2", "t1.id = t2.rate_id");
        $this->db->where('t2.category_id', $category_id);
        if($month) {
            $this->db->where('MONTH(t1.createtime)', $month);
            $this->db->where('YEAR(t1.createtime)', $year);
        }
		if (isset($time))
		{
			$this->db->where("t1.createtime <=", $time);
		}
        return $this->db->get("(SELECT * FROM (select * from tbRate order by id DESC) tbRate WHERE parent_id != -1 AND school_id = $school_user_id GROUP BY parent_id) t1")->row_array();
    }

    function get_school_rating($school_user_id, $category_id, $month = 0, $year = 0, $time = null) {
        $this->db->select('t1.id, ROUND((SUM(t2.average_mark) / COUNT(t2.id)), 1) AS total', false);
        $this->db->join("tbRate_category t2", "t1.id = t2.rate_id AND t2.category_id = $category_id", "left");
        if($month) {
            $this->db->where('MONTH(t1.createtime)', $month);
            $this->db->where('YEAR(t1.createtime)', $year);
        }
		if (isset($time))
		{
			$this->db->where("t1.createtime <=", $time);
		}
        return $this->db->get("(SELECT * FROM (select * from tbRate order by id DESC) tbRate WHERE parent_id = -1 AND school_id = $school_user_id GROUP BY MONTH(createtime)) t1")->row_array();
    }

    function mass_delete($ids) {
        $this->db->where_in('id', $ids);
        $this->db->delete($this->table);

        $this->db->where_in('category_id', $ids);
        $this->db->delete("tbElement");
    }

}