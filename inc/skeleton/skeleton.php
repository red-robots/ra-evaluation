<?php global $post;
$values = get_post_meta($post->ID,'ra_skeleton',true);
if($values){
	$values = json_decode($values,true);
}
?>
<div class="skeleton">
	<div class="middle-1 dot-small ra-node <?php if($values && $values['middle-1']) echo "active";?>"></div>
	<?php

	require_once 'body-left.php';

	require_once 'body-right.php';

	?>
</div>