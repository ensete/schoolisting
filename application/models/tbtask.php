<?php

class tbTask extends DataMapper {

    var $table = 'tbTask';
    /**
	 * init model
	 * @param init: dictionary to filter
	 * @param code_to_throw: if any, an exception will be thrown with this code
	 * @access static
	 */
	static function get_model($init = array(), $code_to_throw = FALSE) {
		$model = new tbTask;
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

    function get_tasks($element_ids) {
        $this->db->where_in('element_id', $element_ids);
        return $this->db->get($this->table)->result_object();
    }
}
