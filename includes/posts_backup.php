<?php

/**
 * posts backup class
 */
class jibres_posts extends jibres_backup
{

	public static $jibres_stantard_post_array = [  'title'       => 'post_title',
											'seotitle'    => '',
											'slug'        => '',
											'excerpt'     => 'post_excerpt',
											'subtitle'    => '',
											'content'     => 'post_content',
											'status'      => 'post_status',
											'publishdate' => 'post_modified',
											'datecreated' => 'post_date'
											];

	private $where_backup;
	private $this_jibres_wis;
	private $last_i = 0;
	
	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			$this->this_jibres_wis = jibres_wis();
			$this->where_backup = ( $this->this_jibres_wis == 'csv' ) ? 'posts' : '/post/add';
			$this->create_pbr();
			$this->get_post_data();
		}
	}

	
	private function create_pbr()
	{
		$all = jibres_get_not_backuped( 'ID', 'posts', 'post', ['post_type'=>'post'] );

		if ( $all != '0' ) 
		{
			printf('<br><p>Backing up posts...</p>');
			printf('<progress id="sprog" value="0" max="'.$all.'" style="height: 3px;"></progress>  <a id="sinof"></a><br><br>');
			printf('<script>
					function srsb(meq) {
						document.getElementById("sprog").value = meq;
						document.getElementById("sinof").innerHTML = meq + " of '.$all.' backed up";
					}
					</script>');
		}
	
	}


	function get_post_data()
	{


		$where = ['post_type'=>'post'];
		$data = $this->get_data( 'ID', 'posts', 'post', $where );
	
		if ( ! empty( $data ) ) 
		{
			$i = $this->last_i;
			
			foreach ( $data as $value ) 
			{
					
				$i++;
				
				// sort array by jibres posts database design
				$changed = $this->backup_arr_sort( $value, self::$jibres_stantard_post_array );
				
				// backup this post
				$get_data = jibres_wis( $this->where_backup, $changed );

				// insert this post to jibres check table
				if ( is_array( $get_data ) and !empty( $get_data ) ) 
				{
					if ( $get_data['ok'] == true ) 
					{
						$this->insert_backup_in_jibres( [$value['ID'], 'post'] );
					}
					else
					{
						$error = 'post code: ' . $value['ID'] . ' > ' . json_encode( $get_data, JSON_UNESCAPED_UNICODE );
						jibres_error_log( 'post_backup', $error );
						
						printf('<div class="updated" style="border-left-color: #c0392b;"><br>' . 
						 		$get_data['msg'][0]['text']	. 
						 		'<a href="?page=jibres" class="jibres_notif_close">close</a><br><br></div>');
						exit();
					}
				}
				elseif ( $get_data == true ) 
				{
					$this->insert_backup_in_jibres( [$value['ID'], 'post'] );
				}
				
				// update progress bar
				printf('<script>
							srsb('.$i.');
						</script>');
				ob_flush();
				flush();
			}
	

			$this->last_i = $i;
			$this->pob_start_again();
		}
		else
		{
			printf('<br><a href="?page=jibres" class="jibres_notif_close">close</a>');
			printf("All Posts Are Backed up");
			if ( $this->this_jibres_wis == 'csv' ) 
			{
				// csv download url
				printf(' | <a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a>');

				if( jibres_auto_mail() == true )
				{
					jibres_mail_backup( 'posts' );
				}
			}
			printf('<br><br>');

		}
	
	}


	function pob_start_again()
	{
		$this->get_post_data();
	}

}

?>