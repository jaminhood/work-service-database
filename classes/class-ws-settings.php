<?php

if (!defined('ABSPATH')) exit(); # No direct access allowed.

if (!class_exists('WorkServiceSettings')) :
  class WorkServiceSettings
  {
    # User Role Arguements.
    private array $customer_args = array(
      'read'  => false,
      'delete_posts'  => false,
      'delete_published_posts' => false,
      'edit_posts'   => false,
      'publish_posts' => false,
      'upload_files'  => false,
      'edit_pages'  => false,
      'edit_published_pages'  =>  false,
      'publish_pages'  => false,
      'delete_published_pages' => false,
    );

    public function __construct()
    {
      add_action('after_setup_theme', [$this, 'remove_admin_bar']);
    }

    public static function on_activation(): void
    {
      $self = new self();
      # Add User Role On Registration.
      $self->add_user_roles();
      # create tables
      WorkServiceDB::create_tables();
    }

    private function add_user_roles(): void
    {
      # Create Customer User Role.
      add_role('customer', __('Customer'), $this->customer_args);
      # Create Expert User Role.
      add_role('expert', __('Expert'), $this->customer_args);
    }

    public static function on_deactivation(): void
    {
      $self = new self();
      # Deleting Users.
      WorkServiceDB::delete_accounts("customer");
      WorkServiceDB::delete_accounts("expert");
      # Add User Role On Registration.
      $self->remove_user_roles();
      # Delete Tables
      WorkServiceDB::delete_tables();
    }

    private function remove_user_roles(): void
    {
      # Remove User Roles.
      remove_role("customer");
      remove_role("expert");
    }

    public static function write_log($log): void
    {
      if (is_array($log) || is_object($log)) {
        error_log(print_r($log, true));
      } else {
        error_log($log);
      }
    }

    public static function send_emails(array $email_content): void
    {
      $email = $email_content['email'];
      $title = $email_content['title'] . " - Work Service Alert!!!";
      $name = $email_content['name'];
      $user_msg = $email_content['user_msg'];
      $admin_msg = $email_content['admin_msg'];

      $mail_headers = 'From: ' . $email . "\r\n" .
        'Reply-To: ' . get_option('business_email') . "\r\n" .
        'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

      try {
        # User's Mail
        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings $name!</h1><p style='font-size: 16px;'>$user_msg<br /><br />Cheers!!!<br />Work Service - Admin</p></body></html>";

        wp_mail($email, $title, $message_body, $mail_headers);

        # Admin's Mail
        $message_body = "<html><body><img src='https://myluxtrade.com/wp-content/plugins/lux-support%20-%20Copy/assets/imgs/logo-edited.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings!</h1><p style='font-size: 16px;'>$admin_msg<br /><br />Cheers!!!<br /><br />Work Service - Admin</p></body></html>";

        wp_mail(get_option('business_email'), $title, $message_body, $mail_headers);
      } catch (\Throwable $th) {
        self::write_log($th);
      }
    }

    public function remove_admin_bar(): void
    {
      if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
      }
    }

    private static function generateRandomString($length = 30)
    {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';

      for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
      }

      return $randomString;
    }

    private static function twoway_encrypt($input_str, $secret_key, $secret_iv, $action = 'e')
    {

      $output = null;

      $key = hash('sha256', $secret_key);
      $iv = substr(hash('sha256', $secret_iv), 0, 16);

      if ($action == 'e') {
        $output = base64_encode(openssl_encrypt($input_str, "AES-256-CBC", $key, 0, $iv));
      } else if ($action == 'd') {
        $output = openssl_decrypt(base64_decode($input_str), "AES-256-CBC",  $key, 0, $iv);
      }

      return $output;
    }

    public static function password_reset_eMail($user_id, $user_eMail)
    {

      $password_reset_token = self::generateRandomString();
      $password_reset_key = self::generateRandomString();
      $password_reset_iv = self::generateRandomString();
      $password_reset_string = $password_reset_token . "," . $password_reset_key . "," . $password_reset_iv;

      $encrypted_string = self::twoway_encrypt($password_reset_token,   $password_reset_key,  $password_reset_iv,  'e');

      $encrypted_string = substr($encrypted_string, 0, strlen($encrypted_string) - 1);

      // Send Mail
      $mail_subject = "Work Service Alert!!! You requested for Password Reset";
      $customer = get_userdata($user_id);
      $name = $customer->display_name;
      $username = $customer->user_login;
      $password_reset_url = site_url("/ws-auth/reset-password/?customer=$username&token=$encrypted_string");

      $email_body = "<p>Greetings $name</p><p>You are recieving this eMail because you requested for a password reset. <a href='$password_reset_url'>Follow this link</a> to reset your password</p><p>If you can't click on the link above, copy this link to your web browser - <code>$password_reset_url</code></p><p>If you haven't requested for any password reset, kindly ignore this eMail</p><p>Thank You</p>";

      function set_html_content_type()
      {
        return 'text/html';
      }

      add_filter('wp_mail_content_type', 'set_html_content_type');
      wp_mail($user_eMail, $mail_subject, $email_body);

      remove_filter('wp_mail_content_type', 'set_html_content_type');
      add_user_meta($user_id,  "password_reset_cridentials",  $password_reset_string);
    }
  }
endif;
