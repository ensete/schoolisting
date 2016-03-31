<?php

class tbElement extends DataMapper {

    var $table = 'tbElement';
	/**
	 * init model
	 * @param init: dictionary to filter
	 * @param code_to_throw: if any, an exception will be thrown with this code
	 * @access static
	 */
	static function get_model($init = array(), $code_to_throw = FALSE) {
		$model = new tbElement;
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
	 * get an element with specific language
	 */
	function get_element ($element_id = -1, $language_id = null)
	{
		return $this->db->select("tbElement.id, tbElement.name, tbElement.description, tbElement_detail.question, tbElement_detail.answer")
						->from("tbElement tbElement ")
						->join("( select element_id,question,answer,language_id from tbElement_detail where `language_id` = {$language_id}) tbElement_detail ", "tbElement.id = tbElement_detail.element_id", "left")
						->where('id', $element_id)
						->get()
						->result();
	}

    function mass_delete($ids) {
        $this->db->where_in('id', $ids);
        $this->db->delete($this->table);
    }

    function get_all_elements() {
        $this->db->select("id, name");
        return $this->db->get($this->table)->result_array();
    }
}