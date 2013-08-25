<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
    <?= $B->htmlhead; ?>
    <body>
        <?= $B->page_top ?>
        <?= $B->pageband; ?>
        <div id="page_wrap">
            <?= $B->header ?>
            <?= $B->navbar ?>
            <div id="main_content">
               <div id="left_column"><?= $B->left_col?></div>                            
               <div id="main_column"><?= $B->content ?></div>
            </div>
            <div id="page_footer">
                <div class="bottom"><?= $B->footer ?></div>
            </div>
        </div>
        <?= $B->page_bottom ?>
    </body>
</html>