<?php
// admin enqueue
function admin_enqueue($hook) {
    wp_enqueue_style('admin-jquery-ui-css', get_stylesheet_directory_uri() . '/admin/css/jquery-ui.css', array(), time(), 'all');
    wp_enqueue_style('admin-custom-css', get_stylesheet_directory_uri() . '/admin/css/admin.css', array(), time(), 'all');
    wp_enqueue_script('admin-jquery-ui-script', get_stylesheet_directory_uri() . '/admin/js/jquery-ui.js', array('jquery'), time(), true);
    wp_enqueue_script('admin-custom-script', get_stylesheet_directory_uri() . '/admin/js/admin.js', array('jquery'), time(), true);
}
add_action('admin_enqueue_scripts', 'admin_enqueue');

// event add custom box
function event_add_custom_box(){
    $screens = ['post', 'tribe_events'];
    foreach ($screens as $screen) {
        add_meta_box(
            'event_custom_box_id',
            'Choise Hotel',
            'event_custom_box_html',
            $screen
        );
    }
}
add_action('add_meta_boxes', 'event_add_custom_box');

// get data hotel event
function get_data_hotel_event($data_hotel_event){
    ob_start(); $html='';
    if(!empty($data_hotel_event)){
        foreach($data_hotel_event as $key => $value){
            $field_text = isset($value['field_text']) ? $value['field_text'] : '';
            ?>
            <div class="box-data-variations" data-hotel="<?php echo $value['hotel_id']; ?>" data-product="<?php echo $value['product_id']; ?>">
                <div class="box-header">
                    <div class="box-title">
                    <p class="title-hotels"><?php echo get_the_title($value['hotel_id']); ?></p>
                    </div>
                    <div class="box-action">
                    <a href="#" class="button-primary button-edit">Edit</a>
                    <a href="#" class="button-remove" data-hotel="#<?php echo $value['hotel_id']; ?>_hotel">Remove</a>
                    </div>
                </div>
                <input type="text" class="field-text-hotel" value="<?php echo $field_text; ?>">
                <div class="data-variations">
                    <?php
                        $variations_data = $value['variations_data'];
                        if(!empty($variations_data)){
                            foreach($variations_data as $k => $v){
                                ?>
                                <div class="variations" data-variations="<?php echo $v['variations_id']; ?>">
                                    <div class="box-type">
                                        <label class="font-w-bold"><?php echo get_post_meta($v['variations_id'], 'attribute_type-of-rooms', true); ?></label>
                                    </div>
                                    <div class="box-type">
                                        <p>Maximum guest per room</p>
                                        <input type="number" name="maximum" class="maximum" value="<?php echo $v['maximum']; ?>">
                                    </div>
                                    <div class="box-type">
                                        <p>Price</p>
                                        <input type="number" name="price" class="price" value="<?php echo $v['price']; ?>">
                                    </div>
                                    <div class="box-type">
                                        <p>Date available</p>
                                        <a href="#" class="button-primary button-setdate" data-variation="#<?php echo $v['variations_id']; ?>_variation">Set Date</a>
                                    </div>
                                    <div id="<?php echo $v['variations_id']; ?>_variation_available">
                                        <div class="day-available">
                                            <?php    
                                                if(isset($v['date_available'])){
                                                    $day_available = $v['date_available'];
                                                    foreach($day_available as $kk => $vl){
                                                        ?>
                                                        <input type='hidden' name='day' value='<?php echo $vl['date']; ?>' data-stock='<?php echo $vl['stock']; ?>'>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    ?>
                </div>
            </div>
        <?php
        }
    }
    $html = ob_get_clean();
    return $html;
}

// get lis it hotel html
function get_list_id_hotel_html($data_hotel_event){
    ob_start(); $html='';
    if(!empty($data_hotel_event)){
        foreach($data_hotel_event as $key => $value){
            $html .= '<input type="hidden" id="'.$value['hotel_id'].'_hotel" class="hotel-id" name="hotel_id[]" value="'.$value['hotel_id'].'">';
        }
    }
    return $html;
}

// event custom box html
function event_custom_box_html($post){
    $args = array(
        'post_type' => 'hotel',
        'posts_per_page' => -1,
        'order' => 'DESC', 
        'orderby' => 'date'
    );

    $custom_query = new WP_Query($args);
    $event_id = get_the_ID();
    $data_hotel_event = get_post_meta($event_id, 'data_hotel_event', true);
    $html_data_hotel_event = get_data_hotel_event($data_hotel_event);
    $html_list_id_hotel = get_list_id_hotel_html($data_hotel_event);
    if ($custom_query->have_posts()) {
        echo "<select class='select-hotel-event'>";
        echo "<option>Select Hotel</option>";
        while ($custom_query->have_posts()) {
            $custom_query->the_post(); 
            $product_id = (int) get_post_meta(get_the_ID(), 'product_of_hotels', true);
            $variations = get_all_variations_by_product_id($product_id);
            if($variations){
                echo "<option value=".get_the_ID().">".get_the_title()."</option>";
            }
        }
        echo "</select>";
        echo "<div class='wrap-box-data-variations'>".$html_data_hotel_event."</div>";
        echo "<div id='hiddenInputs'>".$html_list_id_hotel."</div>";
        echo "<span class='loader'></span>";
        echo "<div class='modal-calendar'>
                <svg class='close-icon' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
                    <line x1='1' y1='1' x2='23' y2='23' stroke='black' stroke-width='2'/>
                    <line x1='23' y1='1' x2='1' y2='23' stroke='black' stroke-width='2'/>
                </svg>
                <div id='calendar-available'></div>
            </div>";
        echo "<div class='bg-modal-open'></div>";
        echo "<div class='modal-set-stock-day'>
            <svg class='close-icon' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
                <line x1='1' y1='1' x2='23' y2='23' stroke='black' stroke-width='2'/>
                <line x1='23' y1='1' x2='1' y2='23' stroke='black' stroke-width='2'/>
            </svg>
            <span class='day-select-text'>Day 22-5-2024</span>
            <input type='hidden' value class='day-select'>
            <label>Quantity</label>
            <input type='number' value='' class='stock-day'>
            <label class='switch'>
                <input type='checkbox' class='action-day-switch'>
                <span class='slider round'></span>
            </label>
            <a href='#' class='button-primary save-each-date-select'>Save</a>
        </div>";
        echo "<div class='bg-modal-open-stock'></div>";
        echo "<a href='#' class='button-primary save-variation-data' data-event='".$event_id."'>Save</a>";
        wp_reset_postdata();
    }
}

// get all variations by product id
function get_all_variations_by_product_id($product_id) {
    $args = array(
        'post_type'   => 'product_variation', 
        'post_parent' => $product_id,
        'fields'      => 'ids',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'ASC'
    );

    $query = new WP_Query($args);
    if(!empty($query->posts)){
        return $query->posts;
    }else{
        return false;
    }
}

// ajax select event hotel
function select_event_hotel(){
    $hotel_id = isset($_POST['hotel_id']) ? (int) $_POST['hotel_id'] : "";
    $html = '';
    $success = false;
    if($hotel_id){
        $product_id = (int) get_post_meta($hotel_id, 'product_of_hotels', true);
        $variations = get_all_variations_by_product_id($product_id);
        if ($variations) {
            $html .= "<div class='box-data-variations' data-hotel='".$hotel_id."' data-product='".$product_id."'>";
            $html .= "<div class='box-header'>
                        <div class='box-title'>
                            <p class='title-hotels'>".get_the_title($product_id)."</p>
                        </div>
                        <div class='box-action'>
                            <a href='#' class='button-primary button-edit'>Edit</a>
                            <a href='#' class='button-remove' data-hotel='#".$hotel_id."_hotel'>Remove</a>
                        </div>
                    </div>";
            $html .= "<input type='text' class='field-text-hotel' value=''>";
            $html .= "<div class='data-variations'>";
            foreach ($variations as $variation_id) {
                $variation = wc_get_product($variation_id);
                $html .= "<div class='variations' data-variations='".$variation_id."'>
                            <div class='box-type'>
                                <label class='font-w-bold'>".get_post_meta($variation_id, 'attribute_type-of-rooms', true)."</label>
                            </div>
                            <div class='box-type'>
                                <p>Maximum guest per room</p>
                                <input type='number' name='maximum' class='maximum' value='".get_post_meta($variation_id, '_description', true)."'>
                            </div>
                            <div class='box-type'>
                                <p>Price</p>
                                <input type='number' name='price' class='price' value='".$variation->get_price()."'>
                            </div>
                            <div class='box-type'>
                                <p>Date available</p>
                                <a href='#' class='button-primary button-setdate' data-variation='#".$variation_id."_variation'>Set Date</a>
                            </div>
                            <div id='".$variation_id."_variation_available'>
                                <div class='day-available'>
                                </div>
                            </div>
                        </div>";
            }
            $html .= "</div>";
            $html .= "</div>";
            $success = true;
        }
    }

    $return = array(
        'success' => $success,
        'hotel_id' => $hotel_id,
        'html' => $html
    );

    wp_send_json($return);
}
add_action('wp_ajax_select_event_hotel', 'select_event_hotel');
add_action('wp_ajax_nopriv_select_event_hotel', 'select_event_hotel');

// save variation data
function save_variation_data(){
    $data = $_POST['postData'];
    $event_id = (int) $_POST['event_id'];
    update_post_meta($event_id, 'data_hotel_event', $data);
    wp_die();
}
add_action('wp_ajax_save_variation_data', 'save_variation_data');
add_action('wp_ajax_nopriv_save_variation_data', 'save_variation_data');
?>