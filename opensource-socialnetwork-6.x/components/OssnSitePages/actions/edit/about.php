<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Open Social Website Core Team <info@openteknik.com>
 * @copyright (C) OpenTeknik LLC
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
$save = new OssnSitePages;
$save->pagename = 'about';
$save->pagebody = input('pagebody');
if ($save->SaveSitePage()) {
    ossn_trigger_message(ossn_print('page:saved'), 'success');
    redirect(REF);
} else {
    ossn_trigger_message(ossn_print('page:save:error'), 'error');
    redirect(REF);
}
