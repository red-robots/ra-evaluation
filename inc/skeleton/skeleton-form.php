<p>Patient must have at least 1 joint with definite clinical synovitis (swelling).</p>
<p>Joint involvement refers to any swollen or tender joint on examination.</p>
<p><a href="<?php echo plugin_dir_url(RAEVAL__PLUGIN_DIR)."ra-evaluation/inc/images/skeleton-diagram.pdf";?>" target="_blank">Help</a></p>
<div class="skeleton-key">
    <div class="line"><div class="box active-1"></div> Tender</div>
    <div class="line"><div class="box active-2"></div> Swollen</div>
    <div class="line"><div class="box active-3"></div> Tender & Swollen</div>
    <div class="line"><div class="box non-click"></div> Not Selectable</div>
</div>
<div class="skeleton">

    <div class="middle-1 dot-small ra-node"></div>
	<?php

	require_once 'body-left.php';

	require_once 'body-right.php';

	require_once 'inputs.php';
	?>
</div>