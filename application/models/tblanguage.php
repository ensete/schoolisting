<?php

class tbLanguage extends DataMapper {

    var $table = 'pluslocalization_supported';
    /**
     * init model
     * @param init: dictionary to filter
     * @param code_to_throw: if any, an exception will be thrown with this code
     * @access static
     */
    static function get_model($init = array(), $code_to_throw = FALSE) {
        $model = new tbElement_detail;
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

    function get_languages() {
        return $this->db->get($this->table)->result_array();
    }

    function get_side_language() {
        $this->db->where('is_primary !=', 1);
        return $this->db->get($this->table)->row_array();
    }
    function get_primary_language() {
        $this->db->where('is_primary', 1);
        return $this->db->get($this->table)->row_array();
    }
}