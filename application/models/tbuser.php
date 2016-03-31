<?php

class tbUser extends DataMapper {

    var $table = 'plusauthentication_user';
    /**
	 * init model
	 * @param init: dictionary to filter
	 * @param code_to_throw: if any, an exception will be thrown with this code
	 * @access static
	 */
	static function get_model($init = array(), $code_to_throw = FALSE) {
		$model = new tbUser;
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

    static function get_user($attr = array()) {
        $user = new tbUser();
        foreach($attr as $k => $v) {
            $user->where($k, $v);
        }
        return $user->get();
    }
}
