<?php
/**
 * Kadence functions and definitions
 *
 * This file must be parseable by PHP 5.2.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package kadence
 */

define( 'KADENCE_VERSION', '1.1.18' );
define( 'KADENCE_MINIMUM_WP_VERSION', '5.4' );
define( 'KADENCE_MINIMUM_PHP_VERSION', '7.0' );

// Bail if requirements are not met.
if ( version_compare( $GLOBALS['wp_version'], KADENCE_MINIMUM_WP_VERSION, '<' ) || version_compare( phpversion(), KADENCE_MINIMUM_PHP_VERSION, '<' ) ) {
    require get_template_directory() . '/inc/back-compat.php';
    return;
}
// Include WordPress shims.
require get_template_directory() . '/inc/wordpress-shims.php';

// Load the `kadence()` entry point function.
require get_template_directory() . '/inc/class-theme.php';

// Load the `kadence()` entry point function.
require get_template_directory() . '/inc/functions.php';

// Initialize the theme.
call_user_func( 'Kadence\kadence' );

//*************** Início da customização ***************//

//Força o username a ser o email cadastrado
add_filter( 'woocommerce_new_customer_data', function( $data ) {
    $data['user_login'] = $data['user_email'];
    return $data;
}
);


//adicionando campos no formulário de cadastro
function wooc_extra_register_fields() {?>
 <p class="form-row form-row-first">
     <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
     <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
 </p>
 <p class="form-row form-row-last">
     <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
     <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
 </p>
 <div class="clear"></div>
 <?php
}
add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );


 //adicioando validação dos campos adicionais
 /**
* register fields Validating.
*/
function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
  if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
   $validation_errors->add( 'billing_first_name_error', __( 'Nome é um campo obrigatório.', 'woocommerce' ) );
}
if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
   $validation_errors->add( 'billing_last_name_error', __( 'Sobrenome é um campo obrigatório.', 'woocommerce' ) );
}
return $validation_errors;
}
add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );


//Salvando no banco de dados os campos adiionais
/**
* Below code save extra fields.
*/
function wooc_save_extra_register_fields( $customer_id ) {

  if ( isset( $_POST['billing_first_name'] ) ) {
             //First name field which is by default
   update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
             // First name field which is used in WooCommerce
   update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
}
if ( isset( $_POST['billing_last_name'] ) ) {
             // Last name field which is by default
   update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
             // Last name field which is used in WooCommerce
   update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
}

}
add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields');



 /**
 * @snippet       Change "Place Order" Button text @ WooCommerce Checkout
 * @sourcecode    https://rudrastyh.com/?p=8327#woocommerce_order_button_text
 * @author        Misha Rudrastyh
 */
 add_filter( 'woocommerce_order_button_text', 'misha_custom_button_text' );
 
 function misha_custom_button_text( $button_text ) {
   return 'Finalizar pagamento'; // new text is here 
}


/* ----------inicio nova aba my-account -------------  */

// Add Woocommerce My Account tab for Support
// 1. Register new customer-support endpoint (URL) for My Account page
add_action( 'init', 'add_support_endpoint' );
function add_support_endpoint() {
    add_rewrite_endpoint( 'customer-support', EP_ROOT | EP_PAGES );
}

// 2. Add new query var
add_filter( 'query_vars', 'support_query_vars', 0 );  
function support_query_vars( $vars ) {
    $vars[] = 'customer-support';
    return $vars;
}

// 3. Insert the new endpoint into the My Account menu
add_filter( 'woocommerce_account_menu_items', 'add_new_support_tab' );
function add_new_support_tab( $items ) {
    $items['customer-support'] = 'Support';
    return $items;
}

// 4. Add content to the added tab
add_action( 'woocommerce_account_customer-support_endpoint', 'support_tab_content' ); // Note: add_action must follow 'woocommerce_account_{your-endpoint-slug}_endpoint' format

function support_tab_content() {
 echo '<h4><strong>Support</strong></h4>
 <p>Fermentum tristique non imperdiet vulputate ridiculus platea arcu suspendisse donec</p>';
   echo do_shortcode( '[fluentform id="1"]' ); // Here goes your shortcode if needed
}

// 5. Go to Settings >> Permalinks and re-save permalinks. Otherwise you will end up with 404 Page not found error

// 1. Register new purchased-courses endpoint (URL) for My Account page
add_action( 'init', 'add_lms_endpoint' );
function add_lms_endpoint() {
    add_rewrite_endpoint( 'purchased-courses', EP_ROOT | EP_PAGES );
}

// 2. Add new query var
add_filter( 'query_vars', 'courses_query_vars', 0 );  
function courses_query_vars( $vars ) {
    $vars[] = 'purchased-courses';
    return $vars;
}

// 3. Insert the new endpoint into the My Account menu
add_filter( 'woocommerce_account_menu_items', 'add_new_courses_tab', 40 );
function add_new_courses_tab( $items ) {
    $items['purchased-courses'] = 'Your courses';
    return $items;
}

// 4. Add content to the added tab
add_action( 'woocommerce_account_purchased-courses_endpoint', 'course_tab_content' ); // Note: add_action must follow 'woocommerce_account_{your-endpoint-slug}_endpoint' format

function course_tab_content() {
 echo '<h4><strong>Meus cursos</strong></h4>
 <p>Acesse os cursos comprados.</p>';

 $current_user_id = get_current_user_id();

 $the_user = get_user_by( 'id', $current_user_id);

 include('../../../loja/wp-config.php');

   /*
   //Dados do banco de dados do WP
   echo "<br />" . DB_USER;
   echo "<br />" . DB_NAME;
   echo "<br />" . DB_HOST;
   echo "<br />" . DB_PASSWORD;
   */

   //Dados do banco de dados do Moodle
   $host = "192.168.0.15:3306";
   $dbuser = "directweb-moodle401";
   $dbpass = 'M4R!4DBmoodle401@2022';
   $dbname = "directweb-moodle401";

   //Endereço do Moodle
   $url_moodle = 'https://directweb.eduead.com.br/moodle401/';

   //Criando link de acesso aos cursos
   $url_course = $url_moodle . 'course/view.php?id='; 

   $link = mysqli_connect($host, $dbuser, $dbpass, $dbname);

   if (!$link) {
    die("A conexão como o banco de dados falhou " . mysqli_connect_error());
}

$query = "SELECT "
. "u.id AS user_id, "
. "concat(u.firstname, ' ' , u.lastname) AS name, "
. "u.username, "
. "u.email, "
. "c.id AS course_id, "
. "c.fullname AS course_name " 
. "FROM mdl_role_assignments rs "
. "INNER JOIN mdl_user u ON u.id=rs.userid "
. "INNER JOIN mdl_context e ON rs.contextid=e.id "
. "INNER JOIN mdl_course c ON c.id = e.instanceid "
. "WHERE e.contextlevel = 50 "
. "AND u.deleted <> 1 "
.  "AND u.username = '{$the_user->user_login}' "
. "AND u.email = '{$the_user->user_email}'";


//exibindo os cursos inscritos

$dados = mysqli_query($link, $query);

$total = mysqli_num_rows($dados);

if ($total > 0) { 
    echo "<table>"
    . "<tr>"
    .  "<td>Curso</td>"
    . "</tr>";  
    while($exibe = mysqli_fetch_array($dados)){
        echo "<tr>";       
        echo "<td><a href=".$url_course.$exibe['course_id'].'"'.' target="_blank">'.$exibe['course_name']."</a>"."</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<h4>Nenhum curso adquirido.</h4>";
}

mysqli_close($link);

}

/* ----------Fim nova aba my-account -------------  */


//Alterando mensagem de página de pagamento
add_filter( 'woocommerce_checkout_login_message', 'mycheckoutmessage_return_customer_message' ); 
function mycheckoutmessage_return_customer_message() {
    return '<span class="login-message">Para efetuar o pagãmento é necessário cadastrar-se, <a href="https://directweb.eduead.com.br/loja/my-account/">clique aqui para se cadastrar</a>, mas se já é cliente, </span>';
}

//Alterando mensagem de página de carrinho - carrinho vazio
add_filter( 'wc_empty_cart_message', 'mycartmessage_return_customer_message' );
function mycartmessage_return_customer_message() {
    return '<span class="login-message">Seu carrinho está vazio no momento.</span>';
}

/****************************************************************************/

//Add formato no campo tel, CEP da página pagamento
add_filter( 'woocommerce_checkout_fields', 'checkout_fields_format' );
function checkout_fields_format( $fields ) {

    //Placeholder do campo telefone e CEP
    $fields['billing']['billing_phone']['placeholder'] = '(00) 000000000';
    $fields['billing']['billing_postcode']['placeholder'] = '00000-000';

    //alidação de tamanho maximo do campo telefone e CEP
    $fields['billing']['billing_phone']['maxlength'] = 14;
    $fields['billing_postcode']['maxlength'] = 9;

    return $fields;
}

add_action( 'woocommerce_after_checkout_form', 'checkout_phone_mask' );
function checkout_phone_mask() {
   wc_enqueue_js( "
      $('#billing_phone')
      .keydown(function(e) {
         var key = e.which || e.charCode || e.keyCode || 0;
         var phone = $(this);         
         if (key !== 8 && key !== 9) {
            /*-----*/
            if (phone.val().length === 0) {
                phone.val(phone.val() + '('); // add dash before char #1
            }
            /*-----*/
            if (phone.val().length === 3) {
                phone.val(phone.val() + ') '); // add dash after char #3
            }            
        }
        return (key == 8 ||
        key == 9 ||
        key == 46 ||
        (key >= 48 && key <= 57) ||
        (key >= 96 && key <= 105));
        });
        " );
}

/****************************************************************************/

add_action( 'woocommerce_after_checkout_form', 'my_wc_custom_postcode_mask' );
function my_wc_custom_postcode_mask() {
    wc_enqueue_js( "
      $('#billing_postcode')
      .keydown(function(e) {
         var key = e.which || e.charCode || e.keyCode || 0;
         var postcode = $(this);         
         if (key !== 8 && key !== 9) {
             if (postcode.val().length === 5) {
                postcode.val(postcode.val() + '-'); 
            }
        }
        });
        " );
}

/***************************************************************************/


//Definindo o primeiro nome como display_name
add_action( 'profile_update', 'set_display_name', 10 );

function set_display_name( $user_id ) {

    $data = get_userdata( $user_id );

    if($data->first_name) {

        remove_action( 'profile_update', 'set_display_name', 10 ); //profile_update is called by wp_update_user, so we need to remove it before call, to avoid infinite recursion
        wp_update_user( 
            array (
                'ID' => $user_id, 
                'display_name' => "$data->first_name"
            ) 
        );
        add_action( 'profile_update', 'set_display_name', 10 );
    }
}

/***************************************************************************/

//Criando endpoint (URL)
add_action( 'init', 'add_test_endpoint' );
function add_test_endpoint() {
    add_rewrite_endpoint( 'test', EP_ROOT | EP_PAGES );
}

//Adicionando nova query var
add_filter( 'query_vars', 'test_query_vars', 0 );  
function test_query_vars( $vars ) {
    $vars[] = 'test';
    return $vars;
}

//inserirndo o novo endpoint no menu my-account
add_filter( 'woocommerce_account_menu_items', 'add_new_test_tab', 40 );
function add_new_test_tab( $items ) {
    $items['test'] = 'test';
    return $items;
}

//adicionando content para a nova tab adicionada
add_action( 'woocommerce_account_test_endpoint', 'test_tab_content' );

function test_tab_content() {
 echo '<h4><strong>Tab de testes</strong></h4>
 <p>Realizando testes</p>';

 $current_user_id = get_current_user_id();

 $user_data = get_user_by( 'id', $current_user_id);

 echo $user_data->user_login . "<br />";
 echo $user_data->user_email . "<br />";
 echo $user_data->user_pass . "<br />";
 echo $current_user_id . "<br />";
 echo $user_data->first_name . "<br />";
 echo $user_data->last_name . "<br />";
 echo $user_data->user_registered . "<br />";
 echo $user_data->user_activation_key . "<br />";

 echo "<br /><br />";

 echo "INSERT INTO dw_user VALUES ("
    . "NULL, "
    . "'{$user_data->user_login}', "
    . "'{$user_data->user_pass}', "
    . "'{$user_data->user_email}', "
    . "'$user_data->user_registered')";

    echo "<br /><br />"; 

    include('../../../store/wp-config.php');

   //Dados do banco de dados do WP
    echo "<br />" . DB_USER;
    echo "<br />" . DB_NAME;
    echo "<br />" . DB_HOST;
    echo "<br />" . DB_PASSWORD;

    echo "<br />";

    $city = get_user_meta( $current_user_id, 'billing_city', true );

    echo $city;

    echo "<br />";

    $data_city = get_user_meta ($current_user_id);
    echo $data_city['billing_city'][0];

} 

/***************************************************************************/

//inserindo os dados do usuário na tabela externa ao se cadastrar
add_action( 'user_register', 'insert_external_table', 10, 1 );

function insert_external_table($user_id) {

    $data = get_userdata($user_id);

    include('../../../store/wp-config.php');
    
    //Conexão com o BD do WP
    $link_wp = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $wp_user_id = $data->ID;
    $username = $data->user_login;
    $firstname = $_POST['billing_first_name'];
    $lastname = $_POST['billing_last_name'];
    $password = $data->user_pass;
    $email = $data->user_email;
    $city_state = '';
    $country = '';
    $registered = $data->user_registered;
    
    $query = "INSERT INTO dw_user VALUES (NULL, {$wp_user_id}, '{$username}', '{$firstname}', '{$lastname}', '{$password}', '{$email}', '{$city_state}', '{$country}', '{$registered}')";

    mysqli_query($link_wp, $query);
    
    mysqli_close($link_wp);   

}

/***************************************************************************/

//alterando tabela externa inserindo dados do estado, cidade e paìs
add_action( 'woocommerce_thankyou', 'insert_external_table_wc_data' );

function insert_external_table_wc_data($order_id) {

 $current_user_id = get_current_user_id();
 $city = get_user_meta( $current_user_id, 'billing_city', true );
 $state = get_user_meta( $current_user_id, 'billing_state', true );
 $country = get_user_meta( $current_user_id, 'billing_country', true );
 $city_state = $city . " / " . $state;   

 include('../../../store/wp-config.php');

    //Conexão com o BD do WP
 $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

 $update_data = "UPDATE dw_user SET city_state = '{$city_state}', country = '$country' WHERE wp_user_id = $current_user_id";

 mysqli_query($link, $update_data);

 mysqli_close($link);

}

/***************************************************************************/

//Alterando nomes e email do usuário cadastrado
add_action( 'woocommerce_save_account_details', 'update_wp_user_data');
function update_wp_user_data($user_id) {

    $data = get_userdata($user_id);

    $firstname = $data->first_name;
    $lastname = $data->last_name;
    $email = $data->user_email;
    
    include('../../../store/wp-config.php');

    //Conexão com o BD do WP
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $query = "UPDATE dw_user SET username = '{$email}', first_name = '{$firstname}', last_name = '{$lastname}',
    email = '{$email}' WHERE wp_user_id = {$user_id}";

    mysqli_query($link, $query);

    mysqli_close($link);

}

/***************************************************************************/

//Alterando a senha do usuário na tabela externa
add_action( 'after_password_reset', 'update_password_ext_table');
function update_password_ext_table() {

    $current_user_id = get_current_user_id();
    $user_obj = get_user_by( 'id', $current_user_id);

    $user_id = $user_obj->ID;
    $password = $user_obj->user_pass;

    include('../../../store/wp-config.php');

    //Conexão com o BD do WP
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $query = "UPDATE dw_user SET password = '{$password}' WHERE wp_user_id = {$user_id}";

    mysqli_query($link, $query);

    mysqli_close($link);

    return;
}

add_action( 'woocommerce_edit_account_form', 'update_password_ext_table_action' );
function update_password_ext_table_action() {
    update_password_ext_table();
}


/***************************************************************************/

//Excluindo usuário da tabela externa
add_action( 'delete_user', 'delete_user_ext_table' );
function delete_user_ext_table( $user_id ) {

    $user_obj = get_userdata( $user_id );
    $id_user = $user_obj->ID;

    include('../../../store/wp-config.php');

    //Conexão com o BD do WP
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $query = "DELETE FROM dw_user WHERE wp_user_id = {$id_user}";

    mysqli_query($link, $query);

    mysqli_close($link); 
    
}

/***************************************************************************/

//Salvando a alteração da cidade e estado na tabela externa
add_action( 'woocommerce_customer_save_address', 'update_city_state_ext_table' );
function update_city_state_ext_table($user_id) {

    $user_obj = get_userdata($user_id);

    $id_user = $user_obj->ID;
    $city = $user_obj->billing_city;
    $state = $user_obj->billing_state;
    $city_state = $city . " / " . $state;
    $country = $user_obj->billing_country;

    include('../../../store/wp-config.php');

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $query = "UPDATE dw_user SET city_state = '{$city_state}', country = '$country' WHERE wp_user_id = $id_user";

    mysqli_query($link, $query);

    mysqli_close($link);     

}

/***************************************************************************/


