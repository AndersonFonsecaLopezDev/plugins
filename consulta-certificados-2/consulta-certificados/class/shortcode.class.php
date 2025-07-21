<?php
class shortcode
{
  public function getCertificateForm()
  {
    wp_enqueue_style('bootstrap_css', plugins_url('../admin/bootstrap/css/bootstrap.min.css', __FILE__));
    wp_enqueue_script('personal_js', plugins_url('../admin/js/cc-search-certificate.js', __FILE__));
    wp_localize_script('personal_js', 'personal_ajax_request', [
      // Wordpress utiiza siempre admin-ajax.php para las consultas
      'url' => admin_url('admin-ajax.php'),
      'security' => wp_create_nonce('seg')
    ]);
  }
}
