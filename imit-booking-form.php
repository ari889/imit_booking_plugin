<?php

/**
 * Plugin Name: Imit Booking Form
 * Plugin URI: https://smilesforseattle.com
 * Description: This is an appointment booking plugin with event management system
 * Version: 1.3.4
 * Author: Ideasy Corp.
 * Author URI: https://ideasymind.com
 * Licence: GPLv2 or latest
 * Text Domain: imit-booking-form
 * Domain Path: /Languages/
 */

define("IMIT_DB_VERSION", '1.3.4');
require_once 'class.imitBookingInfo.php';
require_once 'class.imitEventTimeInfo.php';
require_once 'class.imitQuestions.php';

/**
 * secure plugin
 */
if(!defined('ABSPATH')){
    exit;
}

/**
 * textdomain load
 */
function imit_load_textdomain(){
    load_plugin_textdomain('imit-booking-form', false, dirname(__FILE__),'/languages');
}

add_action('plugin_loaded', 'imit_load_textdomain');

/**
 * before active plugin make database
 */
function imit_init(){
    global $wpdb;
    $booking_table_name = $wpdb->prefix.'imit_appointment_bookings';
    $event_table_name = $wpdb->prefix.'imit_event_table';
    $question_table_name = $wpdb->prefix.'imit_booking_questions';

    require_once (ABSPATH."wp-admin/includes/upgrade.php");

    $sql[] = "CREATE TABLE {$booking_table_name} (
            id INT NOT NULL AUTO_INCREMENT,
            answer VARCHAR (1000) NOT NULL,
            first_name VARCHAR (250) NOT NULL,
            last_name VARCHAR (250) NOT NULL,
            email VARCHAR (250) NOT NULL,
            location VARCHAR (250) NOT NULL,
            event_date VARCHAR (250) NOT NULL,
            event_time VARCHAR (250) NOT NULL,
            client_cell VARCHAR (250) NOT NULL,
            referred_by VARCHAR (250),
            referral_name VARCHAR (250),
            status VARCHAR (250),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY(id)
    );";

    $sql[] = "CREATE TABLE {$event_table_name} (
        id INT NOT NULL AUTO_INCREMENT,
        event_time VARCHAR (250),
        status VARCHAR (250) DEFAULT '1',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY(id)
    )";

    $sql[] = "CREATE TABLE {$question_table_name} (
            id INT NOT NULL AUTO_INCREMENT,
            question VARCHAR (250) NOT NULL,
            answer VARCHAR (10000) NOT NULL,
            priority VARCHAR (10) NOT NULL,
            status VARCHAR (250) DEFAULT '1' NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY(id)
        )";
    dbDelta($sql);

    add_option("imit_db_version", IMIT_DB_VERSION);
    add_option("imit_booking_holiday", json_encode(['sun']));
    add_option("imit_booking_receiver_email", get_option('admin_email'));

    /**
     * add new column
     */
    if(get_option('imit_db_version') !== IMIT_DB_VERSION){
        $new_column[] = "ALTER TABLE {$booking_table_name}
                              DROP `braces`,
                              DROP `straighten`,
                              DROP `straightening`;";
        $new_column[] = "ALTER TABLE {$wpdb->prefix}imit_appointment_bookings
                    ADD answer VARCHAR(10000) NOT NULL;";
        dbDelta($new_column);
        update_option('imit_db_version', IMIT_DB_VERSION);
    }
}


register_activation_hook(__FILE__, 'imit_init');


/**
 * load all scripts
 */
function imit_all_scripts(){
    wp_enqueue_style('imit-bootstrap', PLUGINS_URL('css/bootstrap.min.css', __FILE__));
    wp_enqueue_style('imit-fontawesome', PLUGINS_URL('css/all.min.css', __FILE__));
    wp_enqueue_style('imit-theme', PLUGINS_URL('css/theme.css', __FILE__));
    wp_enqueue_style('imit-stylesheet', PLUGINS_URL('css/style.css', __FILE__));

    wp_enqueue_script('imit-jQuery', PLUGINS_URL('js/jquery-3.5.1.min.js', __FILE__));
    wp_enqueue_script('imit-bootstrap', PLUGINS_URL('js/bootstrap.min.js', __FILE__), ['imit-jQuery'], false, true);
    wp_enqueue_script('imit-pooper', PLUGINS_URL('js/popper.min.js', __FILE__), ['imit-jQuery'], false, true);
    wp_enqueue_script('imit-calender', PLUGINS_URL('js/calendar.min.js', __FILE__), ['imit-jQuery'], false, true);
    wp_enqueue_script('imit-main', PLUGINS_URL('js/main.js', __FILE__), ['imit-jQuery'], false, true);
    /**
     * for add booking
     */
    $nonce = wp_create_nonce('imit_ajax_insert_booking');
    wp_localize_script('imit-main', 'imitPluginData', [
       'ajax_url' => admin_url('admin-ajax.php'),
        'imit_nonce' => $nonce
    ]);
    /**
     * for fetch booking available time
     */
    $booking_nonce = wp_create_nonce('imit_booking_time_fetch');
    wp_localize_script('imit-main', 'fetchPluginDate', [
       'ajax_url' => admin_url('admin-ajax.php'),
        'imit_nonce' => $booking_nonce
    ]);

    /**
     * for check appointment status
     */
    $check_appointment = wp_create_nonce('imit_appointment_check');
    wp_localize_script('imit-main', 'fetchAppointmentStatus', [
       'ajax_url' => admin_url('admin-ajax.php'),
        'imit_nonce' => $check_appointment
    ]);

    /**
     * for number of added questions
     */
    global $wpdb;
    $table_name = $wpdb->prefix.'imit_booking_questions';
    $result = $wpdb->get_results("SELECT * FROM {$table_name}");
    wp_localize_script('imit-main', 'imitQuestions', [
       'number' => count($result)
    ]);
}

add_action('wp_enqueue_scripts', 'imit_all_scripts');

/**
 * after setup theme
 */

function imit_theme_setup_init(){

    /**
     * custom logo
     */

    add_theme_support('custom-logo');
    add_theme_support('menus');
    /**
     * make menu location
     */
    register_nav_menu('imit_menu', __('Booking menu', 'imit-booking-form'));
}

add_action('after_setup_theme', 'imit_theme_setup_init');

/**
 * admin script
 */
add_action('admin_enqueue_scripts', function($hook){
    if('toplevel_page_imitAppointmentBooking' == $hook){
        wp_enqueue_style('imitAppointmentBooking-style', PLUGINS_URL('style.css', __FILE__));
    }else if('appointment-booking_page_imitMenageEvent' == $hook){
        wp_enqueue_style('imitAppointmentBooking-style', PLUGINS_URL('style.css', __FILE__));
    }else if('appointment-booking_page_imitBookingCog' == $hook){
        wp_enqueue_style('imitAppointmentBooking-style', PLUGINS_URL('style.css', __FILE__));
    }else if('appointment-booking_page_imitQuestions' == $hook){
        wp_enqueue_style('imitAppointmentBooking-style', PLUGINS_URL('style.css', __FILE__));
    }
});


/**
 * frontend shortcode [imit-booking]
 */
add_shortcode('imit-booking', function(){
    ob_start();
    global $wpdb;

    $questions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}imit_booking_questions WHERE status = '1' ORDER BY priority ASC");
    ?>
    <!--header start-->
    <header class="header">
        <nav class="navbar navbar-expand-sm navbar-light bg-light">
            <div class="container-fluid">
                <!-- ====================back button====================== -->
                <button type="button" class="back">Back</button>

                <!--========================logo =========================-->
                <a class="navbar-brand mx-auto" href="<?php echo home_url(); ?>">
                    <img src="<?php
                    $custom_logo_id = get_theme_mod( 'custom_logo' );
                    $image = wp_get_attachment_image_src( $custom_logo_id  , 'full' );
                    echo $image[0];
                    ?>" alt="" style="width: 50px">
                </a>

                <!-- =================== navbar toggler =========================-->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!--===================Appointment find link====================-->
                <div class="collapse navbar-collapse" id="navbarTogglerDemo02" style="flex-grow: 0;">
                    <?php wp_nav_menu([
                            'theme_location' => 'imit_menu',
                            'menu_class' => 'navbar-nav ms-auto mb-2 p-0 mb-lg-0 text-center plugin-default',
                            'menu_id' => '',
                            'fallback_cb' => 'imit_default_menu'
                    ]); ?>
                </div>
            </div>
        </nav>
    </header>
    <!--header end-->

    <!--    =============== event management form =======================-->
    <section class="event-management">
        <div class="content-area">
            <form action="#" class="text-center content" id="booking_form" style="/*width: <?php //echo (count($questions) + 5)*100; ?>%*/">

                <?php
                $i = 1;
                foreach($questions as $imitqa){
                    $answers = json_decode($imitqa->answer);
                    ?>
                    <div class="custom-page" id="page-<?php echo $i; ?>" style="<?php if($i == 1){echo 'display: block;';} ?>">
                        <div class="page-content">
                            <h3 class="title"><?php echo $imitqa -> question; ?></h3>
                            <div id="booking-message<?php echo $i; ?>"></div>

                            <div class="">
                                <?php
                                $index = 1;
                                foreach($answers as $imita => $a){
                                    ?>
                                    <div class="label-button">
                                        <input type="radio" id="answer-<?php echo $i;echo $index; ?>" name="answer-<?php echo $i; ?>" value="<?php echo $a; ?>" class="d-none" <?php if($index == 1){echo 'checked';} ?> data-question_id="<?php echo $imitqa->id; ?>">
                                        <label for="answer-<?php echo $i;echo $index; ?>"><?php echo ucfirst($a); ?></label>
                                    </div>
                                    <?php
                                    $index++;
                                } ?>
                            </div>

                            <button type="button" class="next border-0" id="next" data-next_page="<?php echo $i + 1; ?>">Next</button>
                        </div>
                    </div>
                    <?php
                    $i++;
                }

                ?>

                <!--                ============================= page 4 =========================-->
                <div class="custom-page" id="page-<?php echo count($questions) + 1; ?>">
                    <div class="page-content">
                        <h3 class="title">Let’s get to know each other. What’s your name?</h3>
                        <div id="booking-message<?php echo count($questions) + 1; ?>"></div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="text-input mt-2">
                                    <label for="first_name">First name</label>
                                    <input type="text" name="first_name" id="first_name" placeholder="First name" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-input mt-2">
                                    <label for="last_name">Last name</label>
                                    <input type="text" name="last_name" id="last_name" placeholder="Last name" class="form-control outline">
                                </div>
                            </div>
                        </div>

                        <button type="button" class="next border-0" id="next" data-next_page="<?php echo count($questions) + 2; ?>">Next</button>
                    </div>
                </div>

                <!--                ========================= page 5 ========================-->
                <div class="custom-page" id="page-<?php echo count($questions) + 2; ?>">
                    <div class="page-content">
                        <h3 class="title">Please enter your email to see your booking.</h3>
                        <div id="booking-message<?php echo count($questions) + 2; ?>"></div>
                            <div class="email-input d-flex flex-sm-row flex-column justify-content-center align-items-center">
                                <label for="email">Email</label>
                                <input type="text" name="email" id="email" placeholder="Email" class="form-control mt-0">
                            </div>
                        <button type="button" class="next border-0" id="next" data-next_page="<?php echo count($questions) + 3; ?>">Next</button>
                    </div>
                </div>

                <!--                ====================== page 6 ====================-->
                <div class="custom-page" id="page-<?php echo count($questions) + 3; ?>">
                    <div class="page-content">
                        <h3 class="title">Choose your location.</h3>
                        <div id="booking-message<?php echo count($questions) + 3; ?>"></div>

                        <div class="d-flex flex-md-row flex-column justify-content-center align-items-center">
                            <div class="location">
                                <input type="radio" name="location" id="Burien" value="15580 3rd Ave SW Suite 201 Burien, WA 98166" class="d-none" checked>
                                <label for="Burien"><span>15580 3rd Ave SW Suite 201 Burien, WA 98166</span><img src="<?php echo plugins_url('images/bay-area.png', __FILE__); ?>" alt=""></label>
                            </div>
                        </div>

                        <button type="button" class="next border-0" id="next" data-next_page="<?php echo count($questions) + 4; ?>">Next</button>
                    </div>
                </div>

                <!--                ======================== page 7 =======================-->
                <div class="custom-page" id="page-<?php echo count($questions) + 4; ?>">
                    <div class="row w-100 m-0" style="min-height: calc(100vh - 143px);">
                        <div class="col-md-4 p-0">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2697.3577647435714!2d-122.3396471841319!3d47.46345730584812!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x5490449ebeaf7f9b%3A0x66af8b2595a4e940!2s15580%203rd%20Ave%20SW%2C%20Burien%2C%20WA%2098166%2C%20USA!5e0!3m2!1sen!2sbd!4v1614327266184!5m2!1sen!2sbd" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                        <div class="col-md-8 p-0">
                            <div id="booking-message<?php echo count($questions) + 4; ?>"></div>
                            <div class="calendar-wrapper"></div>
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status" id="available_time_loader" style="display: none;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="available-time mt-3" id="fetch_available_time">

                            </div>
                        </div>
                    </div>
                    <button type="button" class="next border-0" id="next" data-next_page="<?php echo count($questions) + 5; ?>">Next</button>
                </div>

                <!--                ============================ page 8 ====================-->
                <div class="custom-page" id="page-<?php echo count($questions) + 5; ?>">
                    <div class="page-content" style="max-width: 500px;">
                        <h3 class="title">One more step to finish your booking.</h3>
                        <div id="booking-message<?php echo count($questions) + 5; ?>"></div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="text-input mt-2 w-100">
                                    <label for="cell">Phone number</label>
                                    <input type="text" name="cell" id="cell" placeholder="Phone number" class="form-control">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="text-input mt-2 w-100">
                                    <label for="referral_name" class="w-100">How did you find us?</label>
                                    <select name="referral_name" id="referral_name" class="form-select">
                                        <option value="">--Select--</option>
                                        <option value="dentist">Dentist</option>
                                        <option value="employer">Employer</option>
                                        <option value="facebook">Facebook</option>
                                        <option value="friend/family">Friend/Family</option>
                                        <option value="google">Google</option>
                                        <option value="instagram">Instagram</option>
                                        <option value="mailer">Mailer</option>
                                        <option value="radio">Radio</option>
                                        <option value="wechat">weChat</option>
                                        <option value="yelp">Yelp</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-input mt-2" id="referral_input">
                            <label for="name-event">Enter name of the event</label>
                            <input type="text" name="referred_by" id="name-event" placeholder="Enter name of the event" class="form-control">
                        </div>

                        <button type="submit" class="next border-0" id="submit">Submit</button>
                    </div>
                </div>



            </form>
        </div>
    </section>
    <?php
    return ob_get_clean();
});

/**
 * booking appointment using ajax
 */
function imit_booking(){
    $action = 'imit_ajax_insert_booking';
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, $action)){
        global $wpdb;
        $table_name = $wpdb->prefix.'imit_appointment_bookings';
        $wpdb->insert($table_name, [
            'answer' => sanitize_text_field(json_encode($_POST['answer'])),
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name' => sanitize_text_field($_POST['last_name']),
            'email' => sanitize_text_field($_POST['email']),
            'event_date' => sanitize_text_field($_POST['event_date']),
            'location' => sanitize_text_field($_POST['location']),
            'event_time' => sanitize_text_field($_POST['event_time']),
            'referred_by' => sanitize_text_field($_POST['referred_by']),
            'referral_name' => sanitize_text_field($_POST['referral_name']),
            'client_cell' => sanitize_text_field($_POST['cell']),
            'status' => '0',
        ]);
        $user_message = "
            Event date: {$_POST['event_date']}
            Event time: {$_POST['event_time']}
            Location: {$_POST['location']}
        ";
        wp_mail($_POST['email'], 'An appintment has been booked using your email.', $user_message, 'Email from admin@gmail.com', '');

        $admin_message = "
            Please check your appointment.
            First name: {$_POST['first_name']}
            Last name: {$_POST['last_name']}
            Email : {$_POST['email']}
            Event date : {$_POST['event_date']}
            Event time: {$_POST['event_time']}
            Location: {$_POST['referred_by']}
            Referred by: {$_POST['referred_by']}
            Referral name: {$_POST['referral_name']}
            Phone number: {$_POST['cell']}
        ";
        $header = 'Email from '.$_POST['email'];

        wp_mail(get_option('imit_booking_receiver_email'), 'New appintment has been booked.', $admin_message, $header, '');
    }
}

add_action('wp_ajax_nopriv_imit_booking', 'imit_booking');
add_action('wp_ajax_imit_booking', 'imit_booking');

/**
 * fetch available time for days event
 */

function imit_available_time(){
    $action = 'imit_booking_time_fetch';
    $nonce = $_POST['nonce'];
    $date = sanitize_text_field($_POST['date']);
    $week = strtolower(explode(' ', $date)[0]);
    $holiday = json_decode(get_option('imit_booking_holiday'));

    if(!in_array($week, $holiday)){
        if(wp_verify_nonce($nonce, $action)){
            global $wpdb;
            $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}imit_event_table WHERE status = '1' AND event_time NOT IN (SELECT event_time FROM {$wpdb->prefix}imit_appointment_bookings WHERE event_date = '{$date}') AND (status = '1' OR status = '3')", OBJECT);

            if(count($result) > 0){
                foreach($result as $event_time){
                    ?>
                    <div class="radio-button">
                        <input type="radio" name="event" id="event<?php echo $event_time->id; ?>" class="d-none" value="<?php echo $event_time->event_time; ?>">
                        <label for="event<?php echo $event_time->id; ?>"><?php echo $event_time->event_time; ?></label>
                    </div>
                    <?php
                }
            }else{
                echo '<p class="text-danger text-center">No available booking</p>';
            }
        }
    }else{
        echo '<p class="text-danger text-center">Sorry, we are closed today.</p>';
    }
    die();
}
add_action('wp_ajax_imit_available_time', 'imit_available_time');
add_action('wp_ajax_nopriv_imit_available_time', 'imit_available_time');

/**
 * @param $item
 * search booking by email
 */
function imit_search_by_email($item){
    $email = strtolower($item['email']);
    $search_email = sanitize_text_field($_REQUEST['s']);
    if(strpos($email, $search_email) !== false){
        return true;
    }
    return false;
}

/**
 * show booking data in backend
 */
function imit_admin_page(){
    global $wpdb;
    if(isset($_GET['bid'])){
        if(!isset($_GET['n']) || !wp_verify_nonce($_GET['n'], 'imit_appointment_edit')){
            wp_die(__('Sorry you are not allowed to do this.', 'imit-booking-form'));
        }
    }
    echo '<h2>IMIT booking</h2>';
    _e('For booking form type this shortcode', 'imit-booking-form');
    echo ' <code>[imit-booking]</code> ';
    _e(' and for manage appointment type this ', 'imit-booking-form');
    echo '<code>[imit-manage-my-appointment]</code>';
    $id = $_GET['bid']??0;
    $id = sanitize_key($id);
    if($id){
        if(isset($_GET['action']) && $_GET['action'] == 'delete'){
            $wpdb->delete("{$wpdb->prefix}imit_appointment_bookings", ['id' => sanitize_key($_GET['bid'])]);
            $_GET['bid'] == null;
        }else{
            $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}imit_appointment_bookings WHERE id='{$id}'");
            ?>
            <div class="edit-form">
                <div class="edit-form-header">
                    <?php _e("View and edit", 'imit-booking-form'); ?> <strong><?php echo $result->first_name.' '.$result->last_name; ?></strong> <?php _e('booking info', 'imit-booking-form') ?>
                </div>
                <div class="edit-form-body">
                    <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                        <?php
                        wp_nonce_field('imit_edit_nonce', 'nonce');
                        ?>

                        <input type="hidden" name="action" value="imit_update_client_status">

                        <?php
                        $answers = json_decode($result->answer);
                        foreach($answers as $answer => $key){
                            $data = explode(',', $key);
                            $question_answer = $data[0];
                            $question_id = end($data);

                            $question_table = $wpdb->prefix.'imit_booking_questions';

                            $question_text = $wpdb->get_row("SELECT question FROM {$question_table} WHERE id = '{$question_id}'");

                            ?>
                            <p><strong>Question:</strong> <?php echo $question_text->question; ?></p>
                            <p><strong>Answer:</strong> <?php if($id)echo ucfirst($question_answer); ?></p>
                            <?php

                        }

                        ?>

<!--                        <p><strong>Question:</strong> Have you had braces or clear aligners in the past?</p>-->
<!--                        <p><strong>Answer:</strong> --><?php //if($id)echo ucfirst($result->braces); ?><!--</p>-->
<!---->
<!--                        <p><strong>Question:</strong> Why do you want to straighten your teeth?</p>-->
<!--                        <p><strong>Answer:</strong> --><?php //if($id)echo ucfirst($result->straighten); ?><!--</p>-->
<!---->
<!--                        <p><strong>Question:</strong> How long have you been thinking about straightening your teeth?</p>-->
<!--                        <p><strong>Answer:</strong> --><?php //if($id)echo ucfirst($result->straightening); ?><!--</p>-->

                        <p><strong>Email:</strong> <a href="mailto:<?php echo $result->email; ?>"><?php echo $result->email; ?></a></p>

                        <p><strong>Event location:</strong> <?php echo $result->location; ?></p>

                        <p><strong>Event date:</strong> <?php echo $result->event_date; ?></p>

                        <p><strong>Event time:</strong> <?php echo $result->event_time; ?></p>

                        <p><strong>Client cell:</strong> <a href="tel:<?php echo $result->client_cell; ?>"><?php echo $result->client_cell; ?></a></p>

                        <p><strong>Referred by:</strong> <?php echo $result->referred_by; ?></p>

                        <p><strong>Referral name:</strong> <?php echo $result->referral_name; ?></p>

                        <label for="status">Status</label>
                        <select name="status" id="status">
                            <option value="0" <?php if($result->status == '0')echo 'selected'; ?>>Pending</option>
                            <option value="1" <?php if($result->status == '1')echo 'selected'; ?>>Active</option>
                            <option value="2" <?php if($result->status == '2')echo 'selected'; ?>>Denied</option>
                            <option value="3" <?php if($result->status == '3')echo 'selected'; ?>>Completed</option>
                        </select>

                        <?php if($id){
                            echo "<input type='hidden' name='id' value='".$id."'>";
                            submit_button('Update record');
                        } ?>
                    </form>
                </div>
            </div>
            <?php
        }
    }
    $imit_bookings = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}imit_appointment_bookings ORDER BY id DESC", ARRAY_A);

    if(isset($_REQUEST['s']) && !empty($_REQUEST['s'])){
        $imit_bookings = array_filter($imit_bookings, 'imit_search_by_email');
    }

    $imitbu = new ImitAppointment($imit_bookings);
    $imitbu->prepare_items();
    ?>
    <div class="wrap">
        <h4 style="text-align:right;margin-bottom:10px;"><?php _e('Search by email', 'imit-booking-form') ?></h4>
        <form method="GET">
            <?php
            $imitbu->search_box('search', 'imit_search_id');
            $imitbu->display();
            ?>
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>">
        </form>
    </div>
    <?php
}

/**
 * update booking status
 */
add_action('admin_post_imit_update_client_status', function(){
   global $wpdb;
   $nonce = sanitize_text_field($_POST['nonce']);
   if(wp_verify_nonce($nonce, 'imit_edit_nonce')){
       $status = sanitize_text_field($_POST['status']);
       $id = sanitize_text_field($_POST['id']);

        if($id){
            $wpdb->update("{$wpdb->prefix}imit_appointment_bookings", [
               'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id]);
            $nonce = wp_create_nonce('imit_appointment_edit');
            wp_redirect('admin.php?page=imitAppointmentBooking&bid='.$id.'&n='.$nonce);
        }
   }
});

/**
 * for menage event page
 */
function manage_booking_event_page(){
    global $wpdb;
    $id = $_GET['eid']??0;
    $id = sanitize_key($id);
    if(isset($_GET['eid'])){
        if(!isset($_GET['n']) || !wp_verify_nonce($_GET['n'], 'imit_event_edit')){
            wp_die(__('Sorry you are not allowed to do this', 'imit-booking-form'));
        }
        if(isset($_GET['action']) && $_GET['action'] == 'delete'){
            $wpdb->delete("{$wpdb->prefix}imit_event_table", ['id' => sanitize_key($_GET['eid'])]);
            $_GET['eid'] = null;
        }
    }
    if($id){
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}imit_event_table WHERE id='{$id}'");
        $exp = explode(' - ', $result->event_time);
        $start = explode(' ', $exp[0]);

        $end = explode(' ', end($exp));

        $start_time = $start[0];
        $start_ampm = end($start);

        $end_time = $end[0];
        $end_ampm = end($end);
    }
    ?>
    <h2><?php _e('Add event', 'imit-booking-form'); ?></h2>
    <?php
    _e('For booking form type this shortcode', 'imit-booking-form');
    echo ' <code>[imit-booking]</code> ';
    _e(' and for manage appointment type this ', 'imit-booking-form');
    echo '<code>[imit-manage-my-appointment]</code>';
    ?>
    <div class="edit-form">
        <div class="edit-form-header">

            <?php
            if($id && !isset($_GET['action'])){
                _e('Edit event', 'imit-booking-form');
                echo ' <strong>'.$result->event_time.'</strong>';
            }else{
                _e("Add event time", 'imit-booking-form');
            }
            ?>
        </div>
        <div class="edit-form-body">
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <?php
                wp_nonce_field('imit_event_add', 'nonce');
                ?>
                <input type="hidden" name="action" value="imit_add_event_record">

                <label for="">Event start</label>
                <input type="text" name="event_start" placeholder="Eg: 8:00" value="<?php if($id && !isset($_GET['action']))echo $start_time; ?>" <?php if($id && !isset($_GET['action'])){echo 'disabled';} ?>>
                <select name="start_ampm" id="" style="margin-bottom: 20px;" <?php if($id && !isset($_GET['action'])){echo 'disabled';} ?>>
                    <option value="AM" <?php if($id && $start_ampm == 'AM' && !isset($_GET['action'])){echo 'selected';} ?>>AM</option>
                    <option value="PM" <?php if($id && $start_ampm == 'PM' && !isset($_GET['action'])){echo 'selected';} ?>>PM</option>
                </select>

                <label for="">Event end</label>
                <input type="text" name="event_end" placeholder="Eg: 12:00" value="<?php if($id && !isset($_GET['action']))echo $end_time; ?>" <?php if($id && !isset($_GET['action'])){echo 'disabled';} ?>>
                <select name="end_ampm" id="" <?php if($id && !isset($_GET['action'])){echo 'disabled';} ?>>
                    <option value="AM" <?php if($id && $end_ampm == 'AM' && !isset($_GET['action'])){echo 'selected';} ?>>AM</option>
                    <option value="PM" <?php if($id && $end_ampm == 'PM' && !isset($_GET['action'])){echo 'selected';} ?>>PM</option>
                </select>

                <?php if($id && !isset($_GET['action'])){
                    ?>
                    <label for="status" style="margin-top: 20px;">Status</label>
                    <select name="status" id="status">
                        <option value="0" <?php if($result->status == '0')echo 'selected'; ?>>Denied</option>
                        <option value="1" <?php if($result->status == '1')echo 'selected'; ?>>Published</option>
                    </select>
                    <?php
                } ?>

                <?php
                if($id && !isset($_GET['action'])){
                    echo "<input type='hidden' name='id' value='".$id."' />";
                    submit_button('Update record');
                }else{
                    submit_button('Add event time');
                }
                ?>
            </form>
        </div>
    </div>
    <?php

    $imit_event_time = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}imit_event_table ORDER BY ID DESC", ARRAY_A);
    $imitet = new ImitEventTime($imit_event_time);
    $imitet->prepare_items();
    $imitet->display();
}

/**
 * add event
 */
add_action('admin_post_imit_add_event_record', function(){
    global $wpdb;
    $nonce = sanitize_text_field($_POST['nonce']);
    if(wp_verify_nonce($nonce,'imit_event_add')){
        $event_start = sanitize_text_field($_POST['event_start']);
        $event_start_ampm = sanitize_text_field($_POST['start_ampm']);
        $event_end = sanitize_text_field($_POST['event_end']);
        $event_end_ampm = sanitize_text_field($_POST['end_ampm']);
        $id = sanitize_text_field($_POST['id']);
        if($id){
            $wpdb->update("{$wpdb->prefix}imit_event_table", [
                'status' => sanitize_text_field($_POST['status']),
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id]);
            $nonce = wp_create_nonce('imit_event_edit');
            wp_redirect('admin.php?page=imitMenageEvent&eid='.$id.'&n='.$nonce);
        }else{
            $wpdb->insert("{$wpdb->prefix}imit_event_table", [
                'event_time' => $event_start.' '.$event_start_ampm.' - '.$event_end.' '.$event_end_ampm,
            ]);
            wp_redirect('admin.php?page=imitMenageEvent');
        }
    }
});

/**
 * booking configuration
 */
function manage_booking_cog(){
    $holiday = json_decode(get_option('imit_booking_holiday'));
    ?>
<h2><?php _e('Settings', 'imit-booking-form');?></h2>
    <?php
    _e('For booking form type this shortcode', 'imit-booking-form');
    echo ' <code>[imit-booking]</code> ';
    _e(' and for manage appointment type this ', 'imit-booking-form');
    echo '<code>[imit-manage-my-appointment]</code>';
    ?>
    <div class="edit-form">
        <div class="edit-form-header">

            <?php

                _e('Manage settings', 'imit-booking-form');

            ?>
        </div>
        <div class="edit-form-body">
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <?php if(isset($_GET['msg'])): ?>
                    <div class="notice notice-error is-dismissible" style="padding: 10px 20px; margin: 0; margin-bottom: 20px;"><?php echo $_GET['msg']; ?></div>
                <?php endif; ?>
                <?php
                wp_nonce_field('imit_settings_update', 'nonce');
                ?>
                <input type="hidden" name="action" value="imit_settings_update">

                <label for=holiday"">Select your weekly closed days.</label>
                <select name="holiday[]" id="holiday" size="7" multiple>
                    <option value="sun" <?php if(in_array('sun', $holiday)){echo 'selected';} ?>>Sunday</option>
                    <option value="mon" <?php if(in_array('mon', $holiday)){echo 'selected';} ?>>Monday</option>
                    <option value="tue" <?php if(in_array('tue', $holiday)){echo 'selected';} ?>>Tuesday</option>
                    <option value="wed" <?php if(in_array('wed', $holiday)){echo 'selected';} ?>>Wednesday</option>
                    <option value="thu" <?php if(in_array('thu', $holiday)){echo 'selected';} ?>>Thrusday</option>
                    <option value="fri" <?php if(in_array('fri', $holiday)){echo 'selected';} ?>>Friday</option>
                    <option value="sat" <?php if(in_array('sat', $holiday)){echo 'selected';} ?>>Saturday</option>
                </select>
                <p>Press <code>CTRL</code> and select multiple closed days.</p>

                <label for="receiver_email">Receiver email</label>
                <input type="text" name="receiver_email" id="receiver_email" placeholder="Eg: admin@gmail.com" value="<?php echo get_option('imit_booking_receiver_email'); ?>" style="margin-bottom: 0;">

                <p>Please enter a valid email</p>

                <?php
                    submit_button('Update settings');
                ?>
            </form>
        </div>
    </div>
    <?php
}


/**
 * update holiday setting
 */
add_action('admin_post_imit_settings_update', function(){
    $nonce = sanitize_text_field($_POST['nonce']);
    if(wp_verify_nonce($nonce,'imit_settings_update')){
        $holiday = $_POST['holiday'];
        $holiday_data = json_encode($holiday);
        $receiver_email = sanitize_text_field($_POST['receiver_email']);
        if(!filter_var($receiver_email, FILTER_VALIDATE_EMAIL)){
            wp_redirect('admin.php?page=imitBookingCog&&msg=Wrong email');
        }else if(empty($receiver_email)){
            wp_redirect('admin.php?page=imitBookingCog&&msg=Please enter an email.');
        }else{
            update_option('imit_booking_receiver_email', $receiver_email);
            update_option('imit_booking_holiday', $holiday_data);
            wp_redirect('admin.php?page=imitBookingCog');
        }
    }
});

/**
 * for manage all questions
 */
function imit_manage_all_questions(){
    global $wpdb;

    $table_name = $wpdb->prefix.'imit_booking_questions';
    $qid = $_GET['qid']??0;
    $qid = sanitize_key($qid);
    if(isset($_GET['qid'])){
        if(!isset($_GET['n']) || !wp_verify_nonce($_GET['n'], 'imit_question_add')){
            wp_die(__('Sorry you are not allowed to do this', 'imit-booking-form'));
        }
    }
    if($qid){
        if(isset($_GET['action']) && $_GET['action'] == 'delete'){
            $wpdb->delete("{$table_name}", ['id' => sanitize_key($_GET['qid'])]);
            $_GET['qid'] = null;
        }else{
            $result = $wpdb->get_row("SELECT * FROM {$table_name} WHERE id='{$qid}'");
            $imita = json_decode($result->answer);
            $exa = implode($imita, ',');
        }
    }

    echo '<h2>Manage all questions</h2>';
    _e('For booking form type this shortcode', 'imit-booking-form');
    echo ' <code>[imit-booking]</code> ';
    _e(' and for manage appointment type this ', 'imit-booking-form');
    echo '<code>[imit-manage-my-appointment]</code>';
    ?>
    <div class="edit-form">
        <div class="edit-form-header">

            <?php

            _e('Add question', 'imit-booking-form');

            ?>
        </div>
        <div class="edit-form-body">
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <?php if(isset($_GET['msg'])): ?>
                    <div class="notice notice-error is-dismissible" style="padding: 10px 20px; margin: 0; margin-bottom: 20px;"><?php echo $_GET['msg']; ?></div>
                <?php endif; ?>
                <?php
                wp_nonce_field('imit_question_add', 'nonce');
                ?>
                <input type="hidden" name="action" value="imit_add_question">

                <label for="imit_question">Question</label>
                <input type="text" name="imit_question" id="imit_question" placeholder="Eg: Have you had braces or clear aligners in the past?" value="<?php if($qid){echo $result->question;} ?>">

                <label for="imit_answers">Answers</label>
                <input type="text" name="imit_answers" id="imit_answers" placeholder="Eg: yes, no" value="<?php if($qid){echo $exa;} ?>">
                <p>Answers must be seperate with coma ( , )</p>

                <label for="priority">Priority</label>
                <input type="text" name="imit_question_priority" id="priority" placeholder="Eg: 1" value="<?php if($qid){echo $result->priority;} ?>">
                <p>Priority must be an integer number.</p>

                <?php if($qid && (!isset($_GET['action']) && $_GET['action'] !== 'delete')){
                    ?>
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="1" <?php if($result->status == '1'){echo 'selected';} ?>>Published</option>
                        <option value="0" <?php if($result->status == '0'){echo 'selected';} ?>>Denied</option>
                    </select>
                    <?php
                }
                if($qid && (!isset($_GET['action']) && $_GET['action'] !== 'delete')){
                    echo '<input type="hidden" name="id" value="'.$result->id.'" />';
                    submit_button('Update question');
                }else{
                    submit_button('Add question');
                }
                ?>
            </form>
        </div>
    </div>
    <?php

    $imit_questions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}imit_booking_questions ORDER BY priority ASC", ARRAY_A);
    $imitqa = new ImitQuestions($imit_questions);
    $imitqa->prepare_items();
    $imitqa->display();
}

/**
 * add admin question
 */
add_action('admin_post_imit_add_question', function(){
   global $wpdb;
   $table_name = $wpdb->prefix.'imit_booking_questions';
   $nonce = sanitize_text_field($_POST['nonce']);
   if(wp_verify_nonce($nonce, 'imit_question_add')){
       $question = sanitize_text_field($_POST['imit_question']);
       $answers = sanitize_text_field($_POST['imit_answers']);
       $exp_ans = json_encode(explode(',', $answers));
       $priority = $_POST['imit_question_priority'];
       if(empty($question) || empty($answers) || empty($priority)){
           wp_redirect('admin.php?page=imitQuestions&msg=All fields are required.');
       }else if(preg_match('!^[1-9][0-9]*$!',$priority) == false){
           wp_redirect('admin.php?page=imitQuestions&msg=Priority must be an unsigned integer number');
       }else{
           if($_POST['id']){
               $status = $_POST['status'];
               $wpdb->update($table_name, [
                   'question' => $question,
                   'answer' => $exp_ans,
                   'priority' => $priority,
                   'status' => $status
               ], ['id' => $_POST['id']]);
               wp_redirect('admin.php?page=imitQuestions&qid='.$_POST['id'].'&n='.$nonce);
           }else{
                if(count($wpdb->get_results("SELECT * FROM {$table_name} WHERE priority = '{$priority}'")) > 0){
                   wp_redirect('admin.php?page=imitQuestions&&msg=Try different priority.');
                }else{
                    $wpdb->insert($table_name, [
                        'question' => $question,
                        'answer' => $exp_ans,
                        'priority' => $priority
                    ]);
                    wp_redirect('admin.php?page=imitQuestions');
                }
           }
       }
   }
});


/**
 * create admin menu
 */
add_action('admin_menu', function(){
    /**
     * main menu for appintment booking
     */
    add_menu_page('Appointment booking', 'Appointment booking', 'manage_options', 'imitAppointmentBooking', 'imit_admin_page', 'dashicons-calendar-alt');
    /**
     * for event manegement
     */
    add_submenu_page('imitAppointmentBooking', 'Event managememt', 'Event management', 'manage_options', 'imitMenageEvent', 'manage_booking_event_page');
    /**
     * for plugin settings
     */
    add_submenu_page('imitAppointmentBooking', 'Booking option', 'Booking option', 'manage_options', 'imitBookingCog', 'manage_booking_cog');

    /**
     * for add question
     */
    add_submenu_page('imitAppointmentBooking', 'Add question', 'Add question', 'manage_options', 'imitQuestions', 'imit_manage_all_questions');
});

/**
 * imit default menu
 */
function imit_default_menu(){
    echo 'Please add a menu';
}


/**
 * see user appointment status from backend
 */
add_shortcode('imit-manage-my-appointment', function(){
    ob_start();
    ?>
    <!--header start-->
    <header class="header">
        <nav class="navbar navbar-expand-sm navbar-light bg-light">
            <div class="container-fluid">
                <!-- ====================back button====================== -->
<!--                <button type="button" class="back">Back</button>-->

                <!--========================logo =========================-->
                <a class="navbar-brand" href="<?php echo home_url(); ?>">
                    <img src="<?php
                    $custom_logo_id = get_theme_mod( 'custom_logo' );
                    $image = wp_get_attachment_image_src( $custom_logo_id  , 'full' );
                    echo $image[0];
                    ?>" alt="" style="width: 50px">
                </a>

                <!-- =================== navbar toggler =========================-->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!--===================Appointment find link====================-->
                <div class="collapse navbar-collapse" id="navbarTogglerDemo02" style="flex-grow: 0;">
                    <?php wp_nav_menu([
                        'theme_location' => 'imit_menu',
                        'menu_class' => 'navbar-nav ms-auto mb-2 p-0 mb-lg-0 text-center plugin-default',
                        'menu_id' => '',
                        'fallback_cb' => 'imit_default_menu'
                    ]); ?>
                </div>
            </div>
        </nav>
    </header>
    <!--header end-->

    <!--    =============== event management form =======================-->
    <section class="event-management">
        <div class="content-area">
            <form action="#" class="text-center d-flex flex-row justify-content-start align-items-center" id="appointment_check">


                <!--                ===================================== page 1 ========================-->
                <div class="page mx-auto w-100">
                    <h3 class="title mt-5">Check your appointment status</h3>
                    <div id="booking-message1"></div>

                    <div class="d-flex flex-md-row flex-column justify-content-center align-items-center my-4">
                        <div class="input-group" style="max-width: 400px;">
                            <input type="text" class="form-control" name="email" placeholder="Enter email">
                            <input type="submit" name="submit" value="Check" class="btn primary-bg">
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Booking date</th>
                                <th scope="col">Booking time</th>
                                <th scope="col">Location</th>
                                <th scope="col">Status</th>
                            </tr>
                            </thead>
                            <tbody id="booking_status">

                            </tbody>
                        </table>
                    </div>
                </div>

            </form>
            <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status" id="booking_status_spinner" style="display: none;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
});

function imit_booking_status(){
    $action = 'imit_appointment_check';
    $nonce = $_POST['nonce'];
    if(wp_verify_nonce($nonce, $action)){
        global $wpdb;
        $table_name = $wpdb->prefix.'imit_appointment_bookings';
        $email = sanitize_text_field($_POST['email']);
        $result = $wpdb->get_results("SELECT * FROM {$table_name} WHERE email = '{$email}'");

        foreach($result as $data){
            ?>
                <tr>
                    <td><?php echo $data->event_date; ?></td>
                    <td><?php echo $data->event_time; ?></td>
                    <td><?php echo $data->location; ?></td>
                    <td><?php
                        if($data->status == '0'){
                            echo '<div class="badge bg-info">Pending</div>';
                        }elseif ($data->status == '1'){
                            echo '<div class="badge bg-primary">Active</div>';
                        }elseif($data->status == '2'){
                            echo '<div class="badge bg-danger">Denied</div>';
                        }else{
                            echo '<div class="badge bg-success">Completed</div>';
                        }
                        ?></td>
                </tr>
            <?php
        }
    }
    die();
}

add_action('wp_ajax_nopriv_imit_check_booking_status', 'imit_booking_status');
add_action('wp_ajax_imit_check_booking_status', 'imit_booking_status');
