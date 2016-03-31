<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Questionnaire extends MY_Controller {
	var $data = array();
    function __construct ()
    {
        parent::__construct();
		$this->data['admin_title'] = LocalizedString("Questionnaire");
		$this->data['head']['title'] = LocalizedString("Questionnaire");
        $this->data['head']['css'][] = "admin.css";
    }
	
	function index () 
	{
        $this->data['page'] = "admin/main";
		$this->data['admin_header'] = load("admin/header", null, TRUE);
		$this->data['admin_sidebar'] = admin_sidebar(4);
		$this->data['admin_breadcrumb'] = breadcrumb(array("questionnaire"));
		$this->data['admin_content'] = load_table("all-questionnaires");
        load('basic_elements/index', $this->data);
	} 
	/**
	 * show all categories in system
	 * @method public
	 */	
	function all_categories ()
	{
		$this->data['admin_title'] = LocalizedString("Categories");
        $this->data['head']['title'] = LocalizedString("Categories");
        $this->data['admin_breadcrumb'] = breadcrumb(array("Categories"));
		$this->data['page'] = "admin/main";
		$this->data['admin_header'] = load("admin/header", null, TRUE);
		$this->data['admin_sidebar'] = admin_sidebar(5);
		
		$this->data['admin_content'] = load_table("categories");

        load('basic_elements/index', $this->data);
	}

    function category_modal ()
    {
        $id = (int) $this -> input -> get("id");
        $this -> data["content"] = $this -> create_category_form($id);
        load("tablemanagement/libre_elements/modal", $this -> data);
    }

    private function create_category_form($id=-1)
    {
        $category = tbCategory::get_model(array('id' => $id));
        $items = array();
        $items[] = item("hidden", "", "id", null, @$category->id);
        $items[] = item("text", "Name", "name", null, @$category->name);
        $items[] = item("textarea", "Description", "description", null, @$category->description);

        $form = array(
            "action" => "/admin/questionnaire/handle_category_request",
            "submit" => "Submit",
            "items" => $items,
            "error" => ($error = $this -> session -> flashdata("error")) ? $error : null,
            "ajax" => true
        );
        return libre_form($form, "tablemanagement/libre_elements/form");
    }

    function handle_category_request() {
        $input_data = $this->input->post();
        if(!$input_data['name']) {
            echo "Category name must not be left empty";
            return;
        }
        $tbcategory = new tbCategory();
        $all_categories = $tbcategory->get_all_categories();
        foreach($all_categories as $category) {
            if($input_data['id'] != $category['id']) {
                if(strtolower($input_data['name']) == strtolower($category['name'])) {
                    echo "The category name has existed in the database";
                    return;
                }
            }
        }

        if(isset($input_data['id'])) {
            //edit
            $category = tbCategory::get_model(array('id' => $input_data['id']));
        } else {
            //add
            $category = new tbElement();
        }
        $category->name = $input_data['name'];
        $category->description = $input_data['description'];
        $category->save();
        echo "success";
    }

    function mass_delete_category() {
        $ids = $this->input->post("id");
        $tbcategory = new tbCategory();
        $tbcategory->mass_delete($ids);
        redirect("admin/questionnaire/all_categories");
    }

	/**
	 * show all elements in a category
	 * @method public
	 */
	function all_elements ()
	{
		$category_id = (int) $this->input->get("id");
		$category = tbCategory::get_model(array("id"=>$category_id));
		if (!$category->exists()) redirect("questionnaire/all_categories");
		
		$this->data['admin_title'] = LocalizedString("$category->name Elements");
        $this->data['head']['title'] = LocalizedString("$category->name Elements");
        $this->data['admin_breadcrumb'] = breadcrumb(array("Categories", "Elements"));
		$this->data['page'] = "admin/main";
		$this->data['admin_header'] = load("admin/header", null, TRUE);
		$this->data['admin_sidebar'] = admin_sidebar(5);

		$this->data['admin_content'] = load_table("elements_by_category", array("category_id" => $category_id));

        load('basic_elements/index', $this->data);
	}

    function element_modal ()
    {
        $id = (int) $this -> input -> get("id");
        $category_id = (int) $this -> input -> get("category_id");
        $this -> data["content"] = $this -> create_element_form($id, $category_id);
        load("tablemanagement/libre_elements/modal", $this -> data);
    }

    private function create_element_form($id=-1, $category_id = 0)
    {
        $element = tbElement::get_model(array('id' => $id));
        $items = array();
        if($category_id) {
            $items[] = item("hidden", "", "category_id", null, $category_id);
        } else {
            $items[] = item("hidden", "", "id", null, @$element->id);
        }
        $items[] = item("text", "Name", "name", null, @$element->name);
        $items[] = item("textarea", "Description", "description", null, @$element->description);

        $form = array(
            "action" => "/admin/questionnaire/handle_element_request",
            "submit" => "Submit",
            "items" => $items,
            "error" => ($error = $this -> session -> flashdata("error")) ? $error : null,
            "ajax" => true
        );
        return libre_form($form, "tablemanagement/libre_elements/form");
    }

    function handle_element_request() {
        $input_data = $this->input->post();

        if(!$input_data['name']) {
            echo "Element name must not be left empty";
            return;
        }
        $tbelement = new tbElement();
        $all_elements = $tbelement->get_all_elements();

        if(isset($input_data['id'])) {
            //edit
            $element = tbElement::get_model(array('id' => $input_data['id']));
            foreach($all_elements as $el) {
                if($input_data['id'] != $el['id']) {
                    if (strtolower($input_data['name']) == strtolower($el['name'])) {
                        echo "The element name has existed in the database";
                        return;
                    }
                }
            }
        } else {
            //add
            $element = new tbElement();
            $element->category_id = $input_data['category_id'];
            foreach($all_elements as $el) {
                if (strtolower($input_data['name']) == strtolower($el['name'])) {
                    echo "The element name has existed in the database";
                    return;
                }
            }
        }
        $element->name = $input_data['name'];
        $element->description = $input_data['description'];
        $element->save();
        echo "success";
    }

    function mass_delete_el() {
        $ids = $this->input->post("id");
        $category = tbElement::get_model(array('id' => $ids[0]));
        $tbelement = new tbElement();
        $tbelement->mass_delete($ids);
        redirect("admin/questionnaire/all_elements?id=$category->category_id");
    }
	/**
	 * 
	 */
	function show_question ()
	{
		$element_id = (int) $this->input->get("id");
		$language_id = (int) $this->input->get("language");
		
		$language_id = $this->get_active_language($language_id);
		$element = tbElement::get_model()->get_element($element_id, $language_id);
		
		if (!isset($element[0])) 
		{
			redirect("admin/questionnaire/all_categories");
		}
		else
		{
			$element = $element[0];
		}	
		
		$this->data['admin_title'] = LocalizedString("Questions");
        $this->data['head']['title'] = LocalizedString("Questions");
		$this->data['head']['css'][] = "summernote.min.css";
		$this->data['head']['css'][] = "typeahead.min.css";
		$this->data['head']['css'][] = "select2.min.css";
		$this->data['head']['css'][] = "xeditable.min.css";
		
		$this->data['head']['js'][] = "summernote.min.js";
		$this->data['head']['js'][] = "moment.min.js";
		$this->data['head']['js'][] = "custom/editor.js";
		$this->data['head']['js'][] = "handlebars.min.js";
		$this->data['head']['js'][] = "custom/editor.js";
		$this->data['head']['js'][] = "typeahead.bundle.min.js";
		$this->data['head']['js'][] = "bootstrap-editable.min.js";
		$this->data['head']['js'][] = "bootstrap-editable-typeaheadjs.min.js";
		$this->data['head']['js'][] = "bootstrap-editable-address.min.js";
		$this->data['head']['js'][] = "custom/editor.js";

        $this->data['admin_breadcrumb'] = breadcrumb(array("Categories", "Elements"));
		$this->data['page'] = "admin/main";
		$this->data['admin_header'] = load("admin/header", null, TRUE);
		$this->data['admin_sidebar'] = admin_sidebar(5);
		$this->data['admin_content'] = load("admin/question", array(
				"element_id"=>$element_id,
				"languages" => $this->get_supported_languages(),
				"question" => $element->question,
				"answers" => json_decode($element->answer) ? json_decode($element->answer) : array(),
				"language_id" => $language_id
			), true);

        load('basic_elements/index', $this->data);
	}
	/**
	 * 
	 */
	public function save_question ()
	{
		$question = $this->input->post("question");
		$element_id = $this->input->post("element_id");
		$language_id = $this->input->post("language_id");
		$answers = json_decode($this->input->post("answer"));
		
		try {
			$answer = $this->convert_list_answer_into_group_answer ($answers);

			$element = tbElement::get_model(array("id"=>$element_id), 0);

			$element_detail = tbElement_detail::get_model(array("element_id"=>$element_id, "language_id"=>$language_id));
			$element_detail->question = $question;
			$element_detail->answer = json_encode($answer);
			$element_detail->language_id = $language_id;
			$element_detail->element_id = $element_id;
			
			$element_detail->save();
			
			redirect($_SERVER['HTTP_REFERER']);
		} catch (Exception $e) {
			echo $e->getCode();
		}
	}
	/**
	 * 
	 */
	protected function convert_list_answer_into_group_answer ($answers = array())
	{
		$dataset = array();
		$group = array();
		foreach ($answers as $key => $ans) 
		{
			$group[] = $ans;
			if ((($key + 1) % 3) == 0)
			{
				$dataset[] = $group;
				$group = array();
			}
		}
		return $dataset;
	}
	/**
	 * 
	 */
	protected function get_supported_languages ()
	{
		$this->db->from("pluslocalization_supported");
	    $l = $this->db->get()->result_object();
	    $data = array();
	    foreach ($l as $key => $value)
	    {
	      	$data+= array(
	      		$value->id => $value->long_name
			);
	    }
		return $data;
	}
	/**
	 * 
	 */
	protected function get_active_language ($language_id = null)
	{
		$CI =& get_instance();
		$CI->load->model('pluslocalization/pluslocalization_supported');
		$language = $CI->pluslocalization_supported->get_by_attribute(array('id' => $language_id));
		if ($language === FALSE)
		{
			$primary = $CI->pluslocalization_supported->get_by_attribute(array('is_primary' => 1));
			if ($primary === FALSE)
			{
				$language_id = 1;
			}
			else
			{
				$language_id = $primary[0]->id;
			}
		}
		return $language_id;
	}
	/**
	 * 
	 */
	function process_questionnaire ()
	{
		$id = (int) $this->input->get("id");
		$this -> data["content"] = $this -> create_form_questionnaire($id);
		load("tablemanagement/libre_elements/modal", $this -> data);
	}
	/**
	 * @param void
	 * @return form to add to the content of libre_elements/modal
	 * @access public
	 */	
	public function create_form_questionnaire($id = -1)
	{
		$form = array(
			"action" => "/admin/questionnaire/handle_process_questionnaire", 
			"submit" => ($id == -1) ? "Add" : "Save changes", 
			"items" => $this -> form_questionnaire($id),
			"error" => ($error = $this -> session -> flashdata("error")) ? $error : null,
			"ajax" => true
		);
		return libre_form($form, "tablemanagement/libre_elements/form");
	}
	/**
	 * @param void
	 * @return form to add to the items of form element
	 * @access protected
	 */
	protected function form_questionnaire($id = -1)
	{
		$questionnaire = tbQuestionnaire::get_model(array("id"=>$id));
		$items = array();
		$items[] = item("hidden", "", "questionnaire_id", null, $questionnaire->id);
		$items[] = item("text", "Name *", "name", null, $questionnaire->name);
		$items[] = item("textarea", "Description", "description", null, $questionnaire->description);
		
		$target_type = array(
			array(
				'title' => LocalizedString('Parent'),
				'value' => 0,
				'checked' => ($questionnaire->target_type == 0) ? TRUE : FALSE
			),
			array(
				'title' => LocalizedString('School'),
				'value' => 1,
				'checked' => ($questionnaire->target_type == 1) ? TRUE : FALSE
			),
		);
		$items[] = item("radio", "Target *", "target_type", $target_type);
		
		$grade = $this->dropdown_elements;
		$grades = $grade["grade"]["values"];
		$items[] = item("select", "Grade *", "grade", $grades, $questionnaire->school_grade);
		return $items;
	}
	/**
	 * 
	 */
	function handle_process_questionnaire ()
	{
		$id = $this->input->post("questionnaire_id");
		$name = $this->input->post("name");
		$description = $this->input->post("description");
		$target_type = $this->input->post("target_type");
		$grade = $this->input->post("grade");
		
		try {
			$this->load->helper('email');
			//
			if (strlen($name) == 0)
			{
				echo "Missing params.";
				return;
			}

			$questionnaire = new tbQuestionnaire;
			$questionnaire->where("id <>", $id);
			$questionnaire->where("target_type", $target_type);
			$questionnaire->where("school_grade", $grade);
			$questionnaire->get();
			
			if ($questionnaire->exists())
			{
				echo "A questionnaire for this target type and school's grade currently exists.";
				return;
			}
			
			$questionnaire = tbQuestionnaire::get_model(array("id"=>$id));
			$questionnaire->name = $name;
			$questionnaire->description = $description;
			$questionnaire->target_type = $target_type;
			$questionnaire->school_grade = $grade;
			$questionnaire->save();
			echo "success";
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	/**
	 * 
	 */
	function manage_questionnaire ()
	{
		$id = $this->input->get("id");
		$questionnaire = tbQuestionnaire::get_model(array("id"=>$id));
		if (!$questionnaire->exists())
		{
			redirect("/admin/questionnaire");
		}

		$this->data['admin_title'] = LocalizedString("Questionnaire");
        $this->data['head']['title'] = LocalizedString("Questionnaire");
        $this->data['admin_breadcrumb'] = breadcrumb(array("Questionnaires"));
		$this->data['page'] = "admin/main";
		$this->data['admin_header'] = load("admin/header", null, TRUE);
		$this->data['admin_sidebar'] = admin_sidebar(4);
		$this->data['admin_content'] = load("admin/questionnaire_question", array(
				"questionnaire_id"=>$id,
				"questionnaire_name" => $questionnaire->name,
				"categories" => tbCategory::get_model()->get_categories_detail(),
				"selected_questions" => tbQuestionnaire_question::get_model()->get_list_question_as_array($id)
			), true);

        load('basic_elements/index', $this->data);	
	}
	/**
	 * 
	 */
	function handle_manage_questionnaire ()
	{
		$id = (int) $this->input->post("questionnaire_id");
		$selected_element = $this->input->post("selected_element");
		
		$questionnaire = tbQuestionnaire::get_model(array("id"=>$id));
		if (!$questionnaire->exists())
		{
			redirect("/admin/questionnaire");
		}
		
		$questionnaire_question = new tbQuestionnaire_question();
		$questionnaire_question->where('questionnaire_id', $id);
		$questionnaire_question->get();
		
		$questionnaire_question->delete_all();
		foreach ($selected_element as $element_id)
		{
			$qq = new tbQuestionnaire_question();
			$qq->questionnaire_id = $id;
			$qq->question_id = $element_id;
			$qq->save();
		}
        $this->session->set_flashdata('success', "Everything was saved successfully");
		redirect($_SERVER['HTTP_REFERER']);
	}
	/**
	 * 
	 */
	function all_tasks ()
	{
		$element_id = (int) $this->input->get("id");
		$element = tbElement::get_model(array("id"=>$element_id));
		if (!$element->exists()) redirect("questionnaire/all_categories");

        $element_detail = tbElement_detail::get_model(array("id"=>$element_id));
        $answer = json_decode($element_detail->answer);
        $answer_html = "<b style='text-decoration: underline'>Answer: </b>";

        $answer_html .= '
        <div class="table-responsive">
            <table style="table-layout: fixed" id="answer" class="table table-bordered table-striped">
                <tbody>';
        foreach($answer as $value) {
            $answer_html .= "<tr>";
            foreach($value as $v) {
                $answer_html .= '<td>'.LocalizedString($v).'</td>';
            }
            $answer_html .= "</tr>";
        }
        $answer_html .='</tbody>
            </table>
        </div>
        ';
        //echo"<pre>";var_dump($answer);die;
		
		$this->data['admin_title'] = LocalizedString("Tasks - " . LocalizedString($element->name));
        $this->data['head']['title'] = LocalizedString("Tasks");
        $this->data['admin_breadcrumb'] = breadcrumb(array("Categories", "Elements", "Tasks"));
		$this->data['page'] = "admin/main";
		$this->data['admin_header'] = load("admin/header", null, TRUE);
		$this->data['admin_sidebar'] = admin_sidebar(5);

        $this->data['admin_content'] = "<b><span style='text-decoration: underline'>".LocalizedString("Question")."</span>: " . LocalizedString($element_detail->question) . "</b>";
        $this->data['admin_content'] .= $answer_html;
        $this->data['admin_content'] .= "<div style='margin-bottom: 20px'></div>";
		$this->data['admin_content'] .= load_table("all-tasks", array("element_id" => $element_id));

        load('basic_elements/index', $this->data);
	}
	/**
	 * 
	 */
	function process_task ()
	{
		$element_id = (int) $this->input->get("element_id");
		$this -> data["content"] = $this -> create_form_task($element_id);
		load("tablemanagement/libre_elements/modal", $this -> data);
	}
	/**
	 * @param void
	 * @return form to add to the content of libre_elements/modal
	 * @access public
	 */	
	public function create_form_task($element_id = -1)
	{
		$form = array(
			"action" => "/admin/questionnaire/handle_process_task", 
			"submit" => "Add", 
			"items" => $this -> form_task($element_id),
			"error" => ($error = $this -> session -> flashdata("error")) ? $error : null,
			"ajax" => true
		);
		return libre_form($form, "tablemanagement/libre_elements/form");
	}
	/**
	 * @param void
	 * @return form to add to the items of form element
	 * @access protected
	 */
	protected function form_task($element_id = -1)
	{
		// $questionnaire = tbQuestionnaire::get_model(array("id"=>$id));
		$items = array();
		$items[] = item("hidden", "", "element_id", null, $element_id);
		$items[] = item("text", "Name *", "name", null);
		$items[] = item("textarea", "Description", "description", null);
		
		return $items;
	}
	/**
	 * 
	 */
	function handle_process_task ()
	{
		$element_id = $this->input->post('element_id');
		$name = $this->input->post('name');
		$description = $this->input->post('description');
		
		try {
			if (strlen($name) == 0)
			{
				echo "Name cannot be empty";
				return;
			}
			
			$task = new tbTask();
			$task->name = LocalizedString($name);
			$task->description = $description;
			$task->element_id = $element_id;
			$task->save();
			
			echo 'success';
		} catch (Exception $e) {
			echo $e->getMessage();	
		}
	}
	
}