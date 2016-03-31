<?php

class tbPlan_task extends DataMapper {

    var $table = 'tbPlan_task';
    /**
	 * init model
	 * @param init: dictionary to filter
	 * @param code_to_throw: if any, an exception will be thrown with this code
	 * @access static
	 */
	static function get_model($init = array(), $code_to_throw = FALSE) {
		$model = new tbPlan_task;
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
	/**
	 * 
	 */
	function get_tasks_for_plan ($plan_id)
	{
		$this->db->select("plan_task.id, task.name, plan_task.status");
		$this->db->from("`tbPlan_task` plan_task");
		$this->db->join("tbTask task", "plan_task.task_id = task.id", "left");
		$this->db->where("plan_task.plan_id", $plan_id);
		
		return $this->db->get()->result();
	}
}
