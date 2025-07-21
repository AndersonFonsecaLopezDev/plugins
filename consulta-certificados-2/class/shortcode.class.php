<?php
class Shortcode
{
  public function __construct() {
    // Aquí enganchas el shortcode cuando se construya la clase
    add_shortcode('certificate_form', [$this, 'register_certificate_form_shortcode']);
  }

  public function getCertificateForm() {
        // Cargar jQuery
        wp_enqueue_script('jquery');
    wp_enqueue_style('bootstrap_css', plugins_url('../admin/bootstrap/css/bootstrap.min.css', __FILE__));
    wp_enqueue_script('personal_js', plugins_url('../admin/js/cc-search-certificate.js', __FILE__));
    wp_localize_script('personal_js', 'personal_ajax_request', [
      'url' => admin_url('admin-ajax.php'),
      'security' => wp_create_nonce('seg')
    ]);
  }

  public function register_certificate_form_shortcode() {
    $this->getCertificateForm();

    ob_start();
    ?>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-11">
      <div class="card shadow-lg border-0">
        <div class="card-body">
          <form id="cc-search-form">
            <div class="mb-3 row">
              <label for="certificate_number" class="form-label col-12">Número de documento</label>
              <input type="text" class="form-control col-9" id="document_text_box" name="certificate_number" placeholder="Ej: 123456" required>
              <div class="d-flex justify-content-center">
              <button type="button" class="btn btn-primary col-3 px-2" id="search_certificate_button">Buscar</button>
            </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="cc-search-result" class="mt-4 col-12" style="display:none;">
            <!-- Aquí se mostrará el resultado por JavaScript -->
          </div>
    <?php
    return ob_get_clean();
  }
}

// Crear una instancia de la clase para que todo se enganche
new Shortcode();
