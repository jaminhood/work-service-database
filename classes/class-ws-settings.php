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
        $message_body = "<html><body><img src='https://myworkservice.com/wp-content/themes/work-service-theme-design/build/images/word-logo.be7e1ca2.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings $name!</h1><p style='font-size: 16px;'>$user_msg<br /><br />Cheers!!!<br />Work Service - Admin</p></body></html>";

        wp_mail($email, $title, $message_body, $mail_headers);

        # Admin's Mail
        $message_body = "<html><body><img src='https://myworkservice.com/wp-content/themes/work-service-theme-design/build/images/word-logo.be7e1ca2.png' alt='logo' style='max-width: 70px;margin-left: 1rem;'><h1 style='color: green;'>Greetings!</h1><p style='font-size: 16px;'>$admin_msg<br /><br />Cheers!!!<br /><br />Work Service - Admin</p></body></html>";

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

    private static function generateRandomNumbers($length = 6)
    {
      $characters = '0123456789';
      $charactersLength = strlen($characters);
      $randomNumber = '';

      for ($i = 0; $i < $length; $i++) {
        $randomNumber .= $characters[rand(0, $charactersLength - 1)];
      }

      return $randomNumber;
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
      $password_reset_pin = self::generateRandomNumbers();

      // Send Mail
      $customer = get_userdata($user_id);
      $name = $customer->display_name;

      $user_msg = "You are recieving this eMail because you requested for a password reset. <strong>$password_reset_pin</strong>, Enter this pin to reset your password. If you haven't requested for any password reset, kindly ignore this eMail";
      $admin_msg = "You are recieving this eMail because $name requested for a password reset.";

      $email_content = [
        'email'     => $user_eMail,
        'title'     => "Password Reset",
        'name'      => $name,
        'user_msg'  => $user_msg,
        'admin_msg' => $admin_msg,
      ];

      WorkServiceSettings::send_emails($email_content);
      add_user_meta($user_id,  "password_reset_cridentials",  $password_reset_pin);
    }


    public static function check_pin($user_id, $token)
    {
      $pin_string = get_user_meta($user_id, "password_reset_cridentials");
      if ($token == $pin_string) {
        return true;
      }
      return false;
    }
  }
endif;
