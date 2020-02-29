<?php


/**
 * categories backup class
 */
class jibres_categories
{

	public $jibres_stantard_category_array = array( 'name'  => 'name',
													'slug'  => 'slug',
													'group' => 'term_group'
													);
	

	private $where_backup;

	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			$this->where_backup = (wis() == 'csv') ? 'categories' : '/category/add';
			$this->get_category_data();
		}
	}


	function category_arr_sort($arr)
	{
	
		$changed = sort_arr($this->jibres_stantard_category_array, $arr);
	
		wis($this->where_backup, $changed);
	}
	
	function insert_category_in_jibres($id)
	{
		$data = array('item_id' => $id, 'type' => 'category');
		insert_in_jibres($data);
	}
	
	function get_category_data()
	{
		global $wpdb;
	
		$results = $wpdb->get_results("SELECT term_id FROM $wpdb->term_taxonomy WHERE 
										taxonomy = 'product_cat' AND term_id NOT IN 
										(SELECT item_id FROM {$wpdb->prefix}jibres_check 
										WHERE type = 'category' AND backuped = 1)");
	
		$arr_results = array();
		$ids = array();
	
		foreach ($results as $key => $value) 
		{
			foreach ($value as $key => $val) 
			{
				if ($key == "term_id") 
				{
					array_push($ids, $val);
				}
			}
	
		}
	
		if (!empty($results)) 
		{
			$i = 0;
			printf('<p>Backuping categories...</p>');
			printf('<progress id="pprog" value="0" max="'.count($ids).'" style="height: 3px;"></progress>  <a id="inof"></a><br><br>');
			printf('<script>
					function prsb(meq) {
						document.getElementById("pprog").value = meq;
						document.getElementById("inof").innerHTML = meq + " of '.count($ids).' backuped";
					}
					</script>');
			foreach ($ids as $value) 
			{
				
				$i++;
				$this->insert_category_in_jibres($value);
				$cat_results = $wpdb->get_results("SELECT * FROM $wpdb->terms WHERE term_id = $value");
				foreach ($cat_results as $key => $val) 
				{
					foreach ($val as $key2 => $val2) 
					{
						$arr_results[$key2] = $val2;
					}
				}
	
				printf('<script>
							prsb('.$i.');
						</script>');
				$this->category_arr_sort($arr_results);
				ob_flush();
				flush();
			}
	
			printf("OK Your Categories Bacuped<br><br>");
		}
		else
		{
			printf("All Categories Are Backuped<br><br>");
		}
	
	}


}

?>