<?php global $post;
$values = get_post_meta($post->ID,'raeval_skeleton',true);
if($values){
	$values = json_decode($values,true);
}
?>
<div class="key-legend">
	<div class="skeleton-key">
		<h2>Pain Key</h2>
	    <div class="line"><div class="box active-1"></div> Tender</div>
	    <div class="line"><div class="box active-2"></div> Swollen</div>
	    <div class="line"><div class="box active-3"></div> Tender & Swollen</div>
	    <div class="line"><div class="box non-click"></div> Not Selectable</div>
	</div>
	<div class="skeleton-key">
		<h2>Joint Key</h2>
		<div class="line">MCP: Metacarpophalangeal Joint</div>
		<div class="line">PIP: Proximal Interphalangeal Joint</div>
		<div class="line">IP: Interphalangeal Joint</div>
		<div class="line">MTP: Metatarsophalangeal Joint</div>
	</div>
</div>
<div class="skeleton">

	<div class="middle-1 dot-small ra-node <?php if($values && $values['middle-1']) echo "active-".$values['middle-1'];?>"></div>
	<?php

	require_once 'body-left.php';

	require_once 'body-right.php';

	?>
</div>