<?php
/**
Plugin Name: M77 Spotify Embed
Plugin URI: http://www.moment77.com/
Description: Embeds Spotify Songs, Albums and Playlists into your posts from a Spotify-URI.
Author: Anders Gunnarsson, Moment77
Version: 1.0.1
Author URI: http://www.moment77.com/
License: GPL2
*/
/*
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class M77Spotify {
    const DB_OPTION_NAME = 'm77-spotify-options';
    const SMALL_WIDTH   = '100%';
    const SMALL_HEIGHT  = '80';
    const LARGE_WIDTH   = '100%';
    const LARGE_HEIGHT  = '380';
    const DEFAULT_THEME = 'black';
    const DEFAULT_VIEW  = 'list';

    // 
    private $sizes = array('small', 'large', 'custom');
    /**
     * Initalize the plugin by registering the hooks
     */
    function __construct() {
        add_action( 'add_meta_boxes', array(&$this, 'meta_box_add') ); 
        add_action( 'save_post', array(&$this, 'meta_box_save') ); 
        add_action( 'admin_print_scripts', array(&$this, 'add_admin_scripts') );

        add_action( 'admin_menu', array(&$this, 'register_settings_page') );
        add_action( 'admin_init', array(&$this, 'add_settings') );
        
        add_shortcode('spotify', array(&$this, 'spotity_shortcode'));


        
    }


    /**
     * Register the settings page
     */
    function register_settings_page() {
        add_options_page('Spotify Embed', 'Spotify Embed', 'manage_options', 'spotify-embed', array(&$this, 'settings_page') );
    }

    function add_settings(){

        //Global Options section
        register_setting( self::DB_OPTION_NAME, self::DB_OPTION_NAME, array(&$this, 'validate_settings'));

        add_settings_section('m77_spotify_settings', 'Spotify settings', array(&$this, 'print_spotify_settings'), __FILE__);
        
        add_settings_field( 'm77-spotify-default-size', 'Custom appearance', array(&$this, 'spotify_size_callback'), __FILE__, 'm77_spotify_settings');
        
        //add_settings_field( 'm77-spotify-default-size', 'Default Size', array(&$this, 'spotify_size_callback'), __FILE__, 'm77_spotify_settings');
        //add_settings_field( 'm77-spotify-default-size', 'Default Size', array(&$this, 'spotify_size_callback'), __FILE__, 'm77_spotify_settings');

        
    }

        /**
     * Dipslay the Settings page
     */
    function settings_page() {
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Spotify Embed</h2>

            <form id="smer_form" action="options.php" method="post">

                <?php settings_fields(self::DB_OPTION_NAME); ?>
                <?php do_settings_sections(__FILE__); ?>

                <p class="submit">
                    <input type="submit" name="m77-spotify-submit" class="button-primary" value="Save Changes" />
                </p>
            </form>
        </div>
        <?
        // Display credits in Footer
        //add_action( 'in_admin_footer', array(&$this, 'add_footer_links'));
    }

     /**
     * Print settings text
     */
    function  print_spotify_settings() {
        echo '<p>Change the "custom" appearance for Spotify Embeds.</p>';
    }
    
    /**
     * Callback for Ribbon type Setting
     */
    function spotify_size_callback() {
        $options                    = get_option(self::DB_OPTION_NAME);
        $options['custom-width']    = isset($options['custom-width'])?$options['custom-width']:self::LARGE_WIDTH;
        $options['custom-height']   = isset($options['custom-height'])?$options['custom-height']:self::LARGE_HEIGHT;
        $options['default-theme']   = isset($options['default-theme'])?$options['default-theme']:'white';
        $options['default-view']    = isset($options['default-view'])?$options['default-view']:'coverart';
        $options['preview-uri']     = isset($options['preview-uri'])?$options['preview-uri']:'spotify:album:4fTFa5jHOtEhEaXsK2qRRi';

        ?>

            <h3 class="title">Settings</h3>
            <table>
                <tr>
                    <td style="width: 60px"><strong>Size</strong></td>
                    <td><label for="m77-spotify-custom-width">width:</label></td>
                    <td><input type="text" class="small-text" id="m77-spotify-custom-width" name='<?=self::DB_OPTION_NAME?>[custom-width]' value="<?=$options['custom-width']?>" /></td>
                    <td><label for="m77-spotify-custom-height">height:</label></td>
                    <td><input type="text" class="small-text" id="m77-spotify-custom-height" name='<?=self::DB_OPTION_NAME?>[custom-height]' value="<?=$options['custom-height']?>" /></td>
                </tr>
            </table>
            <p class="description">Note: Widths larger then 260 are recomended. User either numbers or percent.</p>
            <table>
                <tr>
                    <td style="width: 60px"><strong>Theme</strong></td>
                    <td>
                        <input type="radio" id="m77-spotify-options-theme-black" name="<?=self::DB_OPTION_NAME?>[default-theme]" value="black" <?= checked('black', $options['default-theme'], false) ?> />
                        <label for="m77-spotify-options-theme-white">Black <em>(default)</em></label>
                    </td>
                    <td>
                        <input type="radio" id="m77-spotify-options-theme-white" name="<?=self::DB_OPTION_NAME?>[default-theme]" value="white" <?= checked('white', $options['default-theme'], false) ?> />
                        <label for="m77-spotify-options-theme-white">White</label>
                    </td>
                </tr>
                <tr>
                    <td style="width: 60px"><strong>View</strong></td>
                    <td>
                        <input type="radio" id="m77-spotify-options-view-list" name="<?=self::DB_OPTION_NAME?>[default-view]" value="list" <?= checked('list', $options['default-view'], false) ?> />
                        <label for="m77-spotify-options-view-list">List <em>(default)</em></label>
                    </td>
                    <td>
                        <input type="radio" id="m77-spotify-options-theme-coverart" name="<?=self::DB_OPTION_NAME?>[default-view]" value="coverart" <?= checked('coverart', $options['default-view'], false) ?> />
                        <label for="m77-spotify-options-view-coverart">Cover Art</label>
                    </td>
                </tr>
            </table>
            <p class="description">Note: Theme and View can only be changed when <em>height</em> is greater then about 330.</p>
            
            <h3 class="title">Preview</h3>
            Try with Song, Album or Playlist.<br/>
            <input class="regular-text" type="text" id="m77-spotify-preview-text" name='<?=self::DB_OPTION_NAME?>[preview-uri]' value="<?=$options['preview-uri']?>" />

            <div id="m77-spotify-preview-iframe" style="width:460px; background-color: #EFEFEF; padding: 10px; margin: 10px 0;">
                <iframe src="https://embed.spotify.com/?uri=<?=$options['preview-uri']?>&theme=<?=$options['default-theme']?>&view=<?=$options['default-view']?>" width="<?=$options['custom-width']?>" height="<?=$options['custom-height']?>" frameborder="0" allowtransparency="true"></iframe>
            </div>
        <?
    }

    /**
     * Validate the options entered by the user
     *
     * @param <type> $input
     * @return <type>
     */
    function validate_settings($input) {
        if(!in_array($input['m77-spotify-default-size'], $this->sizes)){
            // Default to small
            $input['m77-spotify-default-size'] = 'small';
        }

        return $input;
    }


    function add_admin_scripts() {
        // wp_enqueue_script...
        $plugin_js_url = plugins_url( 'm77-spotify.js', __FILE__ );
        wp_enqueue_script('jquery');
        wp_enqueue_script('m77_spotify_script', $plugin_js_url, array('jquery'));
  
    }

    function getOption(){
        return get_option(self::DB_OPTION_NAME, array('size'=>'small', 'custom-width'=>self::LARGE_WIDTH, 'custom-height'=>self::LARGE_HEIGHT, 'default-theme'=>'white', 'default-theme'=>'coverart'));
    }
    function spotity_shortcode($atts) {
        $saved_settings = $this->getOption();

        extract(shortcode_atts(array(
            "uri" => '',
            "size" => 'small'
        ), $atts));

        switch(strtolower($size)){
            case 'large':
                $sizes = array(self::LARGE_WIDTH, self::LARGE_HEIGHT, self::DEFAULT_THEME, self::DEFAULT_VIEW);
            break;
            case 'custom':
                // Fetch from custom settings
                $sizes = array($saved_settings['custom-width'], $saved_settings['custom-height'], $saved_settings['default-theme'], $saved_settings['default-theme']);
            break;
            case 'small':
            default:
                $sizes = array(self::SMALL_WIDTH, self::SMALL_HEIGHT, self::DEFAULT_THEME, self::DEFAULT_VIEW); // Default to small
            break;
            
        }
        return '<iframe src="https://embed.spotify.com/?uri='. $uri .'&theme='. $sizes[2] .'&view='. $sizes[3] .'" width="'. $sizes[0] .'" height="'. $sizes[1] .'" frameborder="0" allowtransparency="true"></iframe>';        
    }

    function meta_box_add()  
    {  
        add_meta_box( 'm77-spotify-box', 'Spotify Embed', array(&$this, 'meta_box_data'), 'post', 'side'); // Post
        add_meta_box( 'm77-spotify-box', 'Spotify Embed', array(&$this, 'meta_box_data'), 'page', 'side'); // Page
    } 

    function meta_box_data( $post )  
    {  
        $saved_settings = $this->getOption();

        $last_used_size     = isset( $saved_settings['last-used-size'] )  ? esc_attr( $saved_settings['last-used-size'] ) : 'small';
        
        ?>  
            <table>
                <tr>
                    <td><label for="m77_spotify_meta_uri">Spotify URI</label>  </td>
                    <td><input type="text" name="m77_spotify_meta_uri" id="m77_spotify_meta_uri" value="" /> <span id="m77_spotify_help_icon" style="padding: 0 5px; cursor: pointer">(?)</span></td>
                </tr>
                <tr>
                    <td><label for="m77_spotify_meta_size">Appearance</label></td>
                    <td>
                        <select name="m77_spotify_meta_size" id="m77_spotify_meta_size"> 
                            <option value="small" <?php selected( $last_used_size, 'small' ); ?>>Compact</option> 
                            <option value="large" <?php selected( $last_used_size, 'large' ); ?>>Large</option> 
                            <option value="custom" <?php selected( $last_used_size, 'custom' ); ?>>Custom</option> 
                        </select>
                    </td>
                </tr>
            </table>
            <p id="m77-spotify-last-used-custom" <?=($last_used_size != 'custom')?'style="display:none"':''?>>
                Change custom appearance in
                <br/><em>Settings->Spotify Embed</em>.
            </p>
            <div id="m77_spotify_help" style="display:none">
                <img src="<?=plugins_url( 'spotify_copyURI.png', __FILE__ )?>" width="100%"/>
            </div>
            <p>
                <a class="button" onclick="addSpotifyShortcode(); return false;">Insert to content</a>
                <div id="m77_spotify_alert_msg"></div>
            </p>  
        <?
    } 

     
    function meta_box_save( $post_id )  
    {  

        // Bail if we're doing an auto save  
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; 
     
        // if our current user can't edit this post, bail  
        if( !current_user_can( 'edit_posts' ) ) return;  
      
         // Make sure your data is set before trying to save it  
        //if( isset( $_POST['m77_spotify_meta_uri'] ) )  
        //    update_post_meta( $post_id, 'm77_spotify_meta_uri', wp_kses( $_POST['m77_spotify_meta_uri'], $allowed ) );  
      
        // This is purely my personal preference for saving check-boxes  
        //$chk = isset( $_POST['my_meta_box_check'] ) && $_POST['my_meta_box_select'] ? 'on' : 'off';  
        //update_post_meta( $post_id, 'my_meta_box_check', $chk ); 

        // Save latest size to options instead of page
        if(isset($_POST['m77_spotify_meta_size'])){
            $saved_settings = $this->getOption();
            $saved_settings['last-used-size'] = $_POST['m77_spotify_meta_size'];
            update_option(self::DB_OPTION_NAME, $saved_settings );
        }
    } 

    // PHP4 compatibility
    function M77Spotify() {
        $this->__construct();
    }
}

// Start this plugin once all other plugins are fully loaded
add_action( 'init', 'M77Spotify_init' ); 
function M77Spotify_init() { 
	global $_M77Spotify; 
	$_M77Spotify = new M77Spotify(); 
}
