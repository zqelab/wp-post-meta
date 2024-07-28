<?php
namespace Zqe;

class Wp_Post_Meta {

    private $post_type;
    private $fields = [];

    public function __construct( $post_type, $fields = [] ) {
        
        $this->post_type = $post_type;
        $this->fields    = $fields;

        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post', [ $this, 'save' ], 10, 2 );
    }

    /**
     * Enqueue scripts and styles
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_media();
        wp_enqueue_style( 'zqe-post-meta', plugin_dir_url( __FILE__ ) . 'css/zqe-post-meta.css', array( 'wp-color-picker' ), '1.0.0', 'all' );
        wp_enqueue_script( 'zqe-post-meta', plugin_dir_url( __FILE__ ) . 'js/zqe-post-meta.js', array( 'jquery', 'wp-color-picker' ), '1.0.0', true );
    }

    /**
     * Add meta boxes
     *
     * @since    1.0.0
     */
    public function add_meta_boxes() {
        add_meta_box(
            'zqe_post_meta_box',
            __( 'Post Meta Box', 'textdomain' ),
            [ $this, 'render_meta_box' ],
            $this->post_type,
            'advanced',
            'high'
        );
    }

    /**
     * Render meta box
     *
     * @since    1.0.0
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box( $post ) {
        wp_nonce_field( basename( __FILE__ ), 'post_meta_nonce' );

        echo '<table class="form-table">';
        foreach ( $this->fields as $field ) {
            $field['value'] = get_post_meta( $post->ID, $field['id'], true );
            $field['size'] = isset( $field['size'] ) ? $field['size'] : '40';
            $field['required'] = ( isset( $field['required'] ) && $field['required'] == true ) ? ' aria-required="true"' : '';
            $field['placeholder'] = ( isset( $field['placeholder'] ) ) ? ' placeholder="' . $field['placeholder'] . '"' : '';
            $field['desc'] = ( isset( $field['desc'] ) ) ? $field['desc'] : '';
            $field['dependency'] = ( isset( $field['dependency'] ) ) ? $field['dependency'] : array();

            echo '<tr>';
            echo '<th scope="row">';
            echo '<label for="' . esc_attr( $field['id'] ) . '">' . $field['label'] . '</label>';
            echo '</th>';
            echo '<td>';
            if ( isset( $field['repeatable'] ) && $field['repeatable'] ) {
                echo $this->repeatable( $field, $post );
            } elseif ( $field['type'] === 'group' ) {
                echo $this->render_box( $field['fields'], $post );
            } else {
                $this->render_field( $field, $post );
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Render a box of fields
     *
     * @since    1.0.0
     *
     * @param array $fields The array of fields to render.
     * @param WP_Post $post The post object.
     * @return string The HTML for the fields.
     */
    public function render_box( $fields, $post ) {
        ob_start();
        echo '<table width="100%" border="1">';
        foreach ( $fields as $field ) {
            echo '<tr>';
            echo '<td>';
            echo '<label for="' . esc_attr( $field['id'] ) . '">' . $field['label'] . '</label>';
            echo '</td>';
            echo '<td>';
            if ( $field['type'] === 'group' ) {
                echo $this->render_box( $field['fields'], $post );
            } else {
                $this->render_field( $field, $post );
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        return ob_get_clean();
    }

    /**
     * Render a single field
     *
     * @since    1.0.0
     *
     * @param array $field Field parameters.
     * @param WP_Post $post The post object.
     */
    private function render_field( $field, $post ) {
        switch ( $field['type'] ) {
            case 'text':
            case 'url':
                echo $this->text( $field );
                break;
            case 'color':
                echo $this->color( $field );
                break;
            case 'textarea':
                echo $this->textarea( $field );
                break;
            case 'editor':
                echo $this->editor( $field );
                break;
            case 'select':
            case 'select2':
                echo $this->select( $field );
                break;
            case 'image':
                echo $this->image( $field );
                break;
            case 'checkbox':
                echo $this->checkbox( $field );
                break;
            case 'callback':
                echo $this->field_callback( $field );
                break;
            default:
                echo $field['type']; // For debugging purposes
                break;
        }
    }

    /**
     * Generate text input field
     *
     * @since    1.0.0
     *
     * @param array $field Field parameters.
     * @return string Field HTML.
     */
    private function text( $field ) {
        ob_start();
        ?>
        <input 
            name="<?php echo esc_attr( $field['id'] ) ?>" 
            id="<?php echo esc_attr( $field['id'] ) ?>" 
            type="<?php echo esc_attr( $field['type'] ) ?>" 
            value="<?php echo esc_attr( $field['value'] ) ?>" 
            size="<?php echo esc_attr( $field['size'] ) ?>" 
            <?php echo $field['required'] ?> 
            <?php echo $field['placeholder'] ?>>
        <?php
        return ob_get_clean();
    }

    /**
     * Generate color input field
     *
     * @since    1.0.0
     *
     * @param array $field Field parameters.
     * @return string Field HTML.
     */
    private function color( $field ) {
        ob_start();
        ?>
        <input 
            name="<?php echo esc_attr( $field['id'] ) ?>" 
            id="<?php echo esc_attr( $field['id'] ) ?>" 
            type="text"
            class="zqe-post-meta-color-picker" 
            value="<?php echo esc_attr( $field['value'] ) ?>"
            data-default-color="<?php echo esc_attr( $field['value'] ) ?>"
            size="<?php echo esc_attr( $field['size'] ) ?>" <?php echo $field['required'] ?> 
            <?php echo $field['placeholder'] ?>>
        <?php
        return ob_get_clean();
    }

    /**
     * Generate textarea field
     *
     * @since    1.0.0
     *
     * @param array $field Field parameters.
     * @return string Field HTML.
     */
    private function textarea( $field ) {
        ob_start();
        ?>
        <textarea 
            name="<?php echo esc_attr( $field['id'] ) ?>" 
            id="<?php echo esc_attr( $field['id'] ) ?>" 
            rows="5"
            cols="<?php echo esc_attr( $field['size'] ) ?>" 
            <?php echo $field['required'] ?> 
            <?php echo $field['placeholder'] ?>>
            <?php echo esc_textarea( $field['value'] ) ?>
        </textarea>
        <?php
        return ob_get_clean();
    }

    /**
     * Generate editor field
     *
     * @since    1.0.0
     *
     * @param array $field Field parameters.
     * @return string Field HTML.
     */
    private function editor( $field ) {
        $field['settings'] = isset( $field['settings'] ) ? $field['settings'] : [
            'textarea_rows' => 8,
            'quicktags' => false,
            'media_buttons' => false
        ];
        ob_start();
        wp_editor( $field['value'], $field['id'], $field['settings'] );
        return ob_get_clean();
    }

    /**
     * Generate select field
     *
     * @since    1.0.0
     *
     * @param array $field Field parameters.
     * @return string Field HTML.
     */
    private function select( $field ) {
        $field['options'] = isset( $field['options'] ) ? $field['options'] : array();
        $field['multiple'] = isset( $field['multiple'] ) ? ' multiple="multiple"' : '';
        $css_class = ( $field['type'] == 'select2' ) ? 'wc-enhanced-select' : '';
        ob_start();
        ?>
        <select 
            name="<?php echo esc_attr( $field['id'] ) ?>" 
            id="<?php echo esc_attr( $field['id'] ) ?>"
            class="<?php echo esc_attr( $css_class ) ?>" 
            <?php echo $field['multiple'] ?>>
            <?php
            foreach ( $field['options'] as $key => $option ) {
                echo '<option' . selected( $field['value'], $key, false ) . ' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
            }
            ?>
        </select>
        <?php
        return ob_get_clean();
    }

    /**
     * Generate image field
     *
     * @since    1.0.0
     *
     * @param array $field Field parameters.
     * @return string Field HTML.
     */
    private function image( $field ) {
        ob_start();
        ?>
        <div class="zqe-post-meta-image-field-wrapper">
            <div class="zqe-post-meta-image-field-preview">
                <img data-placeholder="<?php echo esc_url( self::placeholder_img_src() ); ?>" src="<?php echo esc_url( self::get_img_src( $field['value'] ) ); ?>" width="100px" height="100px"/>
            </div>
            <div class="zqe-post-meta-image-field-button-wrapper">
                <input type="hidden" id="<?php echo esc_attr( $field['id'] ) ?>" name="<?php echo esc_attr( $field['id'] ) ?>" value="<?php echo esc_attr( $field['value'] ) ?>"/>
                <button type="button" class="zqe-post-meta-image-field-upload-button button button-primary button-small">
                    <?php esc_html_e( 'Upload', 'textdomain' ); ?> 
                </button>
                <button type="button" class="zqe-post-meta-image-field-remove-button button button-danger button-small" style="<?php echo ( empty( $field['value'] ) ? 'display:none' : '' ) ?>" >
                    <?php esc_html_e( 'Remove', 'textdomain' ); ?> 
                </button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Generate checkbox field
     *
     * @since    1.0.0
     *
     * @param array $field Field parameters.
     * @return string Field HTML.
     */
    private function checkbox( $field ) {
        ob_start();
        ?>
        <label for="<?php echo esc_attr( $field['id'] ) ?>">
            <input 
            name="<?php echo esc_attr( $field['id'] ) ?>" 
            id="<?php echo esc_attr( $field['id'] ) ?>" 
            type="<?php echo esc_attr( $field['type'] ) ?>" 
            value="yes" <?php echo $field['required'] ?> 
            <?php echo $field['placeholder'] ?> 
            <?php checked( $field['value'], 'yes' ) ?>>
            <?php echo esc_html( $field['label'] ) ?>
        </label>
        <?php
        return ob_get_clean();
    }

    /**
     * Generate repeatable fields
     *
     * @since    1.0.0
     *
     * @param array $field Field parameters.
     * @param WP_Post $post The post object.
     * @return string Field HTML.
     */
    private function repeatable( $field, $post ) {
        $field_values = get_post_meta( $post->ID, $field['id'], true );

        ob_start();
        ?>
        <div class="zqe-post-meta-repeatable">
            <button type="button" class="button add-repeatable"><?php esc_html_e( 'Add', 'textdomain' ); ?></button>
            <?php
            if ( ! empty( $field_values ) && is_array( $field_values ) ) {
                foreach ( $field_values as $index => $value ) {
                    ?>
                    <div class="repeatable-item">
                        <button type="button" class="button remove-repeatable"><?php esc_html_e( 'Remove', 'textdomain' ); ?></button>
                        <?php
                        foreach ( $field['fields'] as $subfield ) {
                            $subfield['id'] = $field['id'] . '[' . $index . '][' . $subfield['id'] . ']';
                            $subfield['value'] = isset( $value[ $subfield['id'] ] ) ? $value[ $subfield['id'] ] : '';
                            $this->render_field( $subfield, $post );
                        }
                        ?>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="repeatable-item">
                    <button type="button" class="button remove-repeatable"><?php esc_html_e( 'Remove', 'textdomain' ); ?></button>
                    <?php
                    foreach ( $field['fields'] as $subfield ) {
                        $subfield['id'] = $field['id'] . '[0][' . $subfield['id'] . ']';
                        $subfield['value'] = '';
                        $this->render_field( $subfield, $post );
                    }
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Handle callback field type
     *
     * @since    1.0.0
     */
    private function field_callback( $field ) {
        return call_user_func_array( $field['callback'], [ (array) $field ] );
    }

    /**
     * Get image source URL
     *
     * @since    1.0.0
     *
     * @param int $thumbnail_id Thumbnail ID.
     * @return string Image URL.
     */
    private function get_img_src( $thumbnail_id = false ) {
        if ( ! empty( $thumbnail_id ) ) {
            $image = wp_get_attachment_thumb_url( $thumbnail_id );
        } else {
            $image = self::placeholder_img_src();
        }
        return $image;
    }

    /**
     * Get placeholder image source URL
     *
     * @since    1.0.0
     *
     * @return string Placeholder image URL.
     */
    private function placeholder_img_src() {
        return plugin_dir_url( __FILE__ ) . 'imgs/placeholder.png';
    }

    /**
     * Save meta box data
     *
     * @since    1.0.0
     *
     * @param int $post_id Post ID.
     * @param WP_Post $post The post object.
     */
    public function save( $post_id, $post ) {
        // Check nonce
        if ( ! isset( $_POST['post_meta_nonce'] ) || ! wp_verify_nonce( $_POST['post_meta_nonce'], basename( __FILE__ ) ) ) {
            return $post_id;
        }

        // Check for autosave or user permissions
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        foreach ( $this->fields as $field ) {
            if ( isset( $field['repeatable'] ) && $field['repeatable'] ) {
                if ( isset( $_POST[ $field['id'] ] ) ) {
                    $sanitized_values = array();
                    foreach ( $_POST[ $field['id'] ] as $group ) {
                        $sanitized_group = array();
                        foreach ( $group as $key => $value ) {
                            $subfield_type = $this->get_subfield_type( $field['fields'], $key );
                            $sanitized_group[ $key ] = self::sanitize( $subfield_type, $value );
                        }
                        $sanitized_values[] = $sanitized_group;
                    }
                    update_post_meta( $post_id, $field['id'], $sanitized_values );
                } else {
                    delete_post_meta( $post_id, $field['id'] );
                }
            } else {
                if ( isset( $_POST[ $field['id'] ] ) ) {
                    update_post_meta( $post_id, $field['id'], self::sanitize( $field['type'], $_POST[ $field['id'] ] ) );
                } else {
                    delete_post_meta( $post_id, $field['id'] );
                }
            }
        }
    }

    /**
     * Get the type of a subfield by its key.
     *
     * @param array $fields The array of subfields.
     * @param string $key The subfield key.
     * @return string|null The subfield type or null if not found.
     */
    private function get_subfield_type( $fields, $key ) {
        foreach ( $fields as $field ) {
            if ( $field['id'] === $key ) {
                return $field['type'];
            }
        }
        return null;
    }

    /**
     * Sanitize field values
     *
     * @since    1.0.0
     *
     * @param string $type Field type.
     * @param mixed $value Field value.
     * @return mixed Sanitized value.
     */
    public static function sanitize( $type, $value ) {
        switch ( $type ) {
            case 'text':
            case 'color':
                return esc_html( $value );
                break;
            case 'url':
                return esc_url( $value );
                break;
            case 'image':
                return absint( $value );
                break;
            case 'textarea':
                return esc_textarea( $value );
                break;
            case 'editor':
                return wp_kses_post( $value );
                break;
            case 'select':
            case 'select2':
                return sanitize_key( $value );
                break;
            case 'checkbox':
                return sanitize_key( $value );
                break;
            default:
                break;
        }
        return sanitize_text_field( $value );
    }
}
?>

