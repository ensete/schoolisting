<?php

class tbQuestionnaire_question extends DataMapper {

    var $table = 'tbQuestionnaire_question';
	/**
	 * init model
	 * @param init: dictionary to filter
	 * @param code_to_throw: if any, an exception will be thrown with this code
	 * @access static
	 */
	static function get_model($init = array(), $code_to_throw = FALSE) {
		$model = new tbQuestionnaire_question;
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
	function get_list_question_as_array($questionnaire_id = "")
	{
		$questionnaire_question = new tbQuestionnaire_question();
		$questionnaire_question->where('questionnaire_id', $questionnaire_id);
		$questionnaire_question->get();
		
		$dataset = array();
		foreach ($questionnaire_question as $qq) 
		{
			$dataset[] = $qq->question_id;
		}
		return $dataset;
	}
	
}