<?php // possible options
    $current_email = get_option('raeval_email');
    if(!$current_email){
	    add_option("raeval_email",'','','no');
	    $current_email = "None";
    }
?>
<div class="wrap">
    <h1>Options</h1>
    <p>Takes a comma seperated value of emails to send evaluations to</p>
    <p>Currently: <?php echo $current_email;?></p>
    <form method="post" action="">
        <?php wp_nonce_field( basename( RAEVAL__PLUGIN_DIR ), 'raeval_options_nonce' );?>
        <input type="text" name="email" placeholder="" value="">
        <input type="submit" value="submit">
    </form>
</div><!-- wrap -->