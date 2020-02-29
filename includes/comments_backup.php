<?php


/**
 * comments backup class
 */
class jibres_comments
{

	public $jibres_stantard_comments_array = array( 'post'         => 'comment_post_ID',
													'author'       => 'comment_author',
													'author_email' => 'comment_author_email',
													'date'         => 'comment_date',
													'content'      => 'comment_content',
													'approved'     => 'comment_approved'
													);
	
	private $where_backup;


	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			$this->where_backup = (wis() == 'csv') ? 'comments' : '/comment/add';
			$this->get_comment_data();
		}
	}


	function comment_arr_sort($arr)
	{
			
		$changed = sort_arr($this->jibres_stantard_comments_array, $arr);
	
		wis($this->where_backup, $changed);
	}
	
	function insert_comment_in_jibres($id)
	{
		$data = array('item_id' => $id, 'type' => 'comment');
		insert_in_jibres($data);
	}
	
	function get_comment_data()
	{
		global $wpdb;
	
		$results = $wpdb->get_results("SELECT comment_ID FROM $wpdb->comments WHERE 
										comment_ID NOT IN 
										(SELECT item_id FROM {$wpdb->prefix}jibres_check 
										WHERE type = 'comment' AND backuped = 1)");
	
		$arr_results = array();
		$ids = array();
	
		foreach ($results as $key => $value) 
		{
			foreach ($value as $key => $val) 
			{
				if ($key == "comment_ID") 
				{
					array_push($ids, $val);
				}
			}
	
		}
	
		if (!empty($results)) 
		{
			$i = 0;
			printf('<p>Backuping comments...</p>');
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
				$this->insert_comment_in_jibres($value);
				$post_results = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_ID = $value");
				foreach ($post_results as $key => $val) 
				{
					foreach ($val as $key2 => $val2) 
					{
						$arr_results[$key2] = $val2;
					}
				}
	
				printf('<script>
							prsb('.$i.');
						</script>');
				$this->comment_arr_sort($arr_results);
				ob_flush();
				flush();
			}
	
			printf("OK Your Comments Backuped<br><br>");
		}
		else
		{
			printf("All Comments Are Backuped<br><br>");
		}
	
	}

}


?>