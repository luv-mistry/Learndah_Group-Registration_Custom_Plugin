<?php
/**
 * Plugin Name:       WDM LearnDash Group Registration Custom Plugin
 * Description:       Add Custom Meta on usermeta table when user enrolled using group code.
 * Version:           1.0
 * Author:            WisdmLabs
 * Author URI:        https://wisdmlabs.com
 */


// Hook is used for saving custom meta data 
if (!class_exists('Wdm_Learndash_Group_Registration_Custom')){
    
    class Wdm_Learndash_Group_Registration_Custom {
        
        public function __construct(){
            add_action('ldgr_action_group_code_user_created',[$this,'wdm_add_meta_on_user_enrolled_using_group_code'],10, 3);
            add_action('ldgr_action_group_code_user_enrolled',[$this ,'wdm_add_meta_on_user_enrolled_using_group_code'],10, 3);
        }

        /**
         * @param int $user_id 
         * 
         * @param array $group_code 
         * 
         * @param array $form_data 
         * 
         * @return void
         */
        public function wdm_add_meta_on_user_enrolled_using_group_code ($user_id, $group_code, $form_data){

            if (!empty($user_id) && !empty($group_code) && !empty($form_data)){
                
                add_user_meta( $user_id, 'wdm_group_code' ,$group_code->post_title);
                add_user_meta( $user_id, $group_code->post_title ,strtotime('now'));
                add_user_meta( $user_id, $group_code->post_title ,$group_code->ID );
            }
            
        }
    }

    new Wdm_Learndash_Group_Registration_Custom();
}