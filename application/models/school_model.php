<?php
class School_model extends CI_Model {

    private $user = "plusauthentication_user";
    private $school = "tbSchools";
    private $menu = "tbSchool_menus";
    private $language = "tbSchool_languages";
    private $city = "tbCities";
    private $district = "tbDistricts";
    private $like = "tbLike";
    private $bookmark = "tbBookmarks";
    private $recommendation = "tbRecommendation";

    public function get_by_attribute($attr = array()) {
        $this->db->select("active, email, username, password, token, uuid, display_name, avatar, customers_emails");
        $this->db->select("t2.*, t2.id as school_id, t2.user_id as id, school_name, lat, lng");
        $this->db->select("group_concat(DISTINCT  t3.menu) as menus");
        $this->db->select("group_concat(DISTINCT  t4.language) as languages");
        $this->db->join("$this->school t2", "t1.id = t2.user_id", 'left');
        $this->db->join("$this->menu t3", "t2.id = t3.school_id", 'left');
        $this->db->join("$this->language t4", "t2.id = t4.school_id", 'left');
        foreach($attr as $key => $val) {
            $this->db->where($key, $val);
        }
        $this->db->order_by('t1.id');
        return $this->db->get("$this->user t1")->row_array();
    }

    public function get_all_schools() {
        return $this->db->get($this->school)->result_array();
    }

    public function insert_school($data) {
        $this->db->insert($this->school, $data);
        return $this->db->insert_id();
    }

    public function insert_school_menu($data) {
        $this->db->insert($this->menu, $data);
    }

    public function insert_school_language($data) {
        $this->db->insert($this->language, $data);
    }

    public function update_school($user_id, $data) {
        $this->db->where('user_id', $user_id);
        $this->db->update($this->school, $data);
    }

    function delete_menu($school_id) {
        $this->db->where('school_id', $school_id);
        $this->db->delete($this->menu);
    }

    function delete_language($school_id) {
        $this->db->where('school_id', $school_id);
        $this->db->delete($this->language);
    }

    public function delete_school($ids = array()) {
        $this->db->where_in('id', $ids);
        $this->db->update($this->school, array('is_active' => 0));
    }

    /**
     * @param $type = city, district
     * @return mixed
     */
    public function search_school_by_location($key, $type, $display, $position = 0, $user_id = 0) {
        $this->db->select("t2.*, t2.id as school_id");
        $this->db->select("t3.avatar, t3.uuid, t3.username");
        $this->db->select("t4.name as district, t5.name as city");

        $this->db->select("COUNT(DISTINCT t6.id) as like_count");
        $this->db->select("COUNT(DISTINCT t7.id) as bookmark_count");
        $this->db->select("COUNT(DISTINCT t8.id) as recommend_count");

        $this->db->select("t9.id as is_liked");
        $this->db->select("t10.id as is_bookmarked");

        $this->db->join("$this->school t2", "t1.id = t2.city");
        $this->db->join("$this->user t3", "t2.user_id = t3.id");
        $this->db->join("$this->district t4", "t2.district = t4.id");
        $this->db->join("$this->city t5", "t4.city_id = t5.id");

        $this->db->join("$this->like t6", "t2.id = t6.school_id", "left");
        $this->db->join("$this->bookmark t7", "t2.id = t7.school_id", "left");
        $this->db->join("$this->recommendation t8", "t2.id = t8.school_id", "left");

        $this->db->join("$this->like t9", "t2.id = t9.school_id AND t9.user_id = $user_id", "left");
        $this->db->join("$this->bookmark t10", "t2.id = t10.school_id AND t10.user_id = $user_id", "left");

        $this->db->where('t2.appearance', 1);
        $this->db->where('t2.is_active', 1);
        $this->db->where('t3.active', 1);
        if($display != 0) {
            $this->db->limit($display, $position);
        }
        $this->db->group_by('t2.id');
        if($type == "city") {
            $this->db->like('t5.name', $key, 'both');
            return $this->db->get("$this->city t1")->result_array();
        } elseif($type == "district") {
            $this->db->like('t4.name', $key, 'both');
            return $this->db->get("$this->district t1")->result_array();
        } else {
            return false;
        }
    }

    public function search_school_by_name($key, $display, $position = 0, $user_id = 0) {
        $this->db->select("t1.*, t1.id as school_id");
        $this->db->select("t2.avatar, t2.uuid, t2.username");
        $this->db->select("t4.name as district, t5.name as city");

        $this->db->select("COUNT(DISTINCT t6.id) as like_count");
        $this->db->select("COUNT(DISTINCT t7.id) as bookmark_count");
        $this->db->select("COUNT(DISTINCT t8.id) as recommend_count");

        $this->db->select("t9.id as is_liked");
        $this->db->select("t10.id as is_bookmarked");

        $this->db->join("$this->user t2", "t1.user_id = t2.id");
        $this->db->join("$this->district t4", "t1.district = t4.id");
        $this->db->join("$this->city t5", "t4.city_id = t5.id");

        $this->db->join("$this->like t6", "t1.id = t6.school_id", "left");
        $this->db->join("$this->bookmark t7", "t1.id = t7.school_id", "left");
        $this->db->join("$this->recommendation t8", "t1.id = t8.school_id", "left");

        $this->db->join("$this->like t9", "t1.id = t9.school_id AND t9.user_id = $user_id", "left");
        $this->db->join("$this->bookmark t10", "t1.id = t10.school_id AND t10.user_id = $user_id", "left");

        $this->db->like('school_name', $key, 'both');
        $this->db->where('t1.appearance', 1);
        $this->db->where('t1.is_active', 1);
        $this->db->where('t2.active', 1);
        $this->db->group_by('t1.id');
        if($display != 0) {
            $this->db->limit($display, $position);
        }
        return $this->db->get("$this->school t1")->result_array();
    }

    public function get_school_by_id($school_id, $user_id = 0) {
        $this->db->select("t1.*, t1.id as school_id");
        $this->db->select("t2.avatar, t2.uuid, t2.username");
        $this->db->select("t4.name as district, t5.name as city");

        $this->db->select("COUNT(DISTINCT t6.id) as like_count");
        $this->db->select("COUNT(DISTINCT t7.id) as bookmark_count");
        $this->db->select("COUNT(DISTINCT t8.id) as recommend_count");

        $this->db->select("t9.id as is_liked");
        $this->db->select("t10.id as is_bookmarked");

        $this->db->join("$this->user t2", "t1.user_id = t2.id");
        $this->db->join("$this->district t4", "t1.district = t4.id");
        $this->db->join("$this->city t5", "t4.city_id = t5.id");

        $this->db->join("$this->like t6", "t1.id = t6.school_id", "left");
        $this->db->join("$this->bookmark t7", "t1.id = t7.school_id", "left");
        $this->db->join("$this->recommendation t8", "t1.id = t8.school_id", "left");

        $this->db->join("$this->like t9", "t1.id = t9.school_id AND t9.user_id = $user_id", "left");
        $this->db->join("$this->bookmark t10", "t1.id = t10.school_id AND t10.user_id = $user_id", "left");

        $this->db->where('t1.id', $school_id);
        $this->db->group_by('t1.id');
        return $this->db->get("$this->school t1")->result_array();
    }

    public function search_school_by_address($key, $display, $position = 0, $user_id = 0) {
        $this->db->select("t1.*, t1.id as school_id");
        $this->db->select("t2.avatar, t2.uuid, t2.username");
        $this->db->select("t4.name as district, t5.name as city");

        $this->db->select("COUNT(DISTINCT t6.id) as like_count");
        $this->db->select("COUNT(DISTINCT t7.id) as bookmark_count");
        $this->db->select("COUNT(DISTINCT t8.id) as recommend_count");

        $this->db->select("t9.id as is_liked");
        $this->db->select("t10.id as is_bookmarked");

        $this->db->join("$this->user t2", "t1.user_id = t2.id");
        $this->db->join("$this->district t4", "t1.district = t4.id");
        $this->db->join("$this->city t5", "t4.city_id = t5.id");

        $this->db->join("$this->like t6", "t1.id = t6.school_id", "left");
        $this->db->join("$this->bookmark t7", "t1.id = t7.school_id", "left");
        $this->db->join("$this->recommendation t8", "t1.id = t8.school_id", "left");

        $this->db->join("$this->like t9", "t1.id = t9.school_id AND t9.user_id = $user_id", "left");
        $this->db->join("$this->bookmark t10", "t1.id = t10.school_id AND t10.user_id = $user_id", "left");

        $this->db->like('t1.address', $key, 'both');
        $this->db->where('t1.appearance', 1);
        $this->db->where('t1.is_active', 1);
        $this->db->where('t2.active', 1);
        $this->db->group_by('t1.id');
        if($display != 0) {
            $this->db->limit($display, $position);
        }
        return $this->db->get("$this->school t1")->result_array();
    }

    public function advanced_search($attr = array(), $languages, $menus, $display, $position = 0, $user_id = 0) {
        $this->db->select("t1.*, t1.id as school_id");
        $this->db->select("t4.name as district, t5.name as city");
        $this->db->select("t6.avatar, t6.uuid, t6.username, t6.email");

        $this->db->select("COUNT(DISTINCT t7.id) as like_count");
        $this->db->select("COUNT(DISTINCT t8.id) as bookmark_count");
        $this->db->select("COUNT(DISTINCT t9.id) as recommend_count");

        $this->db->select("t10.id as is_liked");
        $this->db->select("t11.id as is_bookmarked");

        if($languages) {
            $this->db->join("$this->language t2", "t1.id = t2.school_id");
            $this->db->where_in('language', $languages);
            $this->db->having('COUNT(language)', count($languages), FALSE);
        }
        if($menus) {
            $this->db->join("$this->menu t3", "t1.id = t3.school_id");
            $this->db->where_in('menu', $menus);
            $this->db->having('COUNT(menu)', count($menus), FALSE);
        }
        $this->db->join("$this->district t4", "t1.district = t4.id");
        $this->db->join("$this->city t5", "t4.city_id = t5.id");
        $this->db->join("$this->user t6", "t1.user_id = t6.id");

        $this->db->join("$this->like t7", "t1.id = t7.school_id", "left");
        $this->db->join("$this->bookmark t8", "t1.id = t8.school_id", "left");
        $this->db->join("$this->recommendation t9", "t1.id = t9.school_id", "left");

        $this->db->join("$this->like t10", "t1.id = t10.school_id AND t10.user_id = $user_id", "left");
        $this->db->join("$this->bookmark t11", "t1.id = t11.school_id AND t11.user_id = $user_id", "left");

        foreach($attr as $key => $value) {
            if($key == "school_name") {
                $this->db->like($key, $value, 'both');
            } else {
                $this->db->where($key, $value);
            }
        }
        $this->db->where('t1.appearance', 1);
        $this->db->where('t1.is_active', 1);
        $this->db->where('t6.active', 1);
        $this->db->group_by('t1.id');
        if($display != 0) {
            $this->db->limit($display, $position);
        }
        return $this->db->get("$this->school t1")->result_array();
    }

    public function get_school_details($attr = array(), $user_id = 0)
    {
        $this->db->select("t1.*, t1.id as school_id,t1.is_active, t1.appearance");
        $this->db->select("t2.avatar, t2.uuid, t2.username, t2.email, t2.active");
        $this->db->select("t4.name as district, t5.name as city");

        $this->db->select("COUNT(DISTINCT t6.id) as like_count");
        $this->db->select("COUNT(DISTINCT t7.id) as bookmark_count");
        $this->db->select("COUNT(DISTINCT t8.id) as recommend_count");
        $this->db->select("COUNT(DISTINCT t13.parent_id) as rate_count");

        $this->db->select("t9.id as is_liked");
        $this->db->select("t10.id as is_bookmarked");

        $this->db->select("GROUP_CONCAT(DISTINCT  t11.menu) as menus");
        $this->db->select("GROUP_CONCAT(DISTINCT  t12.language) as languages");

        $this->db->join("$this->user t2", "t1.user_id = t2.id");
        $this->db->join("$this->district t4", "t1.district = t4.id");
        $this->db->join("$this->city t5", "t4.city_id = t5.id");

        $this->db->join("$this->like t6", "t1.id = t6.school_id", "left");
        $this->db->join("$this->bookmark t7", "t1.id = t7.school_id", "left");
        $this->db->join("$this->recommendation t8", "t1.id = t8.school_id", "left");
        $this->db->join("tbRate t13", "t1.user_id = t13.school_id AND t13.parent_id != -1", "left");

        $this->db->join("$this->like t9", "t1.id = t9.school_id AND t9.user_id = $user_id", "left");
        $this->db->join("$this->bookmark t10", "t1.id = t10.school_id AND t10.user_id = $user_id", "left");

        $this->db->join("$this->menu t11", "t1.id = t11.school_id", 'left');
        $this->db->join("$this->language t12", "t1.id = t12.school_id", 'left');

        foreach ($attr as $k => $v) {
            $this->db->where($k, $v);
        }
        $this->db->group_by('t1.id');
        return $this->db->get("$this->school t1")->row_array();
    }

    function get_plans($school_user_id, $status) {
        $this->db->from("tbPlan");
        $this->db->where('status', $status);
        $this->db->where('school_id', $school_user_id);
        return $this->db->count_all_results();
    }

    public function get_nearby_schools($center_lat, $center_lng, $distance) {
        $sql = "select t1.id, t1.user_id, lat, lng, school_name, grade, type,
                acos(cos($center_lat * (PI()/180)) *
                 cos($center_lng * (PI()/180)) *
                 cos(lat * (PI()/180)) *
                 cos(lng * (PI()/180))
                 +
                 cos($center_lat * (PI()/180)) *
                 sin($center_lng * (PI()/180)) *
                 cos(lat * (PI()/180)) *
                 sin(lng * (PI()/180))
                 +
                 sin($center_lat * (PI()/180)) *
                 sin(lat * (PI()/180))
                ) * 6371 as distance
            from tbSchools t1 join plusauthentication_user t2 ON t1.user_id = t2.id where t2.active = 1 AND t1.is_active = 1 AND t1.appearance = 1
            having distance < $distance
            order by distance";

       return $this->db->query($sql)->result_array();
    }
}