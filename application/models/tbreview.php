<?php

class tbReview extends DataMapper {

    var $table = 'tbReviews';
    var $user = 'plusauthentication_user';
    var $school = 'tbSchools';
    var $parent = 'tbParents';
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

    public function get_school_reviews($school_id, $display, $position = 0) {
        $this->db->select("t1.*");
        $this->db->select("t2.username, t2.uuid, t2.avatar");
        $this->db->select("t3.appearance, t3.is_active, t3.full_name");
        $this->db->join("$this->user t2", "t1.user_id = t2.id");
        $this->db->join("tbParents t3", "t1.user_id = t3.user_id");
        $this->db->where('t1.school_id', $school_id);
        if($display) {
            $this->db->limit($display, $position);
        }
        $this->db->order_by('t1.create_time', 'DESC');
        return $this->db->get("$this->table t1")->result_array();
    }

    public function total_reviews($school_id) {
        $this->db->join("$this->user t2", "t1.user_id = t2.id");
        $this->db->join("$this->parent t3", "t2.id = t3.user_id");
        $this->db->where('t1.school_id', $school_id);
        return $this->db->count_all_results("$this->table t1");
    }

    public function get_parent_reviews($user_id, $display = 0, $position = 0) {
        $this->db->select("t1.*");
        $this->db->select("t2.school_name, t3.uuid, t3.avatar");
        $this->db->join("$this->school t2", "t1.school_id = t2.id");
        $this->db->join("$this->user t3", "t2.user_id = t3.id");
        $this->db->where('t1.user_id', $user_id);
        $this->db->where('t2.is_active', 1);
        $this->db->where('t2.appearance', 1);
        if($display) {
            $this->db->limit($display, $position);
        }
        $this->db->order_by('t1.create_time', 'DESC');
        return $this->db->get("$this->table t1")->result_array();
    }

    public function total_parent_reviews($user_id) {
        $this->db->join("$this->user t2", "t1.user_id = t2.id");
        $this->db->join("$this->school t3", "t1.school_id = t3.id");
        $this->db->where('t1.user_id', $user_id);
        return $this->db->count_all_results("$this->table t1");
    }

    public function get_review($attr = array()) {
        $this->db->select("t1.*");
        $this->db->select("t2.username, t2.uuid, t2.avatar, t2.token");
        $this->db->select("t3.appearance, t3.is_active, t3.full_name");
        $this->db->join("$this->user t2", "t1.user_id = t2.id");
        $this->db->join("tbParents t3", "t1.user_id = t3.user_id");
        foreach($attr as $k => $v) {
            $this->db->where($k, $v);
        }
        $this->db->order_by('t1.id', 'DESC');
        $this->db->limit(1);
        return $this->db->get("$this->table t1")->row_array();
    }

}
