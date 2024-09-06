<?php
function add_ajaxurl_to_script()
{
    wp_localize_script('jquery', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('wp_enqueue_scripts', 'add_ajaxurl_to_script');

// function acf_load_select_hotel_event_choices($field)
// {

//     $field['choices'] = array();
//     $hotels = get_posts(
//         array(
//             'post_type' => 'hotel',
//             'posts_per_page' => -1,
//         )
//     );

//     if (!empty($hotels)) {
//         foreach ($hotels as $hotel) {
//             $field['choices'][$hotel->ID] = $hotel->post_title;
//         }
//     }
//     return $field;

// }
// add_filter('acf/load_field/key=field_66139646c1142', 'acf_load_select_hotel_event_choices');
function wporg_add_custom_box()
{
    $screens = ['post', 'tribe_events'];
    foreach ($screens as $screen) {
        add_meta_box(
            'wporg_box_id',
            'List Hotel',
            'wporg_custom_box_html',
            $screen
        );
    }
}
add_action('add_meta_boxes', 'wporg_add_custom_box');

function wporg_custom_box_html($post)
{
    ?>
    <div class="wrap-label-select"><label>List Hotel</label></div>
    <select id="hotel-select">
        <option value="" selected disabled>Choose Hotel</option>
    </select>
    <div class="box-meta-listhotel">
        <span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path
                    d="M304 48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zm0 416a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM48 304a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm464-48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM142.9 437A48 48 0 1 0 75 369.1 48 48 0 1 0 142.9 437zm0-294.2A48 48 0 1 0 75 75a48 48 0 1 0 67.9 67.9zM369.1 437A48 48 0 1 0 437 369.1 48 48 0 1 0 369.1 437z" />
            </svg>
        </span>
    </div>
    <?php
    $list_event = get_post_meta($post->ID, 'hotel_type-of-rooms', true);
    // var_dump($list_event);
    if($list_event){
        foreach ($list_event as $event) {
            $hotelId_Postmeta = $event['hotelId'];
            $post_title = get_the_title($hotelId_Postmeta);
            ?>
            <!-- <script type="text/javascript">
                jQuery(function ($) {
                    var id_post_hotel_disable = <?php echo $hotelId_Postmeta; ?>;
                    var hiddenInput = '<input type="hidden" readonly name="name_typeOfRoom" value="' + hotelId_Postmeta + '"/>';
                    $("#wporg_box_id .wrap-label-select").append(hiddenInput);
                });
            </script> -->
            <input class="hotelId_Postmeta" type="hidden" readonly name="hotelId_Postmeta" value="<?php echo $hotelId_Postmeta; ?>"/>
            <div id="box-typeOfRoom-<?php echo $hotelId_Postmeta; ?>" class="box-typeOfRoom">
                <div class="box-header">
                    <div class="box-title">
                        <p class="title-hotels"><?php echo $post_title ;?></p>
                    </div>
                    <div class="box-action">
                        <a class="button-primary button-edit">Edit</a>
                        <a class="button-remove">Remove</a>
                    </div>
                </div>
                <div class="box-content" data-id-hotel="<?php echo $hotelId_Postmeta ;?>">
                    <?php
                    if(isset($event['roomTypes'])){
                        foreach ($event['roomTypes'] as $room) {
                            $name = $room['name'];
                            $price = $room['price'];
                            $pricenew = $room['pricenew'];
                            $quantity = $room['quantity'];
                            $fooEvents = $room["fooEvents"];
                            $description = $room["descriptionTypeRoom"];
                            ?>
                            <div class="wrap_type-of-room">
                                <div class="box_type">
                                    <label class="font-w-bold"><?php echo $name; ?>:</label>
                                    <input type="hidden" readonly name="name_typeOfRoom" value="<?php echo $name; ?>" />
                                    <input type="hidden" readonly name="price_typeOfRoom" value="<?php echo $price; ?>" />
                                </div>
                                <div class="box_type">
                                    <p>Quantity</p>
                                    <input type="number" readonly name="quantity_typeOfRoom" value="<?php echo $quantity; ?>" />
                                </div>
                                <div class="box_type">
                                    <p>FooEvents Quantity</p>
                                    <input type="number" min="1" max="<?php echo $quantity; ?>" name="fooEvent_typeOfRoom" value="<?php echo $fooEvents;?>" />
                                </div>
                                <div class="box_type">
                                    <p>Maximum guest per room</p>
                                    <input type="number" name="description_typeOfRoom" value="<?php echo $description;?>" />
                                </div>
                                <div class="box_type">
                                    <p>Price</p>
                                    <input type="number" name="price_typeOfRoom_new" value="<?php echo $pricenew;?>" />
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
    ?>
    <button class="button-primary save-event-data-button" id="save-event-data-button">Save Change
        <span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path
                    d="M304 48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zm0 416a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM48 304a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm464-48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM142.9 437A48 48 0 1 0 75 369.1 48 48 0 1 0 142.9 437zm0-294.2A48 48 0 1 0 75 75a48 48 0 1 0 67.9 67.9zM369.1 437A48 48 0 1 0 437 369.1 48 48 0 1 0 369.1 437z" />
            </svg>
        </span>
    </button>
    <?php
}

add_action('admin_footer', 'save_purchase_qty_js');

function save_purchase_qty_js()
{ ?>
    <script id="save-purchase-qty" type="text/javascript">
        jQuery(function ($) {

            var postID = $('.wrap input[name="post_ID"]').val();
            
            var hotels = <?php
                    $hotel_data = array();
                    $hotel_posts = get_posts(
                        array(
                            'post_type' => 'hotel',
                            'posts_per_page' => -1,
                        )
                    );

                foreach ($hotel_posts as $hotel_post) {
                    $hotel_id = $hotel_post->ID;
                    $hotel_meta = get_post_meta($hotel_id, 'trueHotel_type-of-rooms', true);

                    $hotel_data[] = array(
                        'ID' => $hotel_id,
                        'post_title' => $hotel_post->post_title,
                        'type_of_rooms' => $hotel_meta,
                    );
                }

                echo json_encode($hotel_data);
            ?>;
            hotels.forEach(function (hotel) {
                var disableOption = false;
                $('#wporg_box_id .hotelId_Postmeta').each(function() {
                    var abcID = $(this).val();
                });
                $('#hotel-select').append('<option value="' + hotel.ID + '">' + hotel.post_title + '</option>');
            });

            $('#hotel-select').on('change', function () {
                var selectedHotel = $(this).find('option:selected').val();
                var boxId = 'box-typeOfRoom-' + selectedHotel;
                $('#wporg_box_id .box-meta-listhotel').addClass("active");
                if ($('#' + boxId).length === 0) {
                    var newBox = $('<div id="' + boxId + '" class="box-typeOfRoom"></div>');
                    $('#wporg_box_id .box-meta-listhotel').append(newBox);

                    var hotel_id = $(this).val();
                    $('#' + boxId).css('opacity', '0.3');
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {

                            action: 'get_type_of_room_hotel',
                            hotel_id: hotel_id,
                        },
                        success: function (response) {
                            $('#wporg_box_id .box-meta-listhotel').removeClass("active");
                            $('#' + boxId).css('opacity', '1');
                            $('#' + boxId).html(response.result);
                        },
                        error: function (err) {
                            $('#wporg_box_id .box-meta-listhotel').removeClass("active");
                            console.log(err);
                        }
                    });
                } else {
                    $('#wporg_box_id .box-meta-listhotel').removeClass("active");
                }
                
            });
            $(document).on("click", ".box-action .button-edit", function (e) {
                e.preventDefault();
                var boxContent = $(this).closest('.box-header').next('.box-content');
                boxContent.toggleClass('active');
                $('.box-content').not(boxContent).removeClass('active');
            });
            $(document).on("click", ".box-action .button-remove", function (e) {
                e.preventDefault();
                var boxContainer = $(this).closest('.box-typeOfRoom');
                boxContainer.remove();
            });
            $('#save-event-data-button').on('click', function (e) {
                e.preventDefault();
                saveEventData();
            });

            function saveEventData() {
                var postData = [];
                // var hotelId = $(this).data('id-hotel');
                $('.box-content').each(function () {
                    var hotelId = $(this).data('id-hotel');
                    var quantities = [];
                    var fooEvents = [];
                    var nameTypeRoom = [];
                    var priceTypeRoom = [];
                    var priceTypeRoomNew = [];
                    var descriptionTypeRoom = [];
                    var idTypeRoom = [];

                    $(this).find('input[name="name_typeOfRoom"]').each(function () {
                        nameTypeRoom.push($(this).val());
                    });

                    $(this).find('input[name="price_typeOfRoom"]').each(function () {
                        priceTypeRoom.push($(this).val());
                    });

                    $(this).find('input[name="price_typeOfRoom_new"]').each(function () {
                        priceTypeRoomNew.push($(this).val());
                    });

                    $(this).find('input[name="quantity_typeOfRoom"]').each(function () {
                        quantities.push($(this).val());
                    });

                    $(this).find('input[name="fooEvent_typeOfRoom"]').each(function () {
                        fooEvents.push($(this).val());
                    });
                    $(this).find('input[name="description_typeOfRoom"]').each(function () {
                        descriptionTypeRoom.push($(this).val());
                    });
                    $(this).find('input[name="product_id_typeroom"]').each(function () {
                        idTypeRoom.push($(this).val());
                    });

                    var boxData = {
                        hotelId: hotelId,
                        roomTypes: []
                    };

                    for (var i = 0; i < nameTypeRoom.length; i++) {
                        var roomType = {
                            id: idTypeRoom[i],
                            name: nameTypeRoom[i],
                            price: priceTypeRoom[i],
                            pricenew: priceTypeRoomNew[i],
                            quantity: quantities[i],
                            fooEvents: fooEvents[i],
                            descriptionTypeRoom: descriptionTypeRoom[i]
                        };
                        boxData.roomTypes.push(roomType);
                    }
                    postData.push(boxData);
                });
                $('.save-event-data-button').addClass("active");
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'save_event_data',
                        data: postData,
                        post_id: postID,
                    },
                    success: function (response) {
                        $('.save-event-data-button').removeClass("active");
                    },
                    error: function (err) {
                        console.log(err);
                        $('.save-event-data-button').removeClass("active");
                    }
                });
            }


        });
    </script>
    <style>
        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .box-meta-listhotel.active span {
            margin-left: 20px;
        }

        .box-meta-listhotel span {
            margin-left: 0px;
        }

        .box-meta-listhotel.active span svg {
            animation: rotate 2s linear infinite;
            display: inline-block;
        }

        .box-meta-listhotel span svg {
            width: 30px;
            display: none;

        }
        /*  */
        .save-event-data-button.active span {
            margin-left: 10px;
        }

        .save-event-data-button span {
            margin-left: 0px;
        }

        .save-event-data-button.active span svg {
            animation: rotate 2s linear infinite;
            display: inline-block;
            fill:#fff;
        }

        .save-event-data-button span svg {
            width: 15px;
            display: none;

        }
        /*  */
        #save-event-data-button {
            margin: 15px 0 10px 15px;
        }

        .wrap-label-select {
            margin-left: 15px;
        }

        .wrap-label-select label {
            display: block;
            font-weight: 500;
            padding: 0;
            line-height: 1.4;
            font-size: 13px;
        }

        #hotel-select {
            margin: 15px 0 10px 15px;
            width: calc(100% - 30px);
            border-radius: 5px;
            color: #444;
            line-height: 28px;
            font-size: 13px;
        }

        .box-typeOfRoom .spinner-border {
            display: none;
        }

        #button-add-typeOfRoom {
            margin: 15px 0 10px 15px;
            display: block;
        }

        .box-typeOfRoom {
            padding: 0 15px;
            margin-top: 15px;
            border-bottom: 1px solid #cccccc57;
            padding-bottom: 15px;
        }

        .box-typeOfRoom .wrap_type-of-room {
            display: flex;
            flex-direction: row;
            align-items: center;
            width: 70%;
            gap: 10px;
        }

        .box-typeOfRoom .wrap_type-of-room .box_type {
            width: calc(100% / 3 - 10px);
        }

        .box-typeOfRoom .wrap_type-of-room .box_type input {
            width: 100%;
        }

        .box-typeOfRoom .wrap_type-of-room .box_type p {
            margin: 0;
        }

        .box-typeOfRoom .wrap_type-of-room .box_type .font-w-bold {
            font-weight: 600;
        }

        .box-typeOfRoom .title-hotels {
            margin-bottom: 0;
            font-weight: 700;
            margin-top: 0;
        }

        .box-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .box-header .box-title {
            display: flex;
            align-items: center;
        }

        .box-header .box-action .button-remove {
            color: red;
            border-color: red;
            margin-left: 10px;
            border: 1px solid;
            display: inline-block;
            text-decoration: none;
            font-size: 13px;
            line-height: 2.15384615;
            min-height: 30px;
            margin: 0;
            padding: 0 10px;
            cursor: pointer;
            border-width: 1px;
            border-style: solid;
            -webkit-appearance: none;
            border-radius: 3px;
            white-space: nowrap;
            box-sizing: border-box;

        }

        .box-header .box-action .button-remove:hover {
            background: #cccccc4a;
        }

        .box-typeOfRoom .box-content {
            display: none;
        }

        .box-typeOfRoom .box-content.active {
            display: block;
        }
    </style>
    <?php
}
function get_type_of_room_hotel()
{
    if (isset($_POST['action']) && $_POST['action'] === 'get_type_of_room_hotel' && isset($_POST['hotel_id'])) {
        $hotel_id = $_POST['hotel_id'];
        echo ContentTypeOfRoom($hotel_id );
    } else {
        echo 'error';
    }
    wp_reset_postdata();

    $result = ob_get_contents();
    ob_end_clean();
    $return = array(
        'result' => $result,
    );
    wp_send_json($return);
}

add_action('wp_ajax_get_type_of_room_hotel', 'get_type_of_room_hotel');
add_action('wp_ajax_nopriv_get_type_of_room_hotel', 'get_type_of_room_hotel');

function ContentTypeOfRoom($hotel_id )
{
    // $hotel_id = $hotel_id;
    $content_hotel = '';
    $content_product = "";
    $show_edit_button = false;
    
    $typeOfRooms = get_field('add_type_of_room', $hotel_id);
    $product_id_variation = get_post_meta($hotel_id, 'product_of_hotels', true);

    if ($typeOfRooms && is_array($typeOfRooms)) {
        foreach ($typeOfRooms as $room) {
            $name = $room['name'];
            $price = $room['price'];
            $pricenew = $room['pricenew'];
            $quantity = $room['quantity'];
            $description = $room['description'];

            $args_post = array(
                'post_type' => 'product_variation',
                'post_parent' => $product_id_variation,
                'posts_per_page' => -1,
            );
            $query = new WP_Query($args_post);
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $product_variation_title = get_the_content();
                    if ($product_variation_title === $name) {
                        $product_id_typeroom = get_the_ID();
                        $content_hotel .= '
                            <div class="wrap_type-of-room">
                                <div class="box_type">
                                    <label class="font-w-bold">' . $name . ' :</label>
                                    <input type="hidden" readonly name="product_id_typeroom" value="' . $product_id_typeroom . '"/>
                                    <input type="hidden" readonly name="name_typeOfRoom" value="' . $name . '"/>
                                    <input type="hidden" readonly name="price_typeOfRoom" value="' . $price . '"/>
                                    
                                </div>
                                <div class="box_type">
                                    <p>Quantity</p>
                                    <input type="number" readonly name="quantity_typeOfRoom" value="' . $quantity . '"/>
                                </div>
                                <div class="box_type">
                                    <p>FooEvents Quantity</p>
                                    <input type="number" min="1" max="' . $quantity . '" name="fooEvent_typeOfRoom" value=""/>
                                </div>
                                <div class="box_type">
                                    <p>Maximum guest per room</p>
                                    <input type="number" name="description_typeOfRoom" value="' . $description . '"/>
                                </div>
                                <div class="box_type">
                                    <p>Price</p>
                                    <input type="number" name="price_typeOfRoom_new" value="'.$pricenew.'" />
                                </div>
                            </div>
                        ';
                        break;
                    }else{
                        $content_hotel .='';
                    }
                }
                wp_reset_postdata();
            }
            
            
        }
        $show_edit_button = true;
    }
    $title = get_post_field('post_title', $hotel_id);
    $content = '
    <div class="box-header">
        <div class="box-title">
            <p class="title-hotels">' . $title . '</p>
        </div>
        <div class="box-action">';
    if ($show_edit_button) {
        $content .= '<a class="button-primary button-edit">Edit</a>';
    }
    $content .= '
            <a class="button-remove ">Remove</a>
        </div>
    </div>
    <div class="box-content" data-id-hotel="' . $hotel_id . '">
    	' . $content_hotel . '
    </div>
    ';
    return $content;
}

add_action('wp_ajax_save_event_data', 'save_event_data');
add_action('wp_ajax_nopriv_save_event_data', 'save_event_data');

function save_event_data()
{
    $postData = isset($_POST['data']) ? $_POST['data'] : array();
    $post_id = $_POST['post_id'];
    // foreach ($postData as $room) {
    //     $hotel_id = $room['hotelId'];
    //     $true = "true";
    //     update_post_meta($hotel_id, 'trueHotel_type-of-rooms', $true);
    // }
    update_post_meta($post_id, 'hotel_type-of-rooms', $postData);
    
    wp_send_json_success();
}



?>