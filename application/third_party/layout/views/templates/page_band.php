<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div id="page_band">
    <div id="band_left"><a href="<?= $B->left_link?>"><img id="band_logo" src="<?= $B->band_logo?>"/><div id="band_sitename"><?= $B->band_left?></div><div id="band_tenant"><?=$B->tenant?></div></a></div>
    <div id="band_mid"><?= $B->band_mid?></div>
    <div id="band_right" ><?=$B->band_right?></div>
</div>
<div class="clear"></div>