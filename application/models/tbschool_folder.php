<?php

class tbSchool_folder extends DataMapper {

    var $table = 'tbSchool_folder';
    /**
	 * init model
	 * @param init: dictionary to filter
	 * @param code_to_throw: if any, an exception will be thrown with this code
	 * @access static
	 */
	static function get_model($init = array(), $code_to_throw = FALSE) {
		$model = new tbSchool_folder;
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
	function delete_record ($data = array())
	{
		if (count($data) > 0)
		{
			foreach ($data as $key => $value) {
				$this->where($key, $value);
			}
			$this->get();
			$this->delete_all();
		}
	}
	/**
	 * 
	 */
	function get_folder_n_file ($school_id)
	{
		$this->db->select("folder.name as folder_name, folder.id as folder_id, file.id as file_id, file.display_name as file_name");
		$this->db->from("tbSchool_folder folder");
		$this->db->join("tbSchool_file file", "folder.id = file.folder_id", "left");
		$this->db->where("folder.school_id", $school_id);
		$result = $this->db->get()->result();
		return $this->convert_folder_n_file_detail($result);
	}
	/**
	 * change array result from get_folder_n_file into specific array format
	 */
	private function convert_folder_n_file_detail ($data)
	{
		$dataset = array();
		$final_data = array();
		foreach ($data as $d) 
		{
			if (isset($d->file_id))
			{
				$dataset[$d->folder_name . "::" . $d->folder_id][] = array(
					"file_id" => $d->file_id,
					"file_name" => $d->file_name
				);
			}
			else
			{
				$dataset[$d->folder_name . "::" . $d->folder_id] = array();
			}
		}	
		
		foreach ($dataset as $key => $value) 
		{
			$folder = explode("::", $key);
			$final_data[] = array(
				"folder_id" => $folder[1],
				"folder_name" => $folder[0],
				"files" => $value
			);
		}
		
		return $final_data;
	}
}
