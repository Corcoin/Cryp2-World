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
$user = $params['user'];
$message = $params['message'];
$message_id = $params['message_id'];
if($user->guid == ossn_loggedin_user()->guid){
					?>
                    	<div class="row" id="message-item-<?php echo $message_id ?>">
                                <div class="col-md-12 pull-right">
                                	<div class="message-box-sent text">
											<span><?php echo ossn_call_hook('messages', 'message:smilify', (array)$params, ossn_message_print($message)); ?></span>
                                        	<div class="time-created"><?php echo ossn_user_friendly_time(time());?></div>
                                            <a class="ossn-message-delete" data-id="<?php echo $message_id;?>"><i class="fa fa-ellipsis-h"></i></a>				
                                	</div>
                                </div>                            
                        </div>
                    <?php	
					} else {
						?>
                    	<div class="row" id="message-item-<?php echo $message_id ?>">
                        	<div class="col-md-1">
                                	<a href="<?php echo $user->profileURL();?>"><img  class="user-icon" src="<?php echo $user->iconURL()->smaller;?>" /></a>
                                </div>                                
                                <div class="col-md-11 pull-left">
                                	<div class="message-box-recieved text">
											<?php echo ossn_call_hook('messages', 'message:smilify', (array)$params, ossn_message_print($message)); ?>
                                        	<div class="time-created"><?php echo ossn_user_friendly_time(time());?></div>    
                                        </div>
                                </div>
                        </div>                       
                        <?php
					}
