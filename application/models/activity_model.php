<?php
class Activity_model extends CI_Model {

    private $like = "tbLike";
    private $bookmark = "tbBookmarks";
    private $recommend = "tbRecommendation";


    function like($data) {
        $this->db->insert($this->like, $data);
    }

    function unlike($attr = array()) {
        foreach($attr as $k => $v) {
            $this->db->where($k, $v);
        }
        $this->db->delete($this->like);
    }

    function bookmark($data) {
        $this->db->insert($this->bookmark, $data);
    }

    function unbookmark($attr = array()) {
        foreach($attr as $k => $v) {
            $this->db->where($k, $v);
        }
        $this->db->delete($this->bookmark);
    }

    function refer($data) {
        $this->db->insert($this->recommend, $data);
    }
}