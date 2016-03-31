<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Planaction extends MY_Controller {
	function add ()
	{
		$school = check_login('school');
        $school = $this->school_model->get_by_attribute(array('t2.user_id' => $school['id']));

		$data = array();
		$data['user'] = $school;
        $data['current_id'] = $school["id"];
        $data['page'] = "school/planaction_form";
        $data['has_footer'] = TRUE;
        $data['has_header'] = TRUE;
        $data['head']['title'] = LocalizedString("Create new plan");
		
		$data['head']['css'][] = "select2.min.css";
		$data['head']['css'][] = "wizard.min.css";
		$data['head']['css'][] = "selectboxit.min.css";
		$data['head']['css'][] = "daterangepicker.min.css";
		
		$data['head']['js'][] = "select2.min.js";
		$data['head']['js'][] = "moment.min.js";
		$data['head']['js'][] = "handlebars.min.js";
		$data['head']['js'][] = "jquery.bootstrap.wizard.min.js";
		$data['head']['js'][] = "jquery.selectBoxIt.min.js";
		$data['head']['js'][] = "daterangepicker.min.js";
		$data['head']['js'][] = "devs/planaction_form.js";

        $tbcategory = new tbCategory();
        $school_categories = $tbcategory->get_proper_categories($school['grade']);
		$data["categories"] = $school_categories;

		$data["error"] = $this->session->flashdata('error');
		
        $this->load->view('basic_elements/index', $data);
	}
	/**
	 * get elements html for selecting tasks
	 */
	function get_element_by_category_to_html ()
	{
		$category_id = (int) $this->input->post('category_id');
		$elements = tbElement::get_model(array('category_id'=>$category_id));
        $element_id = array();
        foreach($elements as $element) {
            $element_id[] = $element->id;
        }

        $tbtask = new tbTask();
        $tasks = $tbtask->get_tasks($element_id);
		load("school/selecting_task_view", array("tasks"=>$tasks));
	}
	/**
	 * 
	 */
	function handle_add ()
	{
        //echo"<pre>";var_dump($this->input->post());die;
		$category_id = $this->input->post("category");
		$date_range = explode("-", $this->input->post("date_range"));
		
		$starttime = date('Y-m-d', strtotime($date_range[0]));
		$endtime = date('Y-m-d', strtotime($date_range[1]));
		$selected_element = $this->input->post("selected_element");
		
		$school = check_login('school');
		
		$plan = tbPlan::get_model(array("school_id" => $school["id"], "category_id" => $category_id, "status"=>0));
		
		if ($plan->exists())
		{
			$this->session->set_flashdata('error', 'You currently have an active plan with this category!');
			redirect($_SERVER['HTTP_REFERER']);
		}
		
		$plan_id = tbPlan::get_model()->add(array("school_id" => $school["id"], "category_id" => $category_id, "starttime"=>$starttime, "endtime"=>$endtime));
		foreach ($selected_element as $task_id)
		{
			tbPlan_task::get_model()->add(array("plan_id"=>$plan_id, "task_id"=>$task_id));
		}
		redirect("/school/profile/{$school['uuid']}/plan-actions/");
	}
	/**
	 * 
	 */
	function close_plan ($id = -1)
	{
		$plan = new tbPlan($id);
		if ($plan->exists())
		{
			$plan->status = 1;
			$plan->save();
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	/**
	 * 
	 */
	function check_list ($id = -1)
	{
		$plan = new tbPlan($id);
		$tasks = tbPlan_task::get_model()->get_tasks_for_plan($id);
		
		$data = array();
		$data["content"] = load("school/plan_task", array("tasks"=>$tasks, "plan_status"=>$plan->status, "plan_id"=>$plan->id), true);
		load("tablemanagement/libre_elements/modal", $data);
	}
	/**
	 * 
	 */
	// function toggle_status_plantask ()
	// {
		// $school = check_login("school", true);
  		// $plan_task_id =	$this->input->post('plan_task_id');
		// $status = $this->input->post('status');
// 		
		// $plan_task = new tbPlan_task($plan_task_id);
		// if ($plan_task->exists())
		// {
			// $plan_task->status = $status;
			// $plan_task->save();
			// // check if automatically complete
			// if ($status == 1)
			// {
				// $plan = tbPlan::get_model()->get_plans_with_basic_info($school["id"], $plan_task->plan_id);
				// if ($plan[0]->completed_task == $plan[0]->qty_tasks)
				// {
					// $p = new tbPlan($plan[0]->id);
					// $p->status = 2;
					// $p->save();
				// }
			// }
		// } 
	// }
	/**
	 * 
	 */
	function copy_plan ($id = -1)
	{
		$plan = new tbPlan($id);
		if ($plan->exists() && $plan->status == 1)
		{
			$check_plan = tbPlan::get_model(array("school_id" => $plan->school_id, "category_id" => $plan->category_id, "status"=>0));
		
			if ($check_plan->exists())
			{
				$this->session->set_flashdata('error', 'You currently have an active plan with this category!');
				redirect($_SERVER['HTTP_REFERER']);
			}
			
			$plan_id = tbPlan::get_model()->add(array("category_id"=>$plan->category_id, "status"=>0, "school_id"=>$plan->school_id, "starttime"=>$plan->starttime, "endtime"=>$plan->endtime));
			$tasks = tbPlan_task::get_model(array("plan_id"=>$plan->id));
			foreach ($tasks as $task) 
			{
				$plan_task = tbPlan_task::get_model()->add(array("plan_id"=>$plan_id, "task_id"=>$task->task_id, "status"=>$task->status));	
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	/**
	 * 
	 */
	function submit_plan_task ()
	{
		$school = check_login("school");
		$task = $this->input->post('task');
		$plan_id = $this->input->post('plan_id');
		
		$plan = new tbPlan($plan_id);
		if (!$plan->exists()) redirect("/");
		
		$tasks = tbPlan_task::get_model(array("plan_id"=>$plan_id));
		
		foreach ($tasks as $t) 
		{
			if (in_array($t->id, $task))
			{
				$t->status = 1;
				$t->save();
			}	
			else
			{
				$t->status = 0;
				$t->save();
			}
			$plan = tbPlan::get_model()->get_plans_with_basic_info($school["id"], $plan_id);
			if ($plan[0]->completed_task == $plan[0]->qty_tasks)
			{
				$p = new tbPlan($plan[0]->id);
				$p->status = 2;
				$p->save();
			}
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
}