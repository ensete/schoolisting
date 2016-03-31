<?php

class tbPlan extends DataMapper {

    var $table = 'tbPlan';
    /**
	 * init model
	 * @param init: dictionary to filter
	 * @param code_to_throw: if any, an exception will be thrown with this code
	 * @access static
	 */
	static function get_model($init = array(), $code_to_throw = FALSE) {
		$model = new tbPlan;
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
	function get_plans_with_basic_info ($school_id, $plan_id = null, $status = 0)
	{
		$this->db->select("plan.id, category.name as category_name, plan.starttime, plan.endtime, plan.status");
		$this->db->select("sum(case when plan_task.status = 1 then 1 else 0 end ) as completed_task, count(plan_task.id) as qty_tasks", FALSE);
		$this->db->from("`tbPlan` plan");
		$this->db->join("tbCategory category", "plan.category_id = category.id", "left");
		$this->db->join("tbPlan_task plan_task", "plan.id = plan_task.plan_id", "left");
		$this->db->where("plan.school_id", $school_id);
		if (isset($plan_id))
		{
			$this->db->where("plan.id", $plan_id);
		}
        if($status) {
            $this->db->where('plan.status', 0);
        }
		$this->db->group_by("plan.id");
		$this->db->order_by("plan.status");
		
		return $this->db->get()->result();
	}
}
