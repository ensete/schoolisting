<?php
class Parent_model extends CI_Model {

    private $parent = "tbParents";
    private $school = "tbSchools";
    private $user = "plusauthentication_user";
    private $image = "tbImage";
    private $city = "tbCities";
    private $district = "tbDistricts";
    private $methods = "tbKnown_methods";
    private $like = "tbLike";
    private $bookmark = "tbBookmarks";
    private $recommendation = "tbRecommendation";

    public function get_by_attribute($attr = array()) {
        $this->db->select("t1.id, google_id, active, email, username, password, token, uuid, display_name, avatar");
        $this->db->select("t2.id as parent_id, lat, lng, full_name, telephone, city, district, children_numbers, appearance, is_active");
        $this->db->join("$this->parent t2", "t1.id = t2.user_id", 'left');
        foreach($attr as $key => $val) {
            $this->db->where($key, $val);
        }
        return $this->db->get("$this->user t1")->row_array();
    }

    public function get_parent_location($district_id = 0) {
        if($district_id) {
            $this->db->select('t1.name as district_name, t2.name as city_name');
            $this->db->join("$this->city t2", "t1.city_id = t2.id");
            $this->db->where('t1.id', $district_id);
            return $this->db->get("$this->district t1")->row_array();
        } else {
            return NULL;
        }
    }

    public function get_user_id_by_parent_id($ids = array()) {
        $this->db->select("t1.id");
        $this->db->join("$this->parent t2", "t1.id = t2.user_id");
        $this->db->where_in('t2.id', $ids);
        return $this->db->get("$this->user t1")->result_array();
    }

    public function get_user_id_by_school_id($ids = array()) {
        $this->db->select("t1.id");
        $this->db->join("$this->school t2", "t1.id = t2.user_id");
        $this->db->where_in('t2.id', $ids);
        return $this->db->get("$this->user t1")->result_array();
    }

    public function get_all_users($attr = array()) {
        foreach($attr as $k => $v) {
            $this->db->where($k, $v);
        }
        return $this->db->get($this->user)->result_array();
    }

    public function get_bookmarks($user_id, $current_user_id, $display, $position = 0) {
        $this->db->select("t1.*, t1.id as school_id");
        $this->db->select("t2.avatar, t2.uuid, t2.username");
        $this->db->select("t4.name as district, t5.name as city");

        $this->db->select("COUNT(DISTINCT t6.id) as like_count");
        $this->db->select("COUNT(DISTINCT t7.id) as bookmark_count");
        $this->db->select("COUNT(DISTINCT t8.id) as recommend_count");

        $this->db->select("t9.id as is_liked");
        $this->db->select("t10.id as is_bookmarked");
        $this->db->select("t11.id as is_referred");

        $this->db->select("t12.create_time");

        $this->db->join("$this->user t2", "t1.user_id = t2.id");
        $this->db->join("$this->district t4", "t1.district = t4.id");
        $this->db->join("$this->city t5", "t4.city_id = t5.id");

        $this->db->join("$this->like t6", "t1.id = t6.school_id", "left");
        $this->db->join("$this->bookmark t7", "t1.id = t7.school_id", "left");
        $this->db->join("$this->recommendation t8", "t1.id = t8.school_id", "left");

        $this->db->join("$this->like t9", "t1.id = t9.school_id AND t9.user_id = $current_user_id", "left");
        $this->db->join("$this->bookmark t10", "t1.id = t10.school_id AND t10.user_id = $current_user_id", "left");
        $this->db->join("$this->recommendation t11", "t1.id = t11.school_id AND t11.user_id = $current_user_id", "left");

        $this->db->join("$this->bookmark t12", "t1.id = t12.school_id AND t12.user_id = $user_id");
        $this->db->order_by('t12.create_time', 'DESC');

        $this->db->where("t1.is_active", 1);
        $this->db->where("t1.appearance", 1);
        $this->db->where("t2.active", 1);
        $this->db->group_by('t1.id');
        if($display != 0) {
            $this->db->limit($display, $position);
        }
        return $this->db->get("$this->school t1")->result_array();
    }

    public function get_recommendations($user_id, $current_user_id, $display, $position = 0) {
        $this->db->select("t1.*, t1.id as school_id");
        $this->db->select("t2.avatar, t2.uuid, t2.username");
        $this->db->select("t4.name as district, t5.name as city");

        $this->db->select("COUNT(DISTINCT t6.id) as like_count");
        $this->db->select("COUNT(DISTINCT t7.id) as bookmark_count");
        $this->db->select("COUNT(DISTINCT t8.id) as recommend_count");

        $this->db->select("t9.id as is_liked");
        $this->db->select("t10.id as is_bookmarked");
        $this->db->select("t11.id as is_referred");

        $this->db->select("t12.create_time");

        $this->db->join("$this->user t2", "t1.user_id = t2.id");
        $this->db->join("$this->district t4", "t1.district = t4.id");
        $this->db->join("$this->city t5", "t4.city_id = t5.id");

        $this->db->join("$this->like t6", "t1.id = t6.school_id", "left");
        $this->db->join("$this->bookmark t7", "t1.id = t7.school_id", "left");
        $this->db->join("$this->recommendation t8", "t1.id = t8.school_id", "left");

        $this->db->join("$this->like t9", "t1.id = t9.school_id AND t9.user_id = $current_user_id", "left");
        $this->db->join("$this->bookmark t10", "t1.id = t10.school_id AND t10.user_id = $current_user_id", "left");
        $this->db->join("$this->recommendation t11", "t1.id = t11.school_id AND t11.user_id = $current_user_id", "left");

        $this->db->join("$this->recommendation t12", "t1.id = t12.school_id AND t12.user_id = $user_id");
        $this->db->order_by('t12.create_time', 'DESC');

        $this->db->where("t1.is_active", 1);
        $this->db->where("t1.appearance", 1);
        $this->db->where("t2.active", 1);
        $this->db->group_by('t1.id');
        if($display != 0) {
            $this->db->limit($display, $position);
        }
        return $this->db->get("$this->school t1")->result_array();
    }

    public function update_user($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->user, $data);
    }

    public function get_avatar($image_id) {
        $this->db->select("big_image");
        $this->db->where('id', $image_id);
        return $this->db->get($this->image)->row_array();
    }

    public function insert_parent($data) {
        $this->db->insert($this->parent, $data);
    }

    public function update_parent($user_id, $data) {
        $this->db->where('user_id', $user_id);
        $this->db->update($this->parent, $data);
    }

    public function get_cities() {
        return $this->db->get($this->city)->result_array();
    }

    public function get_districts($city_id) {
        $this->db->select("id, name");
        $this->db->where('city_id', $city_id);
        return $this->db->get($this->district)->result_array();
    }

    public function find_location_by_district($district_name) {
        $this->db->select("id as district_id, city_id");
        $this->db->where('name', $district_name);
        return $this->db->get($this->district)->row_array();
    }

    public function find_location_by_city($city_name) {
        $this->db->select("id as city_id");
        $this->db->where('name', $city_name);
        return $this->db->get($this->city)->row_array();
    }

    public function find_default_district_by_city($city_id) {
        $this->db->select("id as district_id");
        $this->db->where('city_id', $city_id);
        $this->db->limit(1);
        return $this->db->get($this->district)->row_array();
    }

    public function delete_parent($ids = array()) {
        $this->db->where_in('id', $ids);
        $this->db->update($this->parent, array('is_active' => 0));
    }

    public function approve_user($ids = array()) {
        $this->db->where_in('id', $ids);
        $this->db->update($this->user, array('active' => 1));
    }

    public function insert_method($data) {
        $this->db->insert($this->methods, $data);
    }
}