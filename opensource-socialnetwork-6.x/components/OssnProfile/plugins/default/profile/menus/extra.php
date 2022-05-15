<?php
/**
 * Open Source Social Network
 *
 * @package   (openteknik.com).ossn
 * @author    OSSN Core Team <info@openteknik.com>
 * @copyright (C) OpenTeknik LLC
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
 
?>
<div>
    <?php
    if (!empty($params['menu'])) {
		echo '<a role="button" data-bs-toggle="dropdown" class="btn-action" data-bs-target="#" aria-expanded="true"><i class="fa fa-sort-down"></i></a>';
		echo '<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">';
        foreach ($params['menu'] as $menu) {
            foreach ($menu as  $link) {
				$class = "dropdown-item profile-menu-extra-".$link['name'];
				if(isset($link['class'])){
					$link['class'] = $class.' '.$link['class'];	
				} else {
					$link['class'] = $class;
				}
				unset($link['name']);
				$link = ossn_plugin_view('output/url', $link);
                echo "<li>{$link}</li>";
            }
        }
		echo "</ul>";
    }
    ?>
</div>
