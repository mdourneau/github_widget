<?php
/*
 * Plugin Name: github_widget
 * Plugin URI: https://github.com
 * Version: 1.0
 * Author: DOURNEAU Maxence
 * Author URI: https://www.linkedin.com/in/dourneau-maxence-9162221a1/
*/

// Creating the widget 
class github_widget extends WP_Widget
{
    function __construct() {
        require_once __DIR__ . '/vendor/autoload.php';
        parent::__construct(
            // Widget ID
            'github_widget',
            // Widget name (front)
            __('Widget GitHub', 'github_widget_domain'),
            // Widget description
            array('description' => __('Une liste de commits d\'un dépôt GitHub.', 'github_widget_domain'),)
        );
    }

    // Widget frontend
    public function widget($args, $instance)
    {      
        $user = $instance['user'];
        $repository = $instance['repository'];
        $branch = $instance['branch'];
        $numbercommits = $instance['numbercommits'];
        $client = new \Github\Client();
        $commits = $client->api('repo')->commits()->all($user, $repository, array('sha' => $branch));
        $transient = get_transient('github_widget_transient');
        if($transient == false){
            $this->gestion_cache($commits);
        }
        $commitsdisplay = $numbercommits <= count($commits) ? $numbercommits : count($commits);
       include_once __DIR__. "/vues/widget_format.php";
    }

    // Widget Backend 
    public function form($instance)
    {
        $user = isset($instance['user']) ? $instance['user'] : __('symfony', 'github_widget_domain');
        $repository = isset($instance['repository']) ? $instance['repository'] : __('symfony', 'github_widget_domain');
        $branch = isset($instance['branch']) ? $instance['branch'] : __('Master', 'github_widget_domain');
        $numbercommits = isset($instance['numbercommits']) ? $instance['numbercommits'] : __('10', 'github_widget_domain');
    // Widget admin form
    ?>
        <p>
            <label for="<?= $this->get_field_id('user'); ?>"><?php _e('User:'); ?></label>
            <input class="widefat" id="<?= $this->get_field_id('user'); ?>" name="<?= $this->get_field_name('user'); ?>" type="text" value="<?= esc_attr($user); ?>" />
            <label for="<?= $this->get_field_id('repository'); ?>"><?php _e('Repository:'); ?></label>
            <input class="widefat" id="<?= $this->get_field_id('repository'); ?>" name="<?= $this->get_field_name('repository'); ?>" type="text" value="<?= esc_attr($repository); ?>" />
            <label for="<?= $this->get_field_id('branch'); ?>"><?php _e('Branch:'); ?></label>
            <input class="widefat" id="<?= $this->get_field_id('branch'); ?>" name="<?= $this->get_field_name('branch'); ?>" type="text" value="<?= esc_attr($branch); ?>" />
            <label for="<?= $this->get_field_id('numbercommits'); ?>"><?php _e('numbercommits:'); ?></label>
            <input class="widefat" id="<?= $this->get_field_id('numbercommits'); ?>" name="<?= $this->get_field_name('numbercommits'); ?>" type="number" value="<?= esc_attr($numbercommits); ?>" />
        </p>
    <?php
    }

    // Replacing old widget instances by new
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['user'] = (!empty($new_instance['user'])) ? strip_tags($new_instance['user']) : '';
        $instance['repository'] = (!empty($new_instance['repository'])) ? strip_tags($new_instance['repository']) : '';
        $instance['branch'] = (!empty($new_instance['branch'])) ? strip_tags($new_instance['branch']) : '';
        $instance['numbercommits'] = (!empty($new_instance['numbercommits'])) ? strip_tags($new_instance['numbercommits']) : '';
        return $instance;
    }

    public function gestion_cache( $value){
        set_transient('github_widget_transient', $value, '3600');
    }
}

// Register and load widget
function github_load_widget()
{
    register_widget('github_widget');
}
add_action('widgets_init', 'github_load_widget');
