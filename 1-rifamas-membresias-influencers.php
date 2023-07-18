<?php
/*
Plugin Name: 1.Rifamas Membresias influencers
Plugin URI: 
Description: 
Version: 
Author: 
Author URI: 
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


// Función para crear una página
function create_page_if_not_exist($titulo, $contenido) {
    $pagina = get_page_by_title($titulo); // Verificar si la página ya existe

    if (!$pagina) {
        $pagina_id = wp_insert_post(array(
            'post_title'    => $titulo,
            'post_content'  => $contenido,
            'post_status'   => 'publish',
            'post_type'     => 'page',
        ));

        if ($pagina_id !== 0) {
            return true; // Página creada con éxito
            echo "Página creada con éxito";
        } else {
            return false; // Error al crear la página
            echo "Error al crear la página";
        }
    }

    return false; // La página ya existe
}

// Función para ejecutar al activar el plugin
function active_plugin_membership() {
    // Crear las páginas si no existen
    create_page_if_not_exist('Pagina de producto de prueba', '[crear_producto]');
    create_page_if_not_exist('Editar membresía', '[edit_membresia_autor]');
    create_page_if_not_exist('Administrar membresía', '[info_membresia_autor]');
    create_page_if_not_exist('Participar en membresías', '[participar_membresia]');
    create_page_if_not_exist('Cancelar membresía', '[cancel_membresia_autor]');
    // Agrega más páginas según tus necesidades
}

// Gancho de activación del plugin
register_activation_hook(__FILE__, 'active_plugin_membership');




require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
include_once dirname(__FILE__).'/include/class_participar_membresia.php';
include_once dirname(__FILE__).'/include/class_info_membership.php';
include_once dirname(__FILE__).'/include/class_edit_membership.php';
include_once dirname(__FILE__).'/include/class_delete_suspend_membership.php';


add_shortcode( 'crear_producto', 'shortcode_crear_membresia' );

function shortcode_crear_membresia() {
  ob_start();

  if (isset($_POST['submit-producto'])) {
    $post_data = $_POST['producto'];
    crear_membresia($post_data);
  } else {
    formulario_crear_membresia();
  }
  return ob_get_clean();
}


function formulario_crear_membresia() { ?>

    <H3 class="title_center_membership">Creando tu membresía...</H3>
    <div class="container">
        <form id="formulario-crear-producto" enctype="multipart/form-data" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <div class="pasos">

                <!-- Paso 1 -->
                <div class="paso-1 active">
                    <h4 class="text-main">Paso 1.</h4>
                    <div class="container-pasos">
                        
                        <!-- Div logo -->
                        <div>
                            <label class="text-main" for="logo">Logo influencer/marca<span class="required">*</span></label>
                            <?php $logo_author = get_user_meta(get_current_user_id(), 'logo_membership', true);
                            $logo_url = wp_get_attachment_url($logo_author,'thumbnail'); ?>

                              <div class="center_button">
                                <div id="loaded-logo"> <?php
                                 if ($logo_url) {
                                     echo '<img src="' . esc_url($logo_url) . '">';
                                 }
                                 ?></div>
                                
                                <button class="cargar-logo-influencer">
                                    <input href="#" id="logo" name="logo" class="upload-images" type="file" <?php if (empty($logo_url)) echo 'required'; ?>  multiple="" accept="image/jpeg,.jpg,.jpeg,image/gif,.gif,image/png,.png,image/bmp,.bmp">
                                    <p class="text-add-images">AÑADIR FOTO</p>
                                </button>
                            </div>
                        </div>
                    
                        <!-- Div title -->
                        <div>
                          <label class="text-main" for="nombre-producto">Título de membresía<span class="required">*</span></label>
                          <input type="text" name="nombre-producto" id="nombre-producto" required>
                        </div>
                    
                        <!-- Div descripction -->
                        <div>
                          <label class="text-main" for="descripcion">Descripción<span class="required">*</span></label>
                          <textarea name="descripcion" id="descripcion" required></textarea>
                        </div>
                    </div>
                    <button class="siguiente-paso button-right">Siguiente</button>
                </div>

                <!-- Paso 2 -->
                <div class="paso-2">
                    <h4 class="text-main">Paso 2.</h4>
                  
                    <div class="container-pasos">
                    
                        <div>
                            <h6 class="text-main" for="define-cuota">Define cuantas opciones de cuota.<span class="required">*</span>
                            </h6>
                            <div class="cuotas-wrapper">
                                <select name="opciones-cuota" id="opciones-cuota">
                                    <option value="" selected>&#9660;</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                                <p class="title-select text-main" for="define-cuota"><span class="required">*</span>Máximo se permiten 3 cuotas diferentes.</p>
                            </div>
                        </div>
                        <div class="separator"></div>
                        <div>
                            <h6 class="text-main" for="define-cuota">Define cuantos sorteos seran al mes.<span class="required">*</span>
                            </h6>
                            <div class="cuotas-wrapper">
                                <select name="opciones-sorteos" id="opciones-sorteos">
                                    <option value="" selected>&#9660;</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                                <p class="title-select text-main" for="define-sorteos"><span class="required">*</span>Máximo se permiten 4 sorteos al mes.</p>
                            </div>
                        </div>
                    </div>
                    <button class="anterior-paso">Anterior</button>
                    <button class="siguiente-paso button-right">Siguiente</button>
                </div>
        
                <!-- Paso 3 -->
                <div class="paso-3">
                    <h4 class="text-main">Paso 3.</h4>
                    <h6 class="text-main text-number-cuota"></h6>
                    <div class="container-pasos"></div>
                </div>
                
               <!-- Paso 4 -->
                <div class="paso-4" style="display: none;">
                    <h4 class="text-main">Paso 4.</h4>
                    <h6 class="text-main text-number-product"></h6>
                    <div class="container-pasos"></div>
                    <!-- Contenido del paso 4 -->
                </div>
            </div>
        </form>
    </div> 
<?php }

function agregar_estilos() {
    wp_enqueue_style( 'estilos', plugins_url( 'css/paginadeprueba.css',__FILE__ ), TRUE,'1.0.16','all');
    wp_enqueue_script('controles', plugins_url( 'js/rifamas_mem_influencer.js',__FILE__ ), TRUE,'1.0.21','all');

    wp_enqueue_script('administracion', plugins_url('js/rifamas_mem_admin.js', __FILE__), array('jquery'), '1.0.02', true);
    wp_enqueue_style( 'admin_estilos', plugins_url( 'css/admin_panel_membership.css',__FILE__ ), TRUE,'1.0.08','all');
    
    wp_localize_script('ajax_membership','vars',['ajaxurl'=>admin_url('admin-ajax.php')]);
    
    wp_enqueue_style( 'sweet-alert-2', 'https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.8.0/sweetalert2.min.css', array(), '7.8.0', 'all' );
    wp_enqueue_script( 'sweet-alert-2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array('jquery'), '11.0.0', true );
    
    wp_enqueue_style( 'slickstyle', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', false, null );
    wp_enqueue_script( 'slickscript', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array( 'jquery' ), null, true );

}
add_action( 'wp_enqueue_scripts', 'agregar_estilos' );


function procesar_creacion_membresia(){
    
    $qty_cuotas = array();
    $qty_papeletas = array();
    if ( isset( $_POST['variacion-1'] ) && ! empty( $_POST['variacion-1'] ) ) {
        $qty_cuotas['Cuota1'] = wp_kses_post( $_POST['variacion-1'] );
        $qty_papeletas['_papeletas_option_1'] = $_POST['papeletas-variacion-1'];
    }
    if ( isset( $_POST['variacion-2'] ) && ! empty( $_POST['variacion-2'] ) ) {
        $qty_cuotas['Cuota2'] = wp_kses_post( $_POST['variacion-2'] );
        $qty_papeletas['_papeletas_option_2'] = $_POST['papeletas-variacion-2'];
    }
    if ( isset( $_POST['variacion-3'] ) && ! empty( $_POST['variacion-3'] ) ) {
        $qty_cuotas['Cuota3'] = wp_kses_post( $_POST['variacion-3'] );
        $qty_papeletas['_papeletas_option_3'] = $_POST['papeletas-variacion-3'];
    }
    if ( isset( $_POST['opciones-sorteos'] ) && ! empty( $_POST['opciones-sorteos'] ) ) {
       $option_sorteos = wp_kses_post ( $_POST['opciones-sorteos'] );
    }
    if (isset($_FILES['logo']) && ! empty( $_FILES['logo'])) {
        $logo_membership = $_FILES['logo'];   
    }
    $descripcion = $_POST['descripcion'];
    

   

        create_product_variation( array( 
            'author'        => get_current_user_id(), // optional
            'title'         => wp_kses_post ( $_POST['nombre-producto'] ),
            'content'       => wp_kses_post ($descripcion),
            'excerpt'       => '',
            'regular_price' => '', // product regular price
            'sale_price'    => '', // product sale price (optional)
            'stock'         => '', // Set a minimal stock quantity
            'image_id'      => '', // optional
            'gallery_ids'   => array(), // optional
            'sku'           => '', // optional
            'tax_class'     => '', // optional
            'weight'        => '', // optional
            // For NEW attributes/values use NAMES (not slugs)
            'attributes'    => array(
                'Cuota'     => $qty_cuotas
            ),
            'option_sorteos'=> $option_sorteos,
            'option_papelet'=> $qty_papeletas,
            'logo_member'   => $logo_membership,
            'prod_gallery_1'=> $product_gallery_1
        ) );
}


/**
 * Create a new variable product (with new attributes if they are).
 * (Needed functions:
 *
 * @since 3.0.0
 * @param array $data | The data to insert in the product.
 */

function create_product_variation( $data ){
    if( ! function_exists ('add_custom_attribute') ) return;

    $postname = sanitize_title( $data['title'] );
    $postcontent = sanitize_title( $data['content'] );
    $author = empty( $data['author'] ) ? '1' : $data['author'];

    $post_data = array(
        'post_author'   => $author,
        'post_name'     => $postname,
        'post_title'    => $data['title'],
        'post_content'  => $postcontent,
        'post_excerpt'  => $data['excerpt'],
        'post_status'   => 'publish',
        'ping_status'   => 'closed',
        'post_type'     => 'product',
        'guid'          => home_url( '/product/'.$postname.'/' ),
    );

    // Creating the product (post data)
    $product_id = wp_insert_post( $post_data );
    
    
    
    ##---------------------Save metadata post_id--------------------##
    if (!is_wp_error($product_id)) {
        // Agrega tus metadatos 
        add_post_meta($product_id, '_option_sorteos', intval($data['option_sorteos']));


    foreach( $data['option_papelet'] as $option_papeleta => $option ){
        add_post_meta($product_id, $option_papeleta, $option);
    }
        //add_post_meta($product_id, '_option_sorteos', 'valor_meta_2');
    }


    ##---------------------Save logo membership--------------------##

    $file_logo = $data['logo_member'];
    $file_tmp = $file_logo['tmp_name'];

    $old_attachment_id = get_user_meta($author, 'logo_membership', true);
    $old_attachment_url = wp_get_attachment_url($old_attachment_id);
    $old_attachment_filename = basename($old_attachment_url);

    // Verifica si se ha cargado una nueva imagen
    if (!empty($file_tmp) && $file_logo['name'] !== $old_attachment_filename) {
        // Elimina la meta data existente 'logo_membership'
        delete_user_meta($author, 'logo_membership');

           // Elimina el archivo físico y su registro de la biblioteca de medios si existe
        if (!empty($old_attachment_id)) {
            wp_delete_attachment($old_attachment_id, true);
        }

        $attachment_url = cargar_imagen_comprimida($file_logo['name'], $file_tmp);

        if (is_numeric($attachment_url)) {
    
            // Actualizar el campo de ID de foto del producto con la ID de la foto cargada
            update_user_meta($author, 'logo_membership', $attachment_url);
        } else {
            // Hubo un error al cargar el archivo comprimido
            //echo $attachment_url;
        }
    } else {
       
    }

    ##--------------------- Save WC_Product_Variable_Subscription --------------------##
    
    // Get an instance of the WC_Product_Variable object and save it
    $product = new WC_Product_Variable_Subscription( $product_id );
    $product->save();

    ## ---------------------- Other optional data  ---------------------- ##

    // IMAGES GALLERY
    if( ! empty( $data['gallery_ids'] ) && count( $data['gallery_ids'] ) > 0 )
        $product->set_gallery_image_ids( $data['gallery_ids'] );

    // SKU
    if( ! empty( $data['sku'] ) )
        $product->set_sku( $data['sku'] );

    // STOCK (stock will be managed in variations)
    //$product->set_stock_quantity( $data['stock'] ); // Set a minimal stock quantity
    //$product->set_manage_stock(false);
    //$product->set_stock_status('');

    // Tax class
    if( empty( $data['tax_class'] ) )
        $product->set_tax_class( $data['tax_class'] );

    // WEIGHT
    if( ! empty($data['weight']) )
        $product->set_weight(''); // weight (reseting)
    else
        $product->set_weight($data['weight']);

    $product->validate_props(); // Check validation



    ## ---------------------- VARIATION ATTRIBUTES ---------------------- ##
    $product_attributes = array();
   
    foreach( $data['attributes'] as $key => $terms ){
       
        
        $attr_name = ucfirst($key);
        //print_r($terms);
        $attr_slug = sanitize_title($key);
        

        $taxonomy = wc_attribute_taxonomy_name(wp_unslash($key)); //pa_cuota
        // NEW Attributes: Register and save them
        
        if (taxonomy_exists($taxonomy))
        {
            $attribute_id = wc_attribute_taxonomy_id_by_name($attr_slug);   
        }else{
            $attribute_id = add_custom_attribute($attr_name);
        }
        
        $product_attributes[$taxonomy] = array (
            'name'         => $taxonomy,
            'value'        => '',
            'position'     => '',
            'is_visible'   => 0,
            'is_variation' => 1,
            'is_taxonomy'  => 1
        );
        

        if($attribute_id){
            //Iterando a través de los atributos de variaciones
            foreach ($terms as $name_cuota => $value_cuota ) {
                $taxonomy = 'pa_'.$attr_slug; // The attribute taxonomy
                
                //key es cuota y term_name el valor
                // Si la taxonomía no existe la creamos
                if( ! taxonomy_exists( $taxonomy ) ){
                    register_taxonomy(
                        $taxonomy,
                    'product_variation',
                        array(
                            'hierarchical' => false,
                            'label' => $attr_name,
                            'query_var' => true,
                            'rewrite' => array( 'slug' => $attr_slug), // The base slug
                        ),
                    );
                }
                
                // Verificamos si el Nombre del Término existe y si no lo creamos.
                if( ! term_exists( $name_cuota, $taxonomy ) ){
                    wp_insert_term( $name_cuota, $taxonomy ); // Create the term
                }

                $term_slug = get_term_by('name', $name_cuota, $taxonomy )->slug; // Get the term slug
                
                if ($term_slug === strtolower($name_cuota)) {
                    $product_variation = new WC_Product_Variation(); 
                    $product_variation->set_parent_id( $product_id );
                    $product_variation->set_attributes( array( $taxonomy => $term_slug ) );
                    $product_variation->set_regular_price($value_cuota );
                    $product_variation->set_sale_price( $value_cuota );
                    //$product_variation->set_billing_period('month');
                    //$product_variation->set_interval(1);
                    //$product_variation->set_manage_stock( $variation['manage_stock'] );
                    //$product_variation->set_stock_quantity( $variation['stock_qty'] );
                    //$product_variation->set_tax_class( $variation['tax_class'] );
                    //$product_variation->set_status( $variation['status'] );
                    $product_variation->save();
                    $variation_id = $product_variation->get_id();
                    update_post_meta($variation_id, '_virtual' , 'yes' );
                    update_post_meta($variation_id, '_subscription_period' , 'month' );
                    update_post_meta($variation_id, '_subscription_period_interval' , 1 );
                    update_post_meta($variation_id, '_subscription_payment_sync_date' , 28);
                } 
                // Get the post Terms names from the parent variable product.
                $post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );
                
                // Check if the post term exist and if not we set it in the parent variable product.
                if( ! in_array( $name_cuota, $post_term_names ) )
                    wp_set_post_terms( $product_id, $name_cuota, $taxonomy, true );
            }
            $position = array_search($name_cuota, array_keys($terms));
            $product_attributes[$taxonomy]['position'] = $position !== false ? $position : '';
        }
    }     
    
    update_post_meta( $product_id, '_product_attributes', $product_attributes ); 
    $product->save(); // Save the data
    add_user_meta($author, 'id_membership_product', $product_id);
    $hide_membership = array( 'exclude-from-search', 'exclude-from-catalog' ); // for hidden..
    wp_set_post_terms( $product_id, $hide_membership, 'product_visibility', false );

    $response = array(
        'productoId' => $product_id,
        'sorteos'    => intval($data['option_sorteos'])
    );

    $jsonResponse = wp_send_json($response);

    header('Content-Type: application/json');
    echo $jsonResponse;



}

/*
* Register a global woocommerce product add attribute Class.
*
* @param str   $nam | name of attribute
* @param arr   $vals | array of variations
* 
*/
function add_custom_attribute($nam){

    $attrs = array();      
    $attributes = wc_get_attribute_taxonomies(); 

    $slug = sanitize_title($nam);

    foreach ($attributes as $key => $value) {
        array_push($attrs,$attributes[$key]->attribute_name);                    
    } 
 
    if (!in_array( $nam, $attrs ) ) {          
        $args = array(
            'slug'    => $slug,
            'name'    => __( $nam, 'woocommerce' ),
            'type'    => 'select',
            'orderby' => 'menu_order',
            'has_archives'  => false,
        );                    
        return wc_create_attribute($args);
    }               
}

add_action( 'admin_post_crear_membresia', 'procesar_creacion_membresia' );
add_action( 'admin_post_nopriv_crear_membresia', 'procesar_creacion_membresia' );
add_action( 'wp_ajax_nopriv_crear_membresia', 'procesar_creacion_membresia');
add_action( 'wp_ajax_crear_membresia','procesar_creacion_membresia');


function procesar_creacion_producto(){
    
    $title_gif = $_POST['name'];
    
    $description = $_POST['description'];
    
    //orden del numero del producto que se cargo
    $data = $_POST['data'];
    
    $img_product = $_FILES['imagenes'];
    
    //Id de la membresia
    $main_product = $_POST['product_ID'];
    
    //Fecha de sorteo del producto que se cargo
    $date = $_POST['date'];

    $finish_form = $_POST['finish'];

    $date_active_membership = date("Y-m-d", strtotime("01-" . date("m-Y", strtotime("+1 month"))));

    //echo 'Mensaje de depuración o seguimiento'.$date_active_membership.' versus '. $date;

    if (strtotime($date) < strtotime($date_active_membership)) {
        //echo "La fecha seleccionada es menor que la fecha de membresía activa.";   
    }else{
        $post = array(
            //'ID'            => $original_post_id,
            'post_title'    => $title_gif,
            'post_status'   => 'publish',
            'post_type'     => 'product',
            'post_excerpt'  => str_replace('<br />', '',$description),
            'post_author'   => get_current_user_id(),
            'meta_input'    => array(
                '_lottery_dates_from'      => $date_active_membership,
                '_lottery_dates_to'        => $date,
                '_lottery_use_pick_numbers'=> 'yes',
                '_manage_stock'            => 'yes',
                '_max_tickets'             => '0',
                '_stock'                   =>  0 ,
                '_stock_status'            => 'instock',
                '_virtual'                 => 'yes',
                'product_type'             => 'lottery',
                '_lottery_num_winners'     => 1,
                'id_membership_data'       => $main_product
            )
        );
       

    
        //guarda el producto
        $product_membership_id = wp_insert_post($post);
        $attachment_urls = array();

        wp_set_object_terms($product_membership_id , 'lottery', 'product_type');
        
        $terms = array( 'exclude-from-search', 'exclude-from-catalog' ); // for hidden..
        wp_set_post_terms( $product_membership_id, $terms, 'product_visibility', false );
        
        // Recorrer los archivos y procesarlos
        foreach ($img_product['name'] as $key => $nombreArchivo) {
            $tmpName = $img_product['tmp_name'][$key];
            $error = $img_product['error'][$key];
            $attachment_url = cargar_imagen_comprimida($nombreArchivo, $tmpName);
            $attachment_urls[] = $attachment_url;
        }

        // Convertir las URLs en una cadena separada por comas
        $product_image_gallery = implode(',', $attachment_urls);

        // Guardar la cadena de URLs en el metadato del producto
        update_post_meta($product_membership_id, '_product_image_gallery', $product_image_gallery);

        // Obtener el ID del primer adjunto de imagen
        $attachment_ids = explode(',', $product_image_gallery);

        $first_attachment_id = isset($attachment_ids[0]) ? intval($attachment_ids[0]) : 0;

        if ($first_attachment_id > 0) {
            // Establecer la primera imagen como la imagen destacada del producto
            set_post_thumbnail($product_membership_id, $first_attachment_id);
        }


        // Obtener el último número asociado al metadato
        $last_number = 1; // Valor predeterminado si no hay otros metadatos almacenados

        //$existing_meta = get_post_meta($main_product, 'id_product_gif_', false);
        $existing_meta = get_post_meta($main_product);

        // Extraer los números de los metadatos existentes
        $numbers = array();
        foreach ($existing_meta as $meta_key => $meta_value) {
            if (preg_match('/^id_product_gif_(\d+)$/', $meta_key, $matches)) {
                $numbers[] = intval($matches[1]);
                echo intval($matches[1]);
            }
        }
        // Obtener el número máximo
        $max_number = max($numbers);

        // Incrementar el número máximo en 1 para obtener el último número
        $last_number = $max_number + 1;
        

        // Construir el nuevo metadato con el número
        $new_meta = 'id_product_gif_' . $last_number;

        // Guardar el nuevo metadato del producto en la membresía principal
        add_post_meta($main_product, $new_meta, $product_membership_id, false);

        //add_post_meta($main_product, 'id_product_gif', $product_membership_id, false);
    }
}

add_action( 'wp_ajax_crear_producto','procesar_creacion_producto');
add_action( 'wp_ajax_nopriv_crear_producto', 'procesar_creacion_producto');




function call_gallery(){
    $product_id = $_POST['product_id'];

    $product_image_gallery = get_post_meta($product_id, '_product_image_gallery', true);
    $image_ids = explode(',', $product_image_gallery);

    $image_urls = array();
    foreach ($image_ids as $image_id) {
        $image_url = wp_get_attachment_image_url($image_id, 'full');
        if ($image_url) {
            $image_urls[] = $image_url;
        }
    }

    wp_send_json($image_urls);
}
add_action( 'wp_ajax_call_gallery','call_gallery');
add_action( 'wp_ajax_nopriv_call_gallery', 'call_gallery');



function cargar_imagen_comprimida($nombre_archivo, $archivo_tmp, $calidad = 80) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image-edit.php');

    if (!empty($archivo_tmp)) {
        // Obtener el editor de imágenes de WordPress
        $image_editor = wp_get_image_editor($archivo_tmp);

        if (!is_wp_error($image_editor)) {
            // Comprimir la imagen reduciendo su calidad
            $image_editor->set_quality($calidad);
            $compressed_file = $image_editor->save();

            if (!is_wp_error($compressed_file)) {
                // Guardar el archivo comprimido en la biblioteca de medios con wp_upload_bits()
                $file_data = file_get_contents($compressed_file['path']);
                $upload = wp_upload_bits($nombre_archivo, null, $file_data);

                if (isset($upload['error']) && !empty($upload['error'])) {
                    // Manejar el error al cargar el archivo comprimido
                    return 'Error al cargar el archivo comprimido: ' . $upload['error'];
                } else {
                    // El archivo comprimido se cargó correctamente
                    // Obtener la ID del archivo adjunto
                    $attachment_id = 0;
                    if (isset($upload['file'])) {
                        $attachment = array(
                            'post_mime_type' => $upload['type'],
                            'post_title' => sanitize_file_name($upload['file']),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $attachment_id = wp_insert_attachment($attachment, $upload['file']);
                        if (!is_wp_error($attachment_id)) {
                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                            wp_update_attachment_metadata($attachment_id, $attachment_data);
                        }
                    }

                    return $attachment_id;
                }
            } else {
                // Manejar el error al comprimir la imagen
                return 'Error al comprimir la imagen: ' . $compressed_file->get_error_message();
            }
        } else {
            // Manejar el error al obtener el editor de imágenes
            return 'Error al obtener el editor de imágenes: ' . $image_editor->get_error_message();
        }
    } else {
        // No se proporcionó una ubicación temporal válida
        return 'No se proporcionó una ubicación temporal válida para el archivo.';
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

