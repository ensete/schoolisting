<?php

class tbSaved_answer extends DataMapper
{

    var $table = 'tbSaved_answer';

    function get_saved_answers($user_id, $type, $school_id = 0) {
        $this->db->select("t1.id, t1.total, t1.created, t1.answer, t2.school_name, t3.uuid, t3.avatar");
        if($type == 1) {
            $this->db->join('tbSchools t2', "t1.target_id = t2.user_id");
        } else {
            $this->db->join('tbSchools t2', "t1.user_id = t2.user_id");
        }
        $this->db->join('plusauthentication_user t3', "t2.user_id = t3.id");
        $this->db->where("t1.type", $type);
        $this->db->where("t1.user_id", $user_id);
        if($school_id) {
            $this->db->where('t2.id', $school_id);
        }
        return $this->db->get("$this->table t1")->result_array();
    }

}