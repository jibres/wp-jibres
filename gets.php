<?php 

$table_name = $wpdb->prefix . 'jibres';
$strc =  "CREATE TABLE $table_name (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT NOW() NOT NULL,
		  store varchar(11) DEFAULT NULL,
		  apikey varchar(32) DEFAULT NULL,
		  appkey varchar(32) DEFAULT NULL,
		  wis varchar(55) NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";
if (create_jibres_table($strc, 'jibres') === true) 
{
	$check_jib = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}jibres");
	if (!empty($check_jib)) 
	{
		if ($_GET['jibres'] and $_GET['jibres'] == 'backup_all') 
		{
			$packs = array('products', 'orders', 'posts', 'comments', 'categories');
			foreach ($packs as $value) 
			{
				require_once dirname( __FILE__ ) . '/includes/'.$value.'_backup.php';
				$open_func = $value.'_b';
				$open_func();
			}
			printf('<a href="?page=jibres"><button class="bt">Back Home</button></a>');
		}
		elseif ($_GET['jibres']) 
		{
			require_once dirname( __FILE__ ) . '/includes/'.$_GET['jibres'].'.php';
			$get_func = explode("_", $_GET['jibres']);
			$open_func = $get_func[0]."_b";
			$open_func();
			printf('<a href="?page=jibres"><button class="bt">Back Home</button></a>');
		}
		else
		{
			require_once(dirname( __FILE__ ) . '/main.php');
		}
	}
	else
	{
		require_once(dirname( __FILE__ ) . '/first_jibres.php');
	}
}

?>