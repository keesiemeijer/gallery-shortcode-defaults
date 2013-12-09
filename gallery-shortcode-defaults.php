<?php
/*
Plugin Name: Gallery Shortcode Defaults
Plugin URI:
Description: Set new gallery defaults in the media uploader. This plugin sets the attachment link url to "Media File" instead of "Attachment Page". Other defaults like 'Columns', 'Random Order' and 'Size' can be set in your (child) theme's functions.php file.
Author: keesiemeijer
Version: 0.1

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version. You may NOT assume that you can use any other version of the GPL.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


if ( !class_exists( 'GSD_Gallery_Shortcode_Defaults' ) ) {

    class GSD_Gallery_Shortcode_Defaults {

        /**
         * Class instance.
         *
         * @since 0.1
         * @see get_instance()
         * @var object
         */
        private static $instance = null;

        /**
         * Image sizes
         *
         * @since 0.1
         * @var array
         */
        public $sizes = array();

        /**
         * Default gallery arguments
         *
         * @since 0.1
         * @var array
         */
        public $args = array();


        /**
         * Acces this plugin's working instance.
         *
         * @since 0.1
         *
         * @return object
         */
        public static function get_instance() {
            // create a new object if it doesn't exist.
            is_null( self::$instance ) && self::$instance = new self;
            return self::$instance;
        }


        /**
         * Enqueue media scipts.
         *
         * @since 0.1
         */
        public static function init() {

            if ( is_admin() ) {
                add_action( 'wp_enqueue_media', array( self::get_instance(), 'wp_enqueue_media' ) );
            }

        }


        /**
         * Enqueues the script.
         *
         * @since 0.1
         */
        public function wp_enqueue_media() {

            $screen = get_current_screen();

            if ( !( isset( $screen->base ) && ( $screen->base === 'post' ) ) )
                return;

            $this->sizes = apply_filters( 'image_size_names_choose', array(
                    'thumbnail' => __( 'Thumbnail' ),
                    'medium'    => __( 'Medium' ),
                    'large'     => __( 'Large' ),
                    'full'      => __( 'Full Size' ),
                ) );


            // Media uploader defaults.
            $defaults = $tmp_defaults = array(
                'link'           => 'post',
                'columns'        => 3,
                '_orderbyRandom' => '',
                'size' => '',
            );

            // set new default 'link'
            $defaults['link'] = 'file';

            // Let users filter defaults.
            $args = (array) apply_filters( 'media_uploader_gallery_defaults' , $defaults );

            // validate filtered arguments
            $args = $this->validate_args( $args, $defaults );

            // If there are new defaults enqueue script to set new defaults in the media uploader.
            if ( $args != $tmp_defaults ) {

                $this->args = array_filter( $args );

                if ( isset( $this->args['size'] ) ) {
                    add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );
                }

                wp_enqueue_script(
                    'gallery-shortcode-defaults',
                    plugins_url( 'gallery-shortcode-defaults.js', __FILE__ ),
                    array( 'media-views' )
                );

                // Creates javascript object 'gallery_defaults'.
                wp_localize_script( 'gallery-shortcode-defaults', 'gallery_shortcode_defaults',  $this->args );
            }
        }



        /**
         * Outputs the view template with the size setting.
         *
         * @since 0.1
         */
        public function print_media_templates() {

            $screen = get_current_screen();

            if ( !( isset( $screen->base ) && ( $screen->base === 'post' ) ) )
                return;

            // image size
            if ( isset( $this->args['size'] ) ) {
?>
            <script type="text/html" id="tmpl-custom-size-setting">
            <label class="setting">
                <span><?php _e( 'Size', 'gallery-shortcode-defaults' ); ?></span>
                <select class="type" name="size" data-setting="size">
                    <?php
                foreach ( $this->sizes as $value => $name ) { ?>
                        <option value="<?php esc_attr_e( $value ); ?>" <?php selected( $value, $this->args['size'] ); ?>>
                            <?php echo esc_html( $name ); ?>
                        </option>
                    <?php } ?>
                </select>
            </label>
            </script>

<?php
            } // end  image size

        } // end print_media_templates()


        /**
         * Validate filtered arguments.
         *
         * @since 0.1
         *
         * @param array   $args     Filtered arguments.
         * @param array   $defaults Default arguments.
         * @return array           Validated arguments.
         */
        private function validate_args( $args, $defaults ) {

            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            if ( !in_array( (string) $link, array( 'post', 'file', 'none' ) ) ) {
                $args['link'] = 'post';
            }

            $columns = absint( $columns );
            if ( !in_array( $columns , range( 1, 9 ) ) ) {
                $args['columns'] = 3;
            }

            if ( 'on' !== $_orderbyRandom ) {
                $args['_orderbyRandom'] = '';
            }

            if ( !in_array( (string) $size, array_keys( $this->sizes ) ) ) {
                $args['size'] = '';
            }

            // remove wrong arguments
            $tmp_args = array_diff_key( $args, $defaults );
            foreach ( $tmp_args as $key => $value ) {
                unset( $args[ $key ] );
            }

            return $args;
        }


    } // class

    GSD_Gallery_Shortcode_Defaults::init();

} // class exists
