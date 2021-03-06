<?php
/**
 * An example of using the settings API with a class
 *
 * @since       1.0
 * @author      Christopher Davis <http://pmg.co/people/chris>
 * @copyright   Performance Media Group 2012
 * @license     GPLv2
 */

class PMG_Settings_Tutorial
{
    /**
     * Our setting name
     * 
     * @since   1.0
     */
    const SETTING = 'pmgtut_class_setting';

    /**
     * Page on which our options will reside
     *
     * @since   1.0
     */
    const PAGE = 'sample_options_page';

    /**
     * Our section ID
     *
     * @since   1.0
     */
    const SECTION = 'pmgtut_class_section';

    /**
     * Called at the bottom of this file to add actions.
     *
     * @since   1.0
     * @access  public
     * @uses    add_action
     */
    public static function init()
    {
        add_action(
            'admin_init',
            array(__CLASS__, 'register')
        );

        add_action(
            'admin_menu',
            array(__CLASS__, 'menu_page')
        );
    }

    /**
     * Hooked into `admin_init` this is the central function that takes care of 
     * registering the setting, adding sections, and adding fields
     *
     * @since   1.0
     * @access  public
     * @uses    register_setting
     * @uses    add_settings_section
     * @uses    add_settings_field
     */
    public static function register()
    {
        register_setting(
            self::PAGE,
            self::SETTING,
            array(__CLASS__, 'validate')
        );

        // at this point WP knows about your setting.
        add_settings_section(
            self::SECTION,
            __('Example Class Section', 'pmg'),
            '__return_false',
            self::PAGE
        );

        $fields = array(
            'email'  => __('Your Email', 'pmg'),
            'normal' => __('Some Text', 'pmg')
        );

        foreach($fields as $key => $label)
        {
            add_settings_field(
                $key,
                $label,
                array(__CLASS__, 'field_cb'),
                self::PAGE,
                self::SECTION,
                array('key' => $key) // option args
            );
        }
    }

    /**
     * settings validation callback
     *
     * @since   1.0
     * @uses    add_settings_error
     * @uses    esc_attr
     * @uses    is_email
     * @return  array The cleaned options
     */
    public static function validate($dirty)
    {
        $clean = array();

        if(isset($dirty['email']) && is_email($dirty['email']))
        {
            $clean['email'] = $dirty['email'];
        }
        else
        {
            add_settings_error(
                'pmgtut_setting',
                'pmg-invalid-email',
                __('Please enter a valid email', 'pmg'),
                'error'
            );
        }

        $clean['normal'] = isset($dirty['normal']) ? esc_attr($dirty['normal']) : '';

        return $clean;
    }

    /**
     * Example of a unified callback
     *
     * @since   1.0
     * @uses    get_option
     */
    public static function field_cb($args)
    {
        // get our options
        $opts = get_option(self::SETTING, array());

        // Set up the value with a ternary statment
        $val = isset($opts[$args['key']]) ? $opts[$args['key']] : '';

        // actually print the field
        printf(
            '<input type="text" class="regular-text" id="%1$s[%2$s]" name="%1$s[%2$s]" value="%3$s" />',
            esc_attr(self::SETTING),
            esc_attr($args['key']),
            esc_attr($val)
        );
    }

    /**
     * Register a new admin page
     *
     * @since   1.0
     * @access  public
     * @uses    add_options_page
     */
    public static function menu_page()
    {
        $p = add_options_page(
            __('Sample Options Page', 'pmg'),
            __('Sample Options', 'pmg'),
            'manage_options',
            'pmg-sample-options',
            array(__CLASS__, 'menu_page_cb')
        );
    }

    /**
     * Menu page callback function
     *
     * @since   1.0
     * @access  public
     * @uses    settings_field
     * @uses    do_settings_section
     * @uses    screen_icon
     */
    public static function menu_page_cb()
    {
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php esc_html_e('Sample Options Page', 'pmg'); ?></h2>
            <form action="<?php echo admin_url('options.php'); ?>" method="post">
                <?php
                settings_fields(self::PAGE);
                do_settings_sections(self::PAGE);
                submit_button(__('Save Settings', 'pmg'));
                ?>
            </form>
        </div>
        <?php
    }
} // end class

PMG_Settings_Tutorial::init();
