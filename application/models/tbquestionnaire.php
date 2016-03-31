<?php

class tbQuestionnaire extends DataMapper {

    var $table = 'tbQuestionnaire';
	/**
	 * init model
	 * @param init: dictionary to filter
	 * @param code_to_throw: if any, an exception will be thrown with this code
	 * @access static
	 */
	static function get_model($init = array(), $code_to_throw = FALSE) {
		$model = new tbQuestionnaire;
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
	 * get question and answer for a questionnaire
	 */
	function get_questionnaire ($questionnaire_id, $current_language, $category_id = 0)
	{
		$this->db->select("element.id as question_id, category_id, detail.question, answer, category.name as category_name");
		$this->db->from("`tbQuestionnaire` questionnaire");
		$this->db->join("tbQuestionnaire_question qq", "questionnaire.id = qq.questionnaire_id", "left");
		$this->db->join("tbElement element", "qq.question_id = element.id", "left");
		$this->db->join("tbElement_detail detail", "element.id = detail.element_id and detail.language_id = {$current_language}", "left");
		$this->db->join("tbCategory category", "category.id = element.category_id", "left");
		$this->db->where('questionnaire.id', $questionnaire_id);
        if($category_id) {
            $this->db->where('category.id', $category_id);
        }
		$this->db->order_by('element.category_id');
		return $this->db->get()->result();
	}
}
