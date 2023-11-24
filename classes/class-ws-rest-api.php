<?php

if (!defined('ABSPATH')) exit(); # No direct access allowed.

require_once(ABSPATH . 'wp-admin/includes/user.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

if (!class_exists('WorkServiceDatabaseRestAPI')) :
  class WorkServiceDatabaseRestAPI
  {

    public function __construct()
    {
      $this->rest_api_init();
    }

    private function rest_api_init(): void
    {
      add_action('rest_api_init', [$this, 'create_rest_routes']);
    }

    public function create_rest_routes(): void
    {
      $api_v = 'ws-api/v1';

      # ====== Auth Endpoints ======

      register_rest_route('ws-auth/v1', 'sign-in', [
        'methods'  => 'POST',
        'callback' => [$this, 'login']
      ]);
      register_rest_route('ws-auth/v1', 'register', [
        'methods'  => 'POST',
        'callback' => [$this, 'register']
      ]);
      register_rest_route('ws-auth/v1', 'logout', [
        'methods'  => 'POST',
        'callback' => [$this, 'logout']
      ]);
      register_rest_route('ws-auth/v1', 'delete-account', [
        'methods'  => 'DELETE',
        'callback' => [$this, 'delete_account']
      ]);
      register_rest_route('ws-auth/v1', 'forgot-password', [
        'methods'  => 'POST',
        'callback' => [$this, 'forgot_password']
      ]);

      # ====== App Endpoints ======
      register_rest_route($api_v, 'categories', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_all_categories'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'sub-categories/category', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_sub_categories_of_category'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'services/sub-category', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_services_of_sub_category'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'services', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_services'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'bookings', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_customer_bookings'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'bookings', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_booking'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'review', [
        'methods'  => 'POST',
        'callback' => [$this, 'review_booking'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'address', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_user_address'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'address', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_user_address'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'chat/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_user_chat_messages'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'chat/post', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_user_chat_messages'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'requests', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_customer_requests'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'requests', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_customer_request'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'profile', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_user_profile'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'profile', [
        'methods'  => 'POST',
        'callback' => [$this, 'update_profile_picture'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'news', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_news'],
        'permission_callback' => [$this, 'permit_user']
      ]);

      # ====== Admin Endpoints ======
      register_rest_route($api_v, 'admin/categories', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_all_categories']
      ]);
      register_rest_route($api_v, 'admin/categories', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_category']
      ]);
      register_rest_route($api_v, 'admin/category', [
        'methods'  => 'POST',
        'callback' => [$this, 'update_category']
      ]);
      register_rest_route($api_v, 'admin/category', [
        'methods'  => 'DELETE',
        'callback' => [$this, 'delete_category']
      ]);
      register_rest_route($api_v, 'admin/sub-categories', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_sub_categories']
      ]);
      register_rest_route($api_v, 'admin/sub-categories/category', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_sub_categories_of_category']
      ]);
      register_rest_route($api_v, 'admin/sub-categories', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_sub_category']
      ]);
      register_rest_route($api_v, 'admin/sub-category', [
        'methods'  => 'POST',
        'callback' => [$this, 'update_sub_category']
      ]);
      register_rest_route($api_v, 'admin/sub-category', [
        'methods'  => 'DELETE',
        'callback' => [$this, 'delete_sub_category']
      ]);
      register_rest_route($api_v, 'admin/services', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_all_services']
      ]);
      register_rest_route($api_v, 'admin/services/sub-category', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_services_of_sub_category']
      ]);
      register_rest_route($api_v, 'admin/services', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_service']
      ]);
      register_rest_route($api_v, 'admin/service', [
        'methods'  => 'POST',
        'callback' => [$this, 'update_service']
      ]);
      register_rest_route($api_v, 'admin/service', [
        'methods'  => 'DELETE',
        'callback' => [$this, 'delete_service']
      ]);
      register_rest_route($api_v, 'admin/bookings', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_bookings']
      ]);
      register_rest_route($api_v, 'admin/booking', [
        'methods'  => 'POST',
        'callback' => [$this, 'update_booking']
      ]);
      register_rest_route($api_v, 'admin/address', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_address']
      ]);
      register_rest_route($api_v, 'chat/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_user_chat_messages'],
      ]);
      register_rest_route($api_v, 'chat/post', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_user_chat_messages'],
      ]);
      register_rest_route($api_v, 'admin/requests', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_requests'],
      ]);
      register_rest_route($api_v, 'profile/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_profiles'],
      ]);
      register_rest_route($api_v, 'admin/news', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_news']
      ]);
      register_rest_route($api_v, 'admin/news', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_news'],
      ]);
      register_rest_route($api_v, 'admin/news', [
        'methods'  => 'DELETE',
        'callback' => [$this, 'delete_news'],
      ]);
      register_rest_route($api_v, 'admin/trust', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_trust'],
      ]);
      register_rest_route($api_v, 'admin/trust', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_trust'],
      ]);
      register_rest_route($api_v, 'admin/trust', [
        'methods'  => 'DELETE',
        'callback' => [$this, 'delete_trust'],
      ]);
      register_rest_route($api_v, 'admin/about', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_about'],
      ]);
      register_rest_route($api_v, 'admin/about', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_about'],
      ]);
      register_rest_route($api_v, 'admin/reason', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_reason'],
      ]);
      register_rest_route($api_v, 'admin/reason', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_reason'],
      ]);
      register_rest_route($api_v, 'admin/reason', [
        'methods'  => 'DELETE',
        'callback' => [$this, 'delete_reason'],
      ]);
      register_rest_route($api_v, 'admin/team', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_team'],
      ]);
      register_rest_route($api_v, 'admin/team', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_team'],
      ]);
      register_rest_route($api_v, 'admin/team/update', [
        'methods'  => 'POST',
        'callback' => [$this, 'update_team'],
      ]);
      register_rest_route($api_v, 'admin/team', [
        'methods'  => 'DELETE',
        'callback' => [$this, 'delete_team'],
      ]);
      register_rest_route($api_v, 'contact/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_contact'],
      ]);
      register_rest_route($api_v, 'contact/post', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_contact'],
      ]);

      # ====== Users ======

      register_rest_route($api_v, 'service/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_single_service'],
      ]);
      register_rest_route($api_v, 'category/single/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_single_category']
      ]);
      register_rest_route($api_v, 'bookings/set', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_booking_react']
      ]);

      # === Profiles ===
      register_rest_route($api_v, 'profiles', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_all_profiles']
      ]);
      # === Categories ===
      register_rest_route($api_v, 'categories/web/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_all_categories']
      ]);
      register_rest_route($api_v, 'category/web/set', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_category_react']
      ]);
      register_rest_route($api_v, 'category/web/update', [
        'methods'  => 'POST',
        'callback' => [$this, 'update_category_react']
      ]);
      register_rest_route($api_v, 'category/web/delete', [
        'methods'  => 'DELETE',
        'callback' => [$this, 'delete_category_react']
      ]);
      # === Services ===
      register_rest_route($api_v, 'services/web/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_all_services_react']
      ]);
      register_rest_route($api_v, 'service/web/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_single_service_react']
      ]);
      register_rest_route($api_v, 'services/web/set', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_services_react']
      ]);
      register_rest_route($api_v, 'services/web/update', [
        'methods'  => 'POST',
        'callback' => [$this, 'update_services_react']
      ]);
      register_rest_route($api_v, 'services/web/delete', [
        'methods'  => 'DELETE',
        'callback' => [$this, 'delete_services_react']
      ]);
      # === Bookings ===
      register_rest_route($api_v, 'bookings/web/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_all_bookings_react']
      ]);
      register_rest_route($api_v, 'bookings/web/get-user', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_all_user_bookings_react']
      ]);
      register_rest_route($api_v, 'booking/web/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_single_booking_react']
      ]);
      register_rest_route($api_v, 'bookings/web/update', [
        'methods'  => 'POST',
        'callback' => [$this, 'update_bookings_react']
      ]);
      register_rest_route($api_v, 'bookings/web/delete', [
        'methods'  => 'DELETE',
        'callback' => [$this, 'delete_bookings_react']
      ]);
      # === Chats ===
      register_rest_route($api_v, 'chats/web/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_all_chats_react']
      ]);
      register_rest_route($api_v, 'chats/web/get-user', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_all_user_chats_react']
      ]);
      register_rest_route($api_v, 'chat/web/get', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_single_chat_react']
      ]);
      register_rest_route($api_v, 'chats/web/set', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_chats_react']
      ]);
      register_rest_route($api_v, 'chats/web/update', [
        'methods'  => 'POST',
        'callback' => [$this, 'update_chats_react']
      ]);
      register_rest_route($api_v, 'chats/web/delete', [
        'methods'  => 'DELETE',
        'callback' => [$this, 'delete_chats_react']
      ]);
    }

    public function permit_user()
    {
      $current_user = wp_get_current_user();
      if (in_array("customer", $current_user->roles) || in_array("expert", $current_user->roles)) {
        return true;
      }
      return false;
    }

    public function permit_admin()
    {
      $current_user = wp_get_current_user();
      if (in_array("administrator", $current_user->roles)) {
        return true;
      }
      return false;
    }

    # ====== Auth ======

    public function login($request)
    {
      if (!isset($request['username']) && !isset($request['password'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for customer registration', // data
          ['status' => 400] // status
        );
      }

      # Setting the query arguments to get the list of users
      $username = sanitize_text_field($request['username']);
      $password = sanitize_text_field($request['password']);
      $rememberMe = sanitize_text_field($request['rememberMe']);

      $rememberMeValue = false;

      if ($rememberMe == 1) {
        $rememberMeValue = true;
      }

      $creds = array(
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $rememberMeValue
      );

      $user = wp_signon($creds, is_ssl());

      $response = "";

      if (is_wp_error($user)) {
        $response = new WP_REST_Response($user->get_error_message());
      } else {
        $response = new WP_REST_Response($user);
      }

      $response->set_status(200);
      return $response;
    }

    public function register($request)
    {
      if (!isset($request['firstname']) && !isset($request['lastname']) && !isset($request['email']) && !isset($request['phone'])  && !isset($request['password']) && !isset($request['username']) && !isset($request['role'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for customer registration', // data
          ['status' => 400] // status
        );
      }

      # Setting the query arguments to get the list of users
      $email = sanitize_email($request['email']);
      $username = sanitize_text_field($request['username']);
      $phone = sanitize_text_field($request['phone']);
      $password = sanitize_text_field($request['password']);
      $first_name = sanitize_text_field($request['firstname']);
      $last_name = sanitize_text_field($request['lastname']);
      $role = sanitize_text_field($request['role']);

      if (email_exists($email)) {
        $response = new WP_REST_Response('this email address have been used by another user. kindly provide another');
        $response->set_status(200);
        return $response;
      }

      if (username_exists($username)) {
        $response = new WP_REST_Response('this username address have been used by another user. kindly provide another');
        $response->set_status(200);
        return $response;
      }

      $userID = WorkServiceDB::setup_new_account([
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'email'      => $email,
        'phone'      => $phone,
        'password'   => $password,
        'username'   => $username,
        'role'       => $role
      ]);

      $userParam = array("userID" => $userID);

      WorkServiceDB::set_user_address($userParam);
      WorkServiceDB::set_user_profile($userParam);

      $response = new WP_REST_Response('User Created Successfully');
      $response->set_status(200);
      return $response;
    }

    public function forgot_password($request)
    {
      if (!isset($request['email'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for customer registration', // data
          ['status' => 400] // status
        );
      }

      # Setting the query arguments to get the list of users
      $email = sanitize_email($request['email']);

      if (!(email_exists($email))) {
        return new WP_Error(
          'this email does not exist', // code
          'this email address is not registered to any user', // data
          array('status' => 401) // status
        );
      }

      WorkServiceSettings::password_reset_eMail(email_exists($email), $email);

      $response = new WP_REST_Response('recovery email sent successfully');
      $response->set_status(200);
      return $response;
    }

    public function logout()
    {
      wp_logout();
      $response = new WP_REST_Response('Logged Out Successfully');
      $response->set_status(200);
      return $response;
    }

    public function delete_account($request)
    {
      if (!isset($request['id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for account deletion', // data
          ['status' => 400] // status
        );
      }

      WorkServiceDB::delete_account($request['id']);

      $response = new WP_REST_Response('User Deleted Successfully');
      $response->set_status(200);
      return $response;
    }

    # ====== App ======

    public function get_all_categories()
    {
      try {
        $categories = array();

        foreach (WorkServiceDB::get_categories() as $category) :
          $categories[] = array(
            'categoryID' => $category->categoryID,
            'categoryName' => $category->categoryName,
            'categoryIconID' => $category->categoryIcon,
            'categoryIcon' => wp_get_attachment_url($category->categoryIcon),
            'timestamp' => $category->timestamp,
          );
        endforeach;

        $response = new WP_REST_Response($categories);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get categories - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function get_sub_categories_of_category($request)
    {
      if (!isset($request['category_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['category_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      try {
        $sub_categories = WorkServiceDB::get_sub_categories_of_category($id);
        foreach ($sub_categories as $sub_category) :
          $sub_category->icon = wp_get_attachment_url($sub_category->subCategoryIcon);
          unset($sub_category->categoryIcon);
        endforeach;

        $response = new WP_REST_Response($sub_categories);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get categories - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function get_services()
    {
      try {
        $services = WorkServiceDB::get_services();
        foreach ($services as $service) :
          $service->icon = wp_get_attachment_url($service->serviceIcon);
          unset($service->categoryIcon);
          unset($service->subCategoryIcon);
        endforeach;

        $response = new WP_REST_Response($services);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get categories - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function get_services_of_sub_category($request)
    {
      if (!isset($request['sub_category_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['sub_category_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      try {
        $services = WorkServiceDB::get_services_by_sub_category($id);
        foreach ($services as $service) :
          $service->icon = wp_get_attachment_url($service->serviceIcon);
          unset($service->categoryIcon);
          unset($service->subCategoryIcon);
        endforeach;

        $response = new WP_REST_Response($services);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get categories - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_booking($request)
    {
      if (!isset($request['service_id']) && !isset($request['booking_desc']) && !isset($request['booking_name'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $customer_id = get_current_user_id();
      $service_id = $request['service_id'];
      $booking_desc = sanitize_text_field($request['booking_desc']);
      $booking_name = sanitize_text_field($request['booking_name']);

      $booking = WorkServiceDB::set_bookings(array(
        'userID' => $customer_id,
        'serviceID' => $service_id,
        'bookingDesc' => $booking_desc,
        'bookingType' => $booking_name,
      ));

      $param = array('bookingID' => $booking->bookingID, 'userID' => $customer_id);

      WorkServiceDB::set_ratings($param);
      WorkServiceDB::set_reviews($param);

      $response = new WP_REST_Response($booking);
      $response->set_status(200);
      return $response;
    }

    public function get_customer_bookings()
    {
      $id = get_current_user_id();

      try {
        $bookings = WorkServiceDB::get_customer_bookings($id);

        foreach ($bookings as $booking) :
          $booking->icon = wp_get_attachment_url($booking->serviceIcon);
          unset($booking->serviceIcon);
          unset($booking->serviceID);
          unset($booking->subCategoryID);
          unset($booking->timestamp);
          unset($booking->reviewDate);
          unset($booking->ratingID);
        endforeach;

        $response = new WP_REST_Response($bookings);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function review_booking($request)
    {
      if (!isset($request['booking_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $bookingID = $request['booking_id'];

      if (isset($request['booking_rate'])) {
        WorkServiceDB::update_booking_ratings(array('ratingValue' => $request['booking_rate']), $bookingID);
      }

      if (isset($request['booking_review'])) {
        WorkServiceDB::update_booking_reviews(array('reviewText' => $request['booking_review']), $bookingID);
      }

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    private function unsets($data)
    {
      foreach ($data as $address) :
        unset($address->user_login);
        unset($address->ID);
        unset($address->user_pass);
        unset($address->user_email);
        unset($address->user_nicename);
        unset($address->user_url);
        unset($address->user_registered);
        unset($address->user_activation_key);
        unset($address->user_status);
        unset($address->display_name);
      endforeach;

      return $data;
    }

    public function get_address()
    {
      try {
        $addresses = WorkServiceDB::get_address();

        foreach ($addresses as $address) :
          $address->username = $address->user_login;
        endforeach;

        $addresses = $this->unsets($addresses);

        $response = new WP_REST_Response($addresses);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function get_user_address()
    {
      $id = get_current_user_id();

      try {
        $addresses = WorkServiceDB::get_user_address($id);

        foreach ($addresses as $address) :
          $address->username = $address->user_login;
        endforeach;

        $addresses = $this->unsets($addresses);

        $response = new WP_REST_Response($addresses);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_user_address($request)
    {
      $user_id = get_current_user_id();

      if (isset($request['address_line_one'])) :
        $address_line_one = sanitize_text_field($request['address_line_one']);
        $param = array('streetAddress' => $address_line_one);
        WorkServiceDB::update_user_address($param, $user_id);
      endif;

      if (isset($request['address_city'])) :
        $address_city = sanitize_text_field($request['address_city']);
        $param = array('city' => $address_city);
        WorkServiceDB::update_user_address($param, $user_id);
      endif;

      if (isset($request['address_state'])) :
        $address_state = sanitize_text_field($request['address_state']);
        $param = array('state' => $address_state);
        WorkServiceDB::update_user_address($param, $user_id);
      endif;

      if (isset($request['address_country'])) :
        $address_country = sanitize_text_field($request['address_country']);
        $param = array('country' => $address_country);
        WorkServiceDB::update_user_address($param, $user_id);
      endif;

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function get_user_chat_messages($request)
    {
      if (!isset($request['booking_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for getting Service Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = get_current_user_id();

      try {
        $response = new WP_REST_Response(WorkServiceDB::get_user_chats($id, $request['booking_id']));
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing chats', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_user_chat_messages($request)
    {
      if (!isset($request['chat_sender']) && !isset($request['chat_message']) && !isset($request['chat_type']) && !isset($request['chat_booking_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $user_id = get_current_user_id();
      $chat_type = $request['chat_type'];
      $chat_booking_id = $request['chat_booking_id'];
      $chat_sender = sanitize_text_field($request['chat_sender']);
      $chat_message = sanitize_text_field($request['chat_message']);

      WorkServiceDB::set_chat(array(
        'user_id' => $user_id,
        'chat_type' => $chat_type,
        'booking_id' => $chat_booking_id,
        'chat_sender' => $chat_sender,
        'chat_message' => $chat_message,
      ));

      $response = new WP_REST_Response('Sent Successful');
      $response->set_status(200);
      return $response;
    }

    public function get_customer_requests()
    {
      $id = get_current_user_id();

      try {
        $requests = WorkServiceDB::get_user_requests($id);

        foreach ($requests as $request) :
          $request->username = $request->user_login;
        endforeach;

        $requests = $this->unsets($requests);

        $response = new WP_REST_Response($requests);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_customer_request($request)
    {
      if (!isset($request['requests_name']) && !isset($request['requests_desc'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $user_id = get_current_user_id();
      $requests_name = sanitize_text_field($request['requests_name']);
      $requests_desc = sanitize_text_field($request['requests_desc']);

      WorkServiceDB::set_requests(array(
        'userID' => $user_id,
        'requestName' => $requests_name,
        'requestDesc' => $requests_desc,
      ));

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function get_user_profile()
    {
      $user_id = get_current_user_id();

      try {
        $profile = WorkServiceDB::get_user_profile($user_id);

        foreach ($profile as $data) :
          $data->username = $data->user_login;
          $data->image = wp_get_attachment_url($data->profileImg);
        endforeach;

        $profile = $this->unsets($profile);

        $response = new WP_REST_Response($profile[0]);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function update_profile_picture()
    {
      if (!isset($_FILES['file'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Profile Endpoint', // data
          array('status' => 400) // status
        );
      }

      $user_id = get_current_user_id();

      try {
        $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');

        if (in_array($_FILES['file']['type'], $arr_img_ext)) {
          $upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));
          $type = '';

          if (!empty($upload['type'])) {
            $type = $upload['type'];
          } else {
            $mime = wp_check_filetype($upload['file']);

            if ($mime) {
              $type = $mime['type'];
            }
          }

          $attachment = array('post_title' => basename($upload['file']), 'post_content' => '', 'post_type' => 'attachment', 'post_mime_type' => $type, 'guid' => $upload['url']);
          $profile_img = wp_insert_attachment($attachment, $upload['file']);

          wp_update_attachment_metadata($profile_img, wp_generate_attachment_metadata($profile_img, $upload['file']));
        } else {
          return new WP_Error(
            'error processing order', // code
            "error processing image", // data
            ['status' => 400] // status
          );
        }

        WorkServiceDB::update_user_profile(
          array(
            'profileImg' => $profile_img,
          ),
          $user_id
        );

        $response = new WP_REST_Response('Upload Successful');
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {

        return new WP_Error(
          'error processing order', // code
          "an error occured while trying to process the giftcard order - $th", // data
          ['status' => 400] // status
        );
      }
    }

    public function get_news()
    {
      try {
        $news = WorkServiceDB::get_news();

        foreach ($news as $new) :
          $new->banner_img = wp_get_attachment_url($new->newsImg);
          unset($new->newsImg);
        endforeach;

        $response = new WP_REST_Response($news);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing news', # code
          "an error occured while trying to get news - $th", # data
          ['status' => 400] # status
        );
      }
    }

    # ====== Admin ======
    public function set_category($request)
    {
      if (!isset($request['category_name']) && !isset($request['category_icon'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $category_name = sanitize_text_field($request['category_name']);
      $category_icon = $request['category_icon'];

      WorkServiceDB::set_category(array(
        'categoryIcon' => $category_icon,
        'categoryName' => $category_name,
      ));

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function update_category($request)
    {
      if (!isset($request['category_id']) && !isset($request['category_name']) && !isset($request['category_icon'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $category_id = $request['category_id'];

      if (isset($request['category_name'])) :
        $category_name = $request['category_name'];
        WorkServiceDB::update_category(array('categoryName' => $category_name), $category_id);
      endif;

      if (isset($request['category_icon'])) :
        $category_icon = $request['category_icon'];
        WorkServiceDB::update_category(array('categoryIcon' => $category_icon), $category_id);
      endif;

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function delete_category($request)
    {
      if (!isset($request['category_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $category_id = $request['category_id'];

      WorkServiceDB::delete_category($category_id);

      $response = new WP_REST_Response('Delete Successful');
      $response->set_status(200);
      return $response;
    }

    public function get_sub_categories()
    {
      try {
        $sub_categories = WorkServiceDB::get_sub_categories();
        foreach ($sub_categories as $sub_category) :
          $sub_category->icon = wp_get_attachment_url($sub_category->subCategoryIcon);
          unset($sub_category->categoryIcon);
        endforeach;

        $response = new WP_REST_Response($sub_categories);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get categories - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_sub_category($request)
    {
      if (!isset($request['category_id']) && !isset($request['sub_category_name']) && !isset($request['sub_category_icon'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $sub_category_name = sanitize_text_field($request['sub_category_name']);
      $category_id = $request['category_id'];
      $sub_category_icon = $request['sub_category_icon'];

      WorkServiceDB::set_sub_category(array(
        'categoryID' => $category_id,
        'subCategoryIcon' => $sub_category_icon,
        'subCategoryName' => $sub_category_name,
      ));

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function update_sub_category($request)
    {
      if (!isset($request['sub_category_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $sub_category_id = $request['sub_category_id'];

      if (isset($request['category_id'])) :
        $category_id = $request['category_id'];
        WorkServiceDB::update_sub_category(array('categoryID' => $category_id), $sub_category_id);
      endif;

      if (isset($request['sub_category_name'])) :
        $sub_category_name = sanitize_text_field($request['sub_category_name']);
        WorkServiceDB::update_sub_category(array('subCategoryName' => $sub_category_name), $sub_category_id);
      endif;

      if (isset($request['sub_category_icon'])) :
        $sub_category_icon = $request['sub_category_icon'];
        WorkServiceDB::update_sub_category(array('subCategoryIcon' => $sub_category_icon), $sub_category_id);
      endif;

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function delete_sub_category($request)
    {
      if (!isset($request['sub_category_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $sub_category_id = $request['sub_category_id'];

      WorkServiceDB::delete_sub_category($sub_category_id);

      $response = new WP_REST_Response('Delete Successful');
      $response->set_status(200);
      return $response;
    }

    public function get_all_services()
    {
      try {
        $services = WorkServiceDB::get_services();
        foreach ($services as $service) :
          $service->icon = wp_get_attachment_url($service->serviceIcon);
          unset($service->categoryIcon);
        endforeach;

        $response = new WP_REST_Response($services);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get categories - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_service($request)
    {
      if (!isset($request['sub_category_id']) && !isset($request['service_name']) && !isset($request['service_icon'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $service_name = sanitize_text_field($request['service_name']);
      $sub_category_id = $request['sub_category_id'];
      $service_icon = $request['service_icon'];

      WorkServiceDB::set_service(array(
        'subCategoryID' => $sub_category_id,
        'serviceIcon' => $service_icon,
        'serviceName' => $service_name,
      ));

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function update_service($request)
    {
      if (!isset($request['service_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $service_id = $request['service_id'];

      if (isset($request['sub_category_id'])) :
        $sub_category_id = $request['sub_category_id'];
        WorkServiceDB::update_service(array('subCategoryID' => $sub_category_id), $service_id);
      endif;

      if (isset($request['service_icon'])) :
        $service_icon = $request['service_icon'];
        WorkServiceDB::update_service(array('serviceIcon' => $service_icon), $service_id);
      endif;

      if (isset($request['service_name'])) :
        $service_name = sanitize_text_field($request['service_name']);
        WorkServiceDB::update_service(array('serviceName' => $service_name), $service_id);
      endif;

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function delete_service($request)
    {
      if (!isset($request['service_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $service_id = $request['service_id'];

      WorkServiceDB::delete_service($service_id);

      $response = new WP_REST_Response('Delete Successful');
      $response->set_status(200);
      return $response;
    }

    public function get_bookings()
    {
      try {
        $bookings = WorkServiceDB::get_bookings();

        foreach ($bookings as $booking) :
          $booking->icon = wp_get_attachment_url($booking->serviceIcon);
          unset($booking->serviceIcon);
          unset($booking->serviceID);
          unset($booking->subCategoryID);
          unset($booking->timestamp);
          unset($booking->reviewDate);
          unset($booking->ratingID);
        endforeach;

        $response = new WP_REST_Response($bookings);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function update_booking($request)
    {
      if (!isset($request['booking_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $bookingID = $request['booking_id'];

      if (isset($request['booking_status'])) :
        WorkServiceDB::update_booking(array(
          'bookingStatus' => $request['booking_status'],
        ), $bookingID);
      endif;

      if (isset($request['booking_price'])) :
        WorkServiceDB::update_booking(array(
          'bookingPrice' => $request['booking_price'],
        ), $bookingID);
      endif;

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function get_requests()
    {
      try {
        $response = new WP_REST_Response(WorkServiceDB::get_requests());
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function get_profiles()
    {
      try {
        $response = new WP_REST_Response(WorkServiceDB::get_all_profiles());
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_news($request)
    {
      if (!isset($request['newsImg'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      try {
        $newsImg = $request['newsImg'];

        WorkServiceDB::set_news(array('newsImg' => $newsImg));

        $response = new WP_REST_Response('Upload Successful');
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing news', # code
          "an error occured while trying to get news - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function delete_news($request)
    {
      if (!isset($request['newsID'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['newsID'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      WorkServiceDB::delete_news($id);

      $response = new WP_REST_Response('Deleted Successfully');
      $response->set_status(200);
    }

    public function get_trust()
    {
      try {
        $trust_banner = WorkServiceDB::get_trust();

        foreach ($trust_banner as $trust) :
          $trust->trusted_img = wp_get_attachment_url($trust->trustedImg);
          unset($trust->trustedImg);
        endforeach;

        $response = new WP_REST_Response($trust_banner);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing news', # code
          "an error occured while trying to get news - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_trust($request)
    {
      if (!isset($request['trusted_img'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      try {
        $trustedImg = $request['trusted_img'];

        WorkServiceDB::set_trust(array(
          'trustedImg' => $trustedImg,
        ));

        $response = new WP_REST_Response('Upload Successful');
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing news', # code
          "an error occured while trying to get news - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function delete_trust($request)
    {
      if (!isset($request['trust_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['trust_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      WorkServiceDB::delete_trust($id);

      $response = new WP_REST_Response('Deleted Successfully');
      $response->set_status(200);
    }

    public function get_about()
    {
      try {
        // $about = WorkServiceDB::get_about();
        // $response = new WP_REST_Response($about);
        $response = new WP_REST_Response(WorkServiceDB::get_about());
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing news', # code
          "an error occured while trying to get news - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_about($request)
    {
      if (!isset($request['aboutStory']) && !isset($request['expertise']) && !isset($request['convenience']) && !isset($request['trust']) && !isset($request['innovation'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      try {
        $aboutStory = $request['aboutStory'];
        $expertise = $request['expertise'];
        $convenience = $request['convenience'];
        $trust = $request['trust'];
        $innovation = $request['innovation'];

        WorkServiceDB::set_about_us(array(
          'aboutStory' => $aboutStory,
          'expertise' => $expertise,
          'convenience' => $convenience,
          'trust' => $trust,
          'innovation' => $innovation,
        ));

        $response = new WP_REST_Response('Upload Successful');
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing news', # code
          "an error occured while trying to get news - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function get_reason()
    {
      try {
        $benefit_info = WorkServiceDB::get_reason();

        // foreach (WorkServiceDB::get_reason() as $benefit) :
        //   $benefit_info[] = array(
        //     'benefit_id' => $benefit->benefit_id,
        //     'benefit_heading' => $benefit->benefit_heading,
        //     'benefit_paragraph' => $benefit->benefit_paragraph,
        //   );
        // endforeach;

        $response = new WP_REST_Response($benefit_info);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing news', # code
          "an error occured while trying to get news - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_reason($request)
    {
      if (!isset($request['benefit_heading']) && !isset($request['benefit_paragraph'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      try {
        $benefit_heading = $request['benefit_heading'];
        $benefit_paragraph = $request['benefit_paragraph'];

        WorkServiceDB::set_reason(array(
          'benefitHeading' => $benefit_heading,
          'benefitParagraph' => $benefit_paragraph,
        ));

        $response = new WP_REST_Response('Upload Successful');
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing news', # code
          "an error occured while trying to get news - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function delete_reason($request)
    {
      if (!isset($request['benefit_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['benefit_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      WorkServiceDB::delete_reason($id);

      $response = new WP_REST_Response('Deleted Successfully');
      $response->set_status(200);
    }

    public function get_team()
    {
      try {
        $team_info = WorkServiceDB::get_team();

        foreach ($team_info as $team) :
          $team->team_img = wp_get_attachment_url($team->teamImg);
        // unset($team->teamedImg);
        endforeach;

        $response = new WP_REST_Response($team_info);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing Team', # code
          "an error occured while trying to get Team - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_team($request)
    {
      if (!isset($request['team_name']) && !isset($request['team_role']) && !isset($request['team_img'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      try {
        $team_name = $request['team_name'];
        $team_role = $request['team_role'];
        $team_img = $request['team_img'];

        WorkServiceDB::set_team(array('teamName' => $team_name, 'teamRole' => $team_role, 'teamImg' => $team_img));

        $response = new WP_REST_Response('Upload Successful');
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing news', # code
          "an error occured while trying to get news - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function update_team($request)
    {
      if (!isset($request['team_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $teamID = $request['team_id'];
      $team_name = $request['team_name'];
      $team_role = $request['team_role'];
      $team_img = $request['team_img'];

      WorkServiceDB::update_team(array(
        'teamName' => $team_name,
        'teamRole' => $team_role,
        'teamImg' => $team_img
      ), $teamID);

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function delete_team($request)
    {
      if (!isset($request['team_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['team_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      WorkServiceDB::delete_team($id);

      $response = new WP_REST_Response('Deleted Successfully');
      $response->set_status(200);
    }

    public function get_contact()
    {
      try {
        $contact = WorkServiceDB::get_contact()[count(WorkServiceDB::get_contact()) - 1];

        $contact_info = array(
          'contact_phone' => $contact->contact_phone,
          'contact_email' => $contact->contact_email,
          'contact_address' => $contact->contact_address,
          'contact_facebook' => $contact->contact_facebook,
          'contact_whatsapp' => $contact->contact_whatsapp,
          'contact_instagram' => $contact->contact_instagram,
        );

        $response = new WP_REST_Response($contact_info);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing news', # code
          "an error occured while trying to get news - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_contact($request)
    {
      if (!isset($request['contact_phone']) && !isset($request['contact_email']) && !isset($request['contact_address']) && !isset($request['contact_facebook']) && !isset($request['contact_whatsapp']) && !isset($request['contact_instagram'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      try {
        $contact_phone = $request['contact_phone'];
        $contact_email = $request['contact_email'];
        $contact_address = $request['contact_address'];
        $contact_facebook = $request['contact_facebook'];
        $contact_whatsapp = $request['contact_whatsapp'];
        $contact_instagram = $request['contact_instagram'];

        WorkServiceDB::set_contact(array(
          'contact_phone' => $contact_phone,
          'contact_email' => $contact_email,
          'contact_address' => $contact_address,
          'contact_facebook' => $contact_facebook,
          'contact_whatsapp' => $contact_whatsapp,
          'contact_instagram' => $contact_instagram,
        ));

        $response = new WP_REST_Response('Upload Successful');
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing news', # code
          "an error occured while trying to get news - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function update_contact($request)
    {
      if (!isset($request['team_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $teamID = $request['team_id'];
      $team_name = $request['team_name'];
      $team_role = $request['team_role'];
      $team_img = $request['team_img'];
      $team_desc = $request['team_desc'];

      WorkServiceDB::update_team(array(
        'team_name' => $team_name,
        'team_role' => $team_role,
        'team_img' => $team_img,
        'team_desc' => $team_desc,
      ), $teamID);

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    # ======

    public function get_single_service($request)
    {
      if (!isset($request['service_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for getting Service Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['service_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      try {
        $ser = array();

        foreach (WorkServiceDB::get_service($id) as $service) :
          $ser = array(
            'service_id' => $service->service_id,
            'service_name' => $service->service_name,
            'service_icon_id' => $service->service_icon,
            'service_icon' => wp_get_attachment_url($service->service_icon),
            'service_desc' => $service->service_desc,
            'sub_category_id' => $service->sub_category_id,
            'sub_category' => WorkServiceDB::get_category($service->sub_category_id)[0]->category_name,
            'service_date' => $service->date_added,
          );
        endforeach;

        $response = new WP_REST_Response($ser);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function get_single_sub_category($request)
    {
      if (!isset($request['sub_category_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['sub_category_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      try {
        $categories = array();

        foreach (WorkServiceDB::get_sub_category($id) as $sub_category) :
          $categories[] = array(
            'sub_category_id' => $sub_category->sub_category_id,
            'sub_category_name' => $sub_category->sub_category_name,
            'sub_category_icon_id' => $sub_category->sub_category_icon,
            'sub_category_icon' => wp_get_attachment_url($sub_category->sub_category_icon),
            'sub_category_date' => $sub_category->time_stamp,
          );
        endforeach;

        $response = new WP_REST_Response($categories);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get categories - $th", # data
          ['status' => 400] # status
        );
      }
    }

    private function bookings_fetch_data($params)
    {
      $bookings = array();

      foreach ($params as $booking) :
        $user_id = $booking->customer_id;
        $user_name = get_user_by('id', $user_id)->display_name;
        $service_icon = 0;
        $service_name = "Customer Service";
        $service_sub_category = "Customer Service";
        $booking_status = $booking->booking_status;
        $booking_price = $booking->booking_price;
        $booking_desc = $booking->booking_desc;
        $booking_name = $booking->booking_name;
        $booking_rate = $booking->booking_rate;
        $booking_date = $booking->time_stamp;

        if ($booking->service_id != 0) {
          $service = WorkServiceDB::get_service($booking->service_id)[0];

          $service_icon = wp_get_attachment_url($service->service_icon);
          $service_name = $service->service_name;
          $service_sub_category = WorkServiceDB::get_sub_category($service->sub_category_id)->sub_category_name;
        }

        $bookings[] = array(
          'user_id' => $user_id,
          'user_name' => $user_name,
          'booking_id' => $booking->booking_id,
          'service_icon' => $service_icon,
          'service_name' => $service_name,
          'service_category' => $service_sub_category,
          'booking_status' => $booking_status,
          'booking_price' => $booking_price,
          'booking_desc' => $booking_desc,
          'booking_name' => $booking_name,
          'booking_rate' => $booking_rate,
          'booking_date' => $booking_date,
        );
      endforeach;

      return array_reverse($bookings);
    }

    public function get_all_bookings_react($request)
    {
      if (!isset($request['user_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for getting Service Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['user_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      try {
        $role = 'administrator';
        $users = get_users(array('role' => array($role)));

        $bookings = array();

        if ($id == 0) {
          $bookings = $this->bookings_fetch_data(WorkServiceDB::get_bookings());
        } else {
          $bookings = $this->bookings_fetch_data(WorkServiceDB::get_single_user_bookings($id));
        }

        $response = new WP_REST_Response($bookings);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get categories - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function get_all_user_bookings_react($request)
    {
      if (!isset($request['user_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for getting Service Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['user_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      try {
        $bookings = array();

        foreach (WorkServiceDB::get_single_user_bookings($id) as $booking) :
          $service_icon = 0;
          $service_name = "Customer Service";
          $service_category = "Customer Service";
          $booking_status = $booking->booking_status;
          $booking_price = $booking->booking_price;
          $booking_desc = $booking->booking_desc;
          $booking_location = $booking->booking_location;
          $booking_rate = $booking->booking_rate;
          $booking_date = $booking->date_added;

          if ($booking->service_id != 0) {
            $service = WorkServiceDB::get_service($booking->service_id)[0];

            $service_icon = wp_get_attachment_url($service->service_icon);
            $service_name = $service->service_name;
            $service_category = WorkServiceDB::get_category($service->service_category_id)[0]->category_name;
          }

          $bookings[] = array(
            'booking_id' => $booking->id,
            'service_icon' => $service_icon,
            'service_name' => $service_name,
            'service_category' => $service_category,
            'booking_status' => $booking_status,
            'booking_price' => $booking_price,
            'booking_desc' => $booking_desc,
            'booking_location' => $booking_location,
            'booking_rate' => $booking_rate,
            'booking_date' => $booking_date,
          );
        endforeach;

        $response = new WP_REST_Response($bookings);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    # ====== Admin ======

    public function get_all_profiles($request)
    {
      if (!isset($request['type'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for Profile Endpoint', // data
          ['status' => 400] // status
        );
      }

      $type = $request->get_param('type');
      if (!is_numeric($type)) {
        return new WP_Error(
          'Type is not a number', # code
          'This type you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      $response = new WP_REST_Response("Type Not Found");

      # ====== Get All Profiles ======
      if ($type == 0) {
        $profiles = WorkServiceDB::get_all_profiles();
        $response = new WP_REST_Response($profiles);
      }

      # ====== Get All Customers Profile ======
      if ($type == 1) {
        $profiles = WorkServiceDB::get_all_customers_profile();
        $response = new WP_REST_Response($profiles);
      }

      # ====== Get All Experts Profile ======
      if ($type == 2) {
        $profiles = WorkServiceDB::get_all_experts_profile();
        $response = new WP_REST_Response($profiles);
      }

      $response->set_status(200);
      return $response;
    }

    public function get_single_category($request)
    {
      if (!isset($request['category_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['category_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      try {
        $categories = array();

        foreach (WorkServiceDB::get_category($id) as $category) :
          $categories[] = array(
            'category_id' => $category->id,
            'category_name' => $category->category_name,
            'category_icon_id' => $category->category_icon,
            'category_icon' => wp_get_attachment_url($category->category_icon),
            'category_date' => $category->date_added,
          );
        endforeach;

        $response = new WP_REST_Response($categories);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get categories - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_category_react($request)
    {
      if (!isset($request['category_name']) && !isset($request['category_icon'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $category_name = sanitize_text_field($request['category_name']);
      $category_icon = $request['category_icon'];

      WorkServiceDB::set_category(array(
        'category_name' => $category_name,
        'category_icon' => $category_icon
      ));

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function update_category_react($request)
    {
      if (!isset($request['category_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['category_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      $category_name = sanitize_text_field($request['category_name']);
      $category_icon = $request['category_icon'];

      WorkServiceDB::update_category(array(
        'category_name' => $category_name,
        'category_icon' => $category_icon
      ), $id);

      $response = new WP_REST_Response('Updated Successfully');
      $response->set_status(200);
      return $response;
    }

    public function delete_category_react($request)
    {
      if (!isset($request['category_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['category_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      WorkServiceDB::delete_category($id);

      // foreach (WorkServiceDB::get_services_by_category($id) as $service) :
      //  WorkServiceDB::delete_service($service->id);
      // endforeach;

      $response = new WP_REST_Response('Deleted Successfully');
      $response->set_status(200);
      return $response;
    }

    public function get_all_services_react()
    {
      try {
        $services = array();

        foreach (WorkServiceDB::get_services() as $service) :
          $services[] = array(
            'service_id' => $service->id,
            'service_name' => $service->service_name,
            'service_icon_id' => $service->service_icon,
            'service_icon' => wp_get_attachment_url($service->service_icon),
            'service_desc' => $service->service_desc,
            'service_category_id' => $service->service_category_id,
            'service_category' => WorkServiceDB::get_category($service->service_category_id)[0]->category_name,
            'service_date' => $service->date_added,
          );
        endforeach;

        $response = new WP_REST_Response($services);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get categories - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_services_react($request)
    {
      if (!isset($request['service_name']) && !isset($request['service_icon']) && !isset($request['service_desc']) && !isset($request['service_category_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $service_name = sanitize_text_field($request['service_name']);
      $service_desc = sanitize_text_field($request['service_desc']);
      $service_category_id = $request['service_category_id'];
      $service_icon = $request['service_icon'];

      WorkServiceDB::set_service(array(
        'service_name' => $service_name,
        'service_desc' => $service_desc,
        'service_category_id' => $service_category_id,
        'service_icon' => $service_icon,
      ));

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function update_services_react($request)
    {
      if (!isset($request['service_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['service_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      $service_name = sanitize_text_field($request['service_name']);
      $service_desc = sanitize_text_field($request['service_desc']);
      $service_category_id = $request['service_category_id'];
      $service_icon = $request['service_icon'];

      WorkServiceDB::update_service(array(
        'service_name' => $service_name,
        'service_desc' => $service_desc,
        'service_category_id' => $service_category_id,
        'service_icon' => $service_icon,
      ), $id);

      $response = new WP_REST_Response('Updated Successfully');
      $response->set_status(200);
      return $response;
    }

    public function delete_services_react($request)
    {
      if (!isset($request['service_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Service Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['service_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      WorkServiceDB::delete_service($id);
      foreach (WorkServiceDB::get_all_specific_service_booking($id) as $service_booking) {
        WorkServiceDB::delete_chat($service_booking->booking_id);
      };
      WorkServiceDB::delete_booking($id);

      $response = new WP_REST_Response('Deleted Successfully');
      $response->set_status(200);
      return $response;
    }

    public function get_single_booking_react($request)
    {
      if (!isset($request['user_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for getting Service Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['user_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      try {
        $bookings = array();

        foreach (WorkServiceDB::get_service($id) as $booking) :
          $service = WorkServiceDB::get_service($booking->service_id)[0];

          $service_icon = wp_get_attachment_url($service->service_icon);
          $service_name = $service->service_name;
          $service_category = WorkServiceDB::get_category($service->service_category_id)[0]->category_name;
          $booking_status = $booking->booking_status;
          $booking_price = $booking->booking_price;
          $booking_desc = $booking->booking_desc;
          $booking_location = $booking->booking_location;
          $booking_rate = $booking->booking_rate;
          $booking_date = $booking->date_added;

          $bookings[] = array(
            'booking_id' => $booking->id,
            'service_icon' => $service_icon,
            'service_name' => $service_name,
            'service_category' => $service_category,
            'booking_status' => $booking_status,
            'booking_price' => $booking_price,
            'booking_desc' => $booking_desc,
            'booking_location' => $booking_location,
            'booking_rate' => $booking_rate,
            'booking_date' => $booking_date,
          );
        endforeach;

        $response = new WP_REST_Response($bookings);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_booking_react($request)
    {
      if (!isset($request['user_id']) && !isset($request['service_id']) && !isset($request['booking_desc']) && !isset($request['booking_name'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $user_id = $request['user_id'];
      $service_id = $request['service_id'];
      $booking_desc = sanitize_text_field($request['booking_desc']);
      $booking_name = sanitize_text_field($request['booking_name']);

      WorkServiceDB::set_bookings(array(
        'customer_id' => $user_id,
        'service_id' => $service_id,
        'booking_desc' => $booking_desc,
        'booking_name' => $booking_name,
      ));

      $response = new WP_REST_Response('Upload Successful');
      $response->set_status(200);
      return $response;
    }

    public function update_bookings_react($request)
    {
      if (!isset($request['booking_id']) && !isset($request['booking_status'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['booking_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      $booking_status = $request['booking_status'];

      WorkServiceDB::update_bookings(array(
        'booking_status' => $booking_status,
      ), $id);

      $response = new WP_REST_Response('Updated Successfully');
      $response->set_status(200);
      return $response;
    }

    public function delete_bookings_react($request)
    {
      if (!isset($request['service_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Service Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['service_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      WorkServiceDB::delete_service($id);

      $response = new WP_REST_Response('Deleted Successfully');
      $response->set_status(200);
      return $response;
    }

    public function get_all_chats_react()
    {
      if (!isset($request['user_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for getting Service Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['user_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      $role = 'admin';

      //Get a list of users that belongs to the specified role
      $users = get_users(array('role' => array($role)));


      try {
        // $services = array();

        // foreach (WorkServiceDB::get_all_services() as $service) :
        //  $services[] = array(
        //   'service_id' => $service->id,
        //   'service_name' => $service->service_name,
        //   'service_icon_id' => $service->service_icon,
        //   'service_icon' => wp_get_attachment_url($service->service_icon),
        //   'service_desc' => $service->service_desc,
        //   'service_category' => WorkServiceDB::get_category($service->service_category_id)[0]->category_name,
        //   'service_date' => $service->date_added,
        //  );
        // endforeach;

        $response = new WP_REST_Response($users);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get categories - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function get_all_user_chats_react($request)
    {
      if (!isset($request['user_id']) && !isset($request['booking_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for getting Service Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['user_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      try {
        $response = new WP_REST_Response(WorkServiceDB::get_user_booking_chats($id, $request['booking_id']));
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing chats', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function get_single_chat_react($request)
    {
      if (!isset($request['user_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for getting Service Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['user_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      try {
        $bookings = array();

        foreach (WorkServiceDB::get_service($id) as $booking) :
          $service = WorkServiceDB::get_service($booking->service_id)[0];

          $service_icon = wp_get_attachment_url($service->service_icon);
          $service_name = $service->service_name;
          $service_category = WorkServiceDB::get_category($service->service_category_id)[0]->category_name;
          $booking_status = $booking->booking_status;
          $booking_price = $booking->booking_price;
          $booking_desc = $booking->booking_desc;
          $booking_location = $booking->booking_location;
          $booking_rate = $booking->booking_rate;
          $booking_date = $booking->date_added;

          $bookings[] = array(
            'booking_id' => $booking->id,
            'service_icon' => $service_icon,
            'service_name' => $service_name,
            'service_category' => $service_category,
            'booking_status' => $booking_status,
            'booking_price' => $booking_price,
            'booking_desc' => $booking_desc,
            'booking_location' => $booking_location,
            'booking_rate' => $booking_rate,
            'booking_date' => $booking_date,
          );
        endforeach;

        $response = new WP_REST_Response($bookings);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing categories', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function set_chats_react($request)
    {
      if (!isset($request['user_id']) && !isset($request['chat_sender']) && !isset($request['chat_message']) && !isset($request['chat_type']) && !isset($request['chat_booking_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $user_id = $request['user_id'];
      $chat_type = $request['chat_type'];
      $chat_booking_id = $request['chat_booking_id'];
      $chat_sender = sanitize_text_field($request['chat_sender']);
      $chat_message = sanitize_text_field($request['chat_message']);

      WorkServiceDB::set_user_chat(array(
        'user_id' => $user_id,
        'chat_type' => $chat_type,
        'booking_id' => $chat_booking_id,
        'chat_sender' => $chat_sender,
        'chat_message' => $chat_message,
      ));

      $response = new WP_REST_Response('Sent Successful');
      $response->set_status(200);
      return $response;
    }

    public function update_chats_react($request)
    {
      if (!isset($request['service_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Category Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['service_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      $service_name = sanitize_text_field($request['service_name']);
      $service_desc = sanitize_text_field($request['service_desc']);
      $service_category_id = $request['service_category_id'];
      $service_icon = $request['service_icon'];

      WorkServiceDB::update_service(array(
        'service_name' => $service_name,
        'service_desc' => $service_desc,
        'service_category_id' => $service_category_id,
        'service_icon' => $service_icon,
      ), $id);

      $response = new WP_REST_Response('Updated Successfully');
      $response->set_status(200);
      return $response;
    }

    public function delete_chats_react($request)
    {
      if (!isset($request['service_id'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting Service Endpoint', // data
          ['status' => 400] // status
        );
      }

      $id = $request['service_id'];
      if (!is_numeric($id)) {
        return new WP_Error(
          'ID is not a number', # code
          'This id you sent is not a numerical value', # message
          ['status' => 400] # status
        );
      }

      WorkServiceDB::delete_service($id);

      $response = new WP_REST_Response('Deleted Successfully');
      $response->set_status(200);
      return $response;
    }
  }
  new WorkServiceDatabaseRestAPI;
endif;
