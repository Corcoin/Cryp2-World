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

$class = 'ossn-text-input';
if(isset($params['class'])){
	//[B] strangeness of class extending in input modules #1635
	$params['class']  = $class .' '. $params['class'];
}
$defaults = array(
	'value' => '',
	'disabled' => false,
	'class' => $class,
	'type' => 'text'
);

$params = array_merge($defaults, $params);
$attributes = ossn_args($params);

echo "<input {$attributes} />";
