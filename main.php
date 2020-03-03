<?php 

printf('<br><br>');
printf('<a href="?page=jibres&jibres=backup_all"><button class="bt">Backup All Data</button></a><br><br><hr><br>');
printf('<a href="?page=jibres&jibres=products_backup"><button class="bt">Backup Your Products</button></a>  |  ');
printf('<a href="?page=jibres&jibres=orders_backup"><button class="bt">Backup Your Orders</button></a>  |  ');
printf('<a href="?page=jibres&jibres=posts_backup"><button class="bt">Backup Your Posts</button></a>  |  ');
printf('<a href="?page=jibres&jibres=comments_backup"><button class="bt">Backup Your Comments</button></a>  |  ');
printf('<a href="?page=jibres&jibres=categories_backup"><button class="bt">Backup Your Categories</button></a><br><br><hr><br>');


function print_infos($jwb)
{
	printf('<div class="infos">');
	informations_b('ID', 'posts', 'product', $jwb, ['post_type'=>'product'], true);
	printf('<br><br>');
	
	informations_b('ID', 'posts', 'order', $jwb, ['post_type'=>'shop_order']);
	printf('<br><br>');
	
	informations_b('ID', 'posts', 'post', $jwb, ['post_type'=>'post']);
	printf('<br><br>');
	
	informations_b('comment_ID', 'comments', 'comment', $jwb);
	printf('<br><br>');
	
	informations_b('term_id', 'term_taxonomy', 'category', $jwb, ['taxonomy'=>'product_cat']);
	printf('</div>');
}

function csv_file_del($fname, $dname, $last = false)
{
	$last = ($last == false) ? '  |  ' : null;
	printf('<form onsubmit="return confirm(\'Do you really want to delete csv file of '.$fname.' backup?\');" action method="post" style="display: inline;">
			<input type="hidden" name="csvdel" value="'.$fname.'_'.$dname.'">
			<input type="submit" class="dbt" value="Delete '.$fname.' csv file">
			</form>'. $last);
}


if (jibres_wis() == 'csv') 
{
	csv_file_del('products', 'product');
	csv_file_del('orders', 'order');
	csv_file_del('posts', 'post');
	csv_file_del('comments', 'comment');
	csv_file_del('categories', 'category', true);
	printf('<br><br><hr><br><br>');
	print_infos('csv');
}
else
{
	printf('<br><br>');
	print_infos('api');
	printf('<br><br>');
	printf('<form onsubmit="return confirm(\'Do you really want to delete your jibres api informations?\');" action method="post">
			<input type="hidden" name="changit" value="start_again">
			<input type="submit" class="jbt" value="Change my jibres api informations">
			</form>');
}


?>