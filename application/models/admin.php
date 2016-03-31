<?php

class Admin extends DataMapper
{

    var $table = 'plusauthentication_user';
    var $admin = 'tbAdmins';

    function get_user_by_attribute($attr = array()) {
        $this->db->select("id, email, username, token, uuid, display_name, avatar");
        foreach($attr as $key => $val) {
            $this->db->where($key, $val);
        }
        return $this->db->get($this->table)->row_array();
    }

    function get_admin($attr = array()) {
        foreach($attr as $key => $value) {
            $this->db->where($key, $value);
        }
        return $this->db->get($this->admin)->row_array();
    }

    function get_admin_by_token($token) {
        if($token) {
            $this->db->where('token', $token);
            return $this->db->get($this->admin)->row_array();
        } else {
            return FALSE;
        }
    }

    function update_admin($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->admin, $data);
    }

    /**
     * @param $type: 0 => school, 1 => parent
     * @param int $month
     * @param int $year
     * @return mixed
     */
    public function get_latest_newccomers($type, $month = 0, $year = 0) {
        $this->db->from("$this->table");
        if($type == 1) {
            $this->db->where('user_type', 'parent');
        } else {
            $this->db->where('user_type', 'school');
        }
        if($month) {
            $this->db->where('MONTH(createtime)', $month);
            $this->db->where('YEAR(createtime)', $year);
        }
        return $this->db->count_all_results();
    }

    public function get_school_total_view() {
        $this->db->select('COALESCE(SUM(total_views),0) as number', false);
        return $this->db->get("tbSchools")->row_array();
    }

    public function get_school_total_recommendation() {
        $this->db->select('COUNT(id) as number');
        return $this->db->get("tbRecommendation")->row_array();
    }

    public function get_inactive_schools() {
        $this->db->select('COUNT(id) as total');
        $this->db->where('latest_login <', 'NOW() - INTERVAL 3 MONTH', false);
        return $this->db->get("tbSchools")->row_array();
    }

    /**
     * @param $type: 0 = school, 1 = parent
     * @param int $year
     * @return mixed
     */
    public function get_newcomers($type) {
        $this->db->from("$this->table");
        if($type == 1) {
            $this->db->where('user_type', 'parent');
        } else {
            $this->db->where('user_type', 'school');
        }
        $this->db->where('YEAR(createtime)', 'YEAR(CURDATE())', false);
        return $this->db->count_all_results();
    }

    function get_improved_schools($month = 0, $year = 0) {
        if($month == 1) {
            $last_month = 12;
            $relative_year = $year - 1;
        } else {
            $last_month = $month - 1;
            $relative_year = $year;
        }
        $this->db->select("CASE WHEN COALESCE((SUM(t2.average_mark) / COUNT(distinct t1.id)),0) > COALESCE((SUM(t3.average_mark) / COUNT(distinct t1.id)),0) THEN t1.school_id END as school_id", false);
        $this->db->from("(SELECT * FROM (SELECT * FROM tbRate ORDER BY id DESC) tbRate GROUP BY school_id, parent_id ) t1");
        $this->db->join("tbRate_category t2", "t1.id = t2.rate_id AND MONTH(t1.createtime) = $month AND YEAR(t1.createtime) = $year");
        $this->db->join("tbRate_category t3", "t1.id = t3.rate_id AND MONTH(t1.createtime) = $last_month AND YEAR(t1.createtime) = $relative_year", "left");
        $this->db->group_by('t1.school_id');
        return $this->db->get()->result_array();
    }

    function get_target_numbers($data) {
        $this->db->select('t1.createtime, t1.user_type, t2.id');
        $this->db->from("$this->table t1");
        if($data['user_type'] == "parent") {
            $this->db->select('t2.full_name as name');
            $this->db->join("tbParents t2", "t1.id = t2.user_id");
        } else {
            $this->db->select('t2.school_name as name');
            $this->db->join("tbSchools t2", "t1.id = t2.user_id");
        }
        if($data['city']) {
            $this->db->where('t2.city', $data['city']);
        }
        if($data['district']) {
            $this->db->where('t2.district', $data['district']);
        }
        $this->db->where('t1.user_type', $data['user_type']);
        return $this->db->get()->result_array();
    }

    function get_plans($status) {
        $this->db->from("tbPlan");
        $this->db->where('status', $status);
        return $this->db->count_all_results();
    }

    function get_total_school_with_completed_plan() {
        $this->db->from("tbPlan t1");
        $this->db->where('t1.status', 0);
        $this->db->group_by("t1.school_id");
        return $this->db->count_all_results();
    }
}