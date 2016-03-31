<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Questionnaire extends MY_Controller {
	function questionnaire_for_parent ()
	{
		$parent = check_login('parent');
		$school_name = (string) $this->input->get('name');
		
		$school = $this->school_model->get_by_attribute(array('school_name'=>$school_name));
		if (!$school)
		{
			redirect("/");
		}
		
		$data = array();
		$data['user'] = $parent;
        $data['current_id'] = $parent["id"];
        $data['user_type'] = "parents";
        $data['page'] = "parents/questionnaire";
        $data['has_footer'] = TRUE;
        $data['has_header'] = TRUE;
        $data['head']['title'] = "Questionnaire";

		$questionnaire = tbQuestionnaire::get_model(array("target_type" => 0, "school_grade" => $school['grade']));
		if (!$questionnaire->exists())
		{
			echo "Sorry! Questionnaire for this type of school does not exist.";
			return;
		}
		$current_language = $this->get_current_language();
        $questions = tbQuestionnaire::get_model()->get_questionnaire($questionnaire->id, $current_language);

        $s = new tbSaved_answer();
        $saved_answer = $s->get_saved_answers($parent['id'], 1, $school['school_id']);
        if($saved_answer) {
            $decode = json_decode($saved_answer[0]['answer']);
            $data['saved_answers'] = json_decode(json_encode(@$decode->answers), true);
            $data['categories'] = json_encode($decode->categories);
            $data['saved_answer_id'] = $saved_answer[0]['id'];
        } else {
            $data['saved_answer_id'] = 0;
            $data['categories'] = array();
        }

		$data["questions"] = $questions;
		$data["questionnaire"] = $questionnaire->id;
		$data["school_id"] = $school["id"];
		$data["self_evaluate"] = 0;
        $this->load->view('basic_elements/index', $data);
	}
	/**
	 * 
	 */
	function questionnaire_for_school ()
	{
		$school = check_login('school');
        $school = $this->school_model->get_by_attribute(array('t1.id' => $school['id']));

		$data = array();
		$data['user'] = $school;
        $data['current_id'] = $school["id"];
        $data['page'] = "parents/questionnaire";
        $data['has_footer'] = TRUE;
        $data['has_header'] = TRUE;
        $data['user_type'] = "school";
        $data['head']['title'] = "Questionnaire";
		
		$questionnaire = tbQuestionnaire::get_model(array("target_type" => 1, "school_grade" => $school['grade']));
		if (!$questionnaire->exists())
		{
			echo "Sorry! Questionnaire for this type of school does not exist.";
			return;
		}
		$current_language = $this->get_current_language();

        $s = new tbSaved_answer();
        $saved_answer = $s->get_saved_answers($school['user_id'], 2);
        if($saved_answer) {
            $decode = json_decode($saved_answer[0]['answer']);
            $data['saved_answers'] = json_decode(json_encode(@$decode->answers), true);
            $data['categories'] = json_encode($decode->categories);
            $data['saved_answer_id'] = $saved_answer[0]['id'];
        } else {
            $data['saved_answer_id'] = 0;
            $data['categories'] = array();
        }

		$data["questions"] = tbQuestionnaire::get_model()->get_questionnaire($questionnaire->id, $current_language);
		$data["questionnaire"] = $questionnaire->id;
		$data["school_id"] = $school['id'];
		$data["self_evaluate"] = 1;
        $this->load->view('basic_elements/index', $data);
	}
	/**
	 * 
	 */
	function get_current_language ()
	{
		$this->load->model('pluslocalization/pluslocalization_supported');
		$primary = $this->pluslocalization_supported->get_by_attribute(array('is_primary' => 1));
		if ($primary === FALSE)
		{
			$primary = "en";
		}
		else
		{
			$primary = $primary[0]->name;
		}
    	$l = ($lang = $this->session->userdata('site_language')) ? $lang : $primary;
		$language = $this->pluslocalization_supported->get_by_attribute(array("name"=>$l));
		return (int) $language[0]->id;
	}

    private function process_raw_questionnaire($post) {
        // convert answer into specific format
        // separate into categories and answers
        $answers = array();
        $category = array();
        foreach ($post as $key => $value)
        {
            $data = explode("-", $key);
            if ($data[0] == "category")
            {
                $category[$data[1]] = $value;
            }
            else
            {
                $answers[$data[3]][$data[1]][$data[2]] = $value;
            }
        }
        // remove questions in inactive category
        foreach ($category as $key => $value)
        {
            if ($value == 0)
            {
                unset($answers[$key]);
            }
        }

        return $answers;
    }
	/**
	 * 
	 */
	public function submit_questionnaire ()
	{
		$post = $this->input->post();
        //$questionnaire_id = $post['questionnaire_id'];
        $school_id = $post['school_id'];
        $self_evaluate = $post['self_evaluate'];
        $saved_answer_id = $post['saved_answer_id'];

        if($saved_answer_id) {
            $s = new tbSaved_answer();
            $s->where('id', $saved_answer_id)->get();
            $s->delete();
        }

        // check login
        if ($self_evaluate == 0)
        {
            $user = check_login('parent');
            $this->session->set_flashdata('user_type', 1);
        }
        else
        {
            $user = check_login('school');
        }
        unset($post['questionnaire_id']);
        unset($post['school_id']);
        unset($post['self_evaluate']);
        unset($post['saved_answer_id']);

        $answers = $this->process_raw_questionnaire($post);
        $clone_answers = $this->arrayCopy($answers);
        // list questions set up from database
        // $current_language = $this->get_current_language();
        // $setup_questions = tbQuestionnaire::get_model()->get_questionnaire($questionnaire_id, $current_language);

        foreach ($clone_answers as $category_id => $questions)
        {
            foreach ($questions as $question_id => $list_answers)
            {
                foreach($list_answers as $k => $v) {
                    if($v == 3) {
                        unset($list_answers[$k]);
                    }
                }
                // get qty of answered answers for this question
                //$qty_choices = $this->separate_into_level($list_answers);
                $qty_answers = count($list_answers);
                if($qty_answers) {
                    $mark = $this->get_mark_for_question2($list_answers, $qty_answers);
                    $answers[$category_id][$question_id] = $mark;
                } else {
                    $answers[$category_id][$question_id] = 1;
                }
            }
        }
		// 2,
		$dataset = array();
		foreach ($answers as $category_id => $questions) 
		{
            $total = $this->calculate_total($questions);
            $dataset[$category_id] = array(
                "total" => $total,
                "average" => round($total/count($questions), 1)
            );
		}
        // save
		$data = array(
			"school_id"=>$school_id, 
			"parent_id"=>($self_evaluate == 0) ? $user["id"] : -1
		);
		$rate_id = tbRate::get_model()->add($data);
		foreach ($dataset as $category => $value) 
		{
			$data = array(
				"rate_id" => $rate_id,
				"category_id" => $category,
				"total" => $value['total'],
				"average_mark" => $value['average'],
			);
			tbRate_category::get_model()->add($data);
		}
        $school = $this->school_model->get_by_attribute(array('t2.user_id' => $school_id));
        $school_id = $school['school_id'];
        $this->session->set_flashdata('school_id', $school_id);
        $this->session->set_flashdata('rate_id', $rate_id);
        add_to_agile($user['email'], "Rate a school");
		redirect(base_url("review/share_review"));
	}

    function save_answered_questions() {
        $post = $this->input->post("values");
        $post = (array) json_decode($post);
        $school = $this->school_model->get_by_attribute(array('user_id'=>$post['school_id']));

        $self_evaluate = $post['self_evaluate'];

        $target_id = NULL;
        if ($self_evaluate == 0) {
            $user = check_login('parent');
            $type = 1;
            $target_id = $post['school_id'];
        }
        else {
            $user = check_login('school');
            $type = 2;
        }
        unset($post['questionnaire_id']);unset($post['school_id']);unset($post['self_evaluate']);unset($post['saved_answer_id']);

        $answers = array();
        $categories = array();
        foreach ($post as $key => $value)
        {
            $data = explode("-", $key);
            if ($data[0] == "category")
            {
                $categories[$data[1]] = $value;
            }
            else
            {
                $answers[$data[3]][$data[1]][$data[2]] = $value;
            }
        }

        $questionnaire = tbQuestionnaire::get_model(array("target_type" => $self_evaluate, "school_grade" => $school['grade']));
        $current_language = $this->get_current_language();
        $total_questions = 0;
        foreach($categories as $category_id => $is_submit) {
            if($is_submit) {
                $questions = tbQuestionnaire::get_model()->get_questionnaire($questionnaire->id, $current_language, $category_id);
                foreach($questions as $question) {
                    $total_answer = count(json_decode($question->answer));
                    $total_questions += $total_answer;
                }
            }
        }

        $total_answered_question = 0;

            $s = new tbSaved_answer();
            $is_exist = $s->where('user_id', $user['id'])->where('target_id', $target_id)->get();

            $answered_question = array();
            foreach ($answers as $category_id => $questions)
            {
                if($categories[$category_id]) {
                    foreach ($questions as $question_id => $list_answers)
                    {
                        $answered_question['answers'][$question_id] = $list_answers;
                        $total_answered_question += count($list_answers);
                    }
                }
            }
            $answered_question['categories'] = $categories;

            $answered_question = json_encode($answered_question);
            $total = $total_answered_question . "/" . $total_questions;
            if($is_exist->exists()) {
                $update = array(
                    'answer' => $answered_question,
                    'total' => $total
                );
                $s->where('user_id', $user['id'])->where('target_id', $target_id)->update($update);
            } else {
                $s->user_id = $user['id'];
                $s->total = $total;
                $s->target_id = $target_id;
                $s->type = $type;
                $s->answer = $answered_question;
                $s->save();
            }
            echo 1;

    }
	/**
	 * 
	 */
	private function arrayCopy( array $array ) {
        $result = array();
        foreach( $array as $key => $val ) {
            if( is_array( $val ) ) {
                $result[$key] = $this->arrayCopy( $val );
            } elseif ( is_object( $val ) ) {
                $result[$key] = clone $val;
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
	}
	/**
	 * 
	 */
	private function get_qty_answer_for_question ($questions, $question_id)
	{
		foreach ($questions as $key => $value) 
		{
			if ($value->question_id == $question_id)
			{
				return count(json_decode($value->answer));
			}
		}
		echo "invalid question_id";
	}
	/**
	 * 
	 */
	private function separate_into_level ($list_answers)
	{
		$data = array();
		$dataset = array();
		foreach ($list_answers as $key => $value) 
		{
			$data[$value][] = 1;
		}
		foreach ($data as $key => $value) {
			$dataset[$key] = count($value);
		}
		return $dataset;
	}
	/**
	 * 
	 */
	private function get_mark_for_question($list_answers, $qty_answers)
	{
		$level1 = intval(@$list_answers[0]);
		$level2 = intval(@$list_answers[1]);
		$level3 = intval(@$list_answers[2]);
		if ($qty_answers < 2)
		{
			return 0;
		}
		else if ($qty_answers == 2)
		{
			if ($level1 == 2)
			{
				return 1;
			}
			else if ($level1 == 1)
			{
				return 2;
			}
			else if ($level2 == 2)
			{
				return 3;
			}
			else if ($level2 == 1 && $level3 == 1)
			{
				return 4;
			}
			else
			{
				return 5;
			}
		}
		else
		{
			$half = 0.5*$qty_answers;
            //echo"<pre>";var_dump($half);die;
			if ($level1 >= $half)
			{
				return 1;
			}
			else if ($level1 >= 1 && $level1 < $half)
			{
				return 2;
			}
			else if ($level1 == 0 && $level2 >= $half) 
			{
				return 3;
			}
			else if ($level1 == 0 && $level2 < $half)
			{
				return 4;
			}
			else
			{
				return 5;
			}
		}
	}

    private function get_mark_for_question2($list_answers, $qty_answers)
    {
        $half = 0.5*$qty_answers;
        $level1 = 0;
        $level2 = 0;
        $level3 = 0;
        foreach($list_answers as $answer) {
            if($answer == 0) {
                $level1++;
            } elseif($answer == 1) {
                $level2++;
            } elseif($answer == 2) {
                $level3++;
            }
        }

        if ($qty_answers < 2) {
            if($level3) {
                return 5;
            } elseif($level2) {
                return 3;
            } else {
                return 1;
            }
        }
        else if ($qty_answers == 2) {
            if($level3 == $qty_answers) {
                return 5;
            } elseif($level3 == 1 && $level2 == 1) {
                return 4;
            } elseif($level2 == 2) {
                return 3;
            } elseif($level1 == 1) {
                return 2;
            } else {
                return 1;
            }
        }
        else {
            if($level3 == $qty_answers) {
                return 5;
            } elseif($level2 < $half && $level3 == ($qty_answers - $level2)) {
                return 4;
            } elseif($level1 == 0 && $level2 >= $half) {
                return 3;
            } elseif($level1 < $half) {
                return 2;
            } else {
                return 1;
            }
        }
    }

	/**
	 * 
	 */
	private function calculate_total($questions)
	{
		$mark = 0;
		foreach ($questions as $value) 
		{
			$mark+= $value;	
		}
		return $mark;
	}
}