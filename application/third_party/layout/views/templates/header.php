<div id='page_header'>
    <!-- Example of getting data via property,$B->prop, via Data array $D['property'] or getter $B->getMessage() -->
<div id='headericon'><?php echo $B->applogo ?></div>
<div id='headertext'><h1><?= $D['header_text'] ?></h1></div>
<?php   $msg = $B->getMessage(); ?>
    <?php if(!empty($msg)) :?>
        echo '<div id="header_msg">' . $msg . '</div>';
    <?php endif; ?>
</div>