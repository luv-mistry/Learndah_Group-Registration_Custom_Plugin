<?PHP
/**
 * Plugin Name:       WMD LearnDash Group Registration Custom Plugin
 * Description:       Add Custom Meta on usermeta table when user enrolled using group code.
 * Version:           1.0
 * Author:            WisdmLabs
 * Author URI:        https://wisdmlabs.com
 */


if (!class_exists('Wdm_Learndash_Group_Registration_Custom')){
    
    class Wdm_Learndash_Group_Registration_Custom {
        
        public function __construct(){
            // Hook is used for saving custom meta data.
            add_action('ldgr_action_group_code_user_created',array($this,'wdm_add_meta_on_user_enrolled_using_group_code'),10, 3);
            add_action('ldgr_action_group_code_user_enrolled',array($this ,'wdm_add_meta_on_user_enrolled_using_group_code'),10, 3);
            // Hook for displaying the meta to user profile.
            add_action( 'show_user_profile', array($this , 'wdm_add_meta_of_user_on_user_profile') );
            add_action( 'edit_user_profile', array($this,'wdm_add_meta_of_user_on_user_profile') );
        }

        /**
         * Adding meta data to usermeta table.
         * 
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
                //If the wdm_group_code meta present in usermeta table
                $groups_id = get_post_meta($group_code->ID,"group_code_related_groups",true);
                $present_group_code = get_user_meta( $user_id , 'wdm_group_code' , true);
                if (!empty($present_group_code)) {
                    $group_code_array = explode(",",$present_group_code);
                    if (!in_array($group_code->post_title ,$group_code_array)){
                        delete_user_meta( $user_id,$group_code->post_title);
                        array_push($group_code_array, $group_code->post_title );
                        $group_code_string = implode(",",$group_code_array);
                        update_user_meta( $user_id, 'wdm_group_code' , $group_code_string);
                        update_user_meta( $user_id, $group_code->post_title ,strtotime('now'));
                        add_user_meta( $user_id, $group_code->post_title ,$groups_id );


                    } 
                    else {
                        delete_user_meta( $user_id,$group_code->post_title);
                        update_user_meta( $user_id, $group_code->post_title ,strtotime('now'));
                        add_user_meta( $user_id, $group_code->post_title ,$groups_id);
                    }
                
                }
                // If the wdm_group_code meta not present in usermeta table
                else {
                    update_user_meta( $user_id, 'wdm_group_code' ,$group_code->post_title);
                    update_user_meta( $user_id, $group_code->post_title ,strtotime('now'));
                    add_user_meta( $user_id, $group_code->post_title ,$groups_id);
                }
            }
            
        }

        /**
         * Show users enrolled Group code, Timestamp and Groups ID in form of table on user profile page
         * 
         * @param object $user
         * 
         * @return void
         *  
          */
        public function wdm_add_meta_of_user_on_user_profile ( $user ) { 
            // Check If the meta 'wdm_group_code' is present
            $present_group_code = get_user_meta( $user->ID,'wdm_group_code',true);
            if (!empty($present_group_code)){
                $group_code_array = explode(",",$present_group_code);?>
                <h3><?php _e("Enrolled Group Information", "blank"); ?></h3>
                <table class="form-table">
                <tr>
                    <th><?php _e("Group Code"); ?></th>
                    <th><?php _e("Timestamp"); ?></th>
                    <th><?php _e("Group ID"); ?></th>
                </tr>
                <?php
                foreach ($group_code_array as $group_code ){
                    $group_code_data = get_user_meta($user->ID, $group_code);
                    $timestamp = wp_date( 'Y-m-d h:i:sa', $group_code_data[0]);
                    $groups_id = $group_code_data[1];
                    ?>
                    <tr>
                    <td><?php _e($group_code); ?></td>
                    <td><?php _e($timestamp); ?></td>
                    <td><?php _e($groups_id); ?></td>
                    </tr>
                    <?php
                }
            }
            
            ?>
            </table>
          
        <?php 
        }
    }

    new Wdm_Learndash_Group_Registration_Custom();
}