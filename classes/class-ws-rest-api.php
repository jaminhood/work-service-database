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
      register_rest_route('ws-auth/v1', 'revalidate', [
        'methods'  => 'POST',
        'callback' => [$this, 'revalidate']
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
      register_rest_route($api_v, 'chats', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_user_chat_list'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'chat', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_user_chat_list'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'messages', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_messages'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'message', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_message'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'requests', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_customer_requests'],
        'permission_callback' => [$this, 'permit_user']
      ]);
      register_rest_route($api_v, 'request', [
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
      register_rest_route($api_v, 'admin/chat', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_user_chat_list'],
      ]);
      register_rest_route($api_v, 'chat/post', [
        'methods'  => 'POST',
        'callback' => [$this, 'set_user_chat_list'],
      ]);
      register_rest_route($api_v, 'admin/requests', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_requests'],
      ]);
      register_rest_route($api_v, 'admin/profile', [
        'methods'  => 'GET',
        'callback' => [$this, 'get_all_profiles'],
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
          'incomplete fields were submitted for customer login', // data
          array('status' => 400) // status
        );
      }

      try {
        # Setting the query arguments to get the list of users
        $username = sanitize_text_field($request['username']);
        $password = sanitize_text_field($request['password']);
        $rememberMe = $request['rememberMe'];

        $rememberMeValue = false;

        if ($rememberMe == 1) {
          $rememberMeValue = true;
        }

        $creds = array('user_login' => $username, 'user_password' => $password, 'remember' => $rememberMeValue);
        $user = wp_signon($creds, is_ssl());

        $response = "";

        if (is_wp_error($user)) {
          $response = new WP_REST_Response($user->get_error_message());
          $response->set_status(400);
          return $response;
        }

        $response = new WP_REST_Response($user);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing user login', # code
          "an error occured while trying to sign in a user", # data
          array('status' => 400) # status
        );
      }
    }

    public function register($request)
    {
      if (!isset($request['firstname']) && !isset($request['lastname']) && !isset($request['email']) && !isset($request['phone'])  && !isset($request['password']) && !isset($request['username']) && !isset($request['role'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for customer registration', // data
          array('status' => 400) // status
        );
      }

      try {
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
          $response->set_status(400);
          return $response;
        }

        if (username_exists($username)) {
          $response = new WP_REST_Response('this username address have been used by another user. kindly provide another');
          $response->set_status(400);
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
        $userPassParam = array("userID" => $userID, 'userPass' => wp_hash_password($password));

        WorkServiceDB::set_user_address($userParam);
        WorkServiceDB::set_user_profile($userParam);
        WorkServiceDB::set_user_password($userPassParam);

        $response = new WP_REST_Response('User Created Successfully');
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing user registration', # code
          "an error occured while trying to register a user", # data
          array('status' => 400) # status
        );
      }
    }

    public function forgot_password($request)
    {
      if (!isset($request['email'])) {
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for forgot password', // data
          array('status' => 400) // status
        );
      }

      try {
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
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing forgot password', # code
          "an error occured while trying to register a user", # data
          array('status' => 400) # status
        );
      }
    }

    public function logout()
    {
      wp_logout();
      $response = new WP_REST_Response('Logged Out Successfully');
      $response->set_status(200);
      return $response;
    }

    public function revalidate()
    {
      $user = WorkServiceDB::get_user_password();

      // $user['userPass'] = wp_;
      wp_logout();
      $response = new WP_REST_Response($user);
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
          "an error occured while trying to get categories", # data
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

    private function get_chat_list($chats)
    {
      try {
        foreach ($chats as $chat) :
          if ($chat->serviceID != 0) :
            $chat->serviceName = WorkServiceDB::get_service($chat->serviceID)[0]->serviceName;
          else :
            $chat->serviceName = "Customer Support";
          endif;
        endforeach;

        return $chats;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing chats', # code
          "an error occured while trying to get services - $th", # data
          ['status' => 400] # status
        );
      }
    }

    public function get_user_chat_list()
    {
      $chats = WorkServiceDB::get_user_chats(get_current_user_id());
      $chatList = $this->get_chat_list($chats);
      $response = new WP_REST_Response($chatList);
      $response->set_status(200);
      return $response;
    }

    // TODO => Get admin chat list

    public function set_user_chat_list($request)
    {
      if (!isset($request['chat_name'])) :
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for Sending Chat Endpoint', // data
          ['status' => 400] // status
        );
      endif;

      $chatName = $request['chat_name'];
      $serviceID = $request['service_id'];

      WorkServiceDB::set_chat(array(
        'serviceID' => $serviceID,
        'chatName' => $chatName,
      ));

      $response = new WP_REST_Response('Sent Successful');
      $response->set_status(200);
      return $response;
    }

    public function get_messages($request)
    {
      if (!isset($request['chat_id'])) :
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for Sending Message Endpoint', // data
          ['status' => 400] // status
        );
      endif;

      $messages = WorkServiceDB::get_messages($request['chat_id']);
      $response = new WP_REST_Response($messages);
      $response->set_status(200);
      return $response;
    }

    public function set_message($request)
    {
      if (!isset($request['type']) && !isset($request['chat_id'])) :
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for Sending Message Endpoint', // data
          ['status' => 400] // status
        );
      endif;

      $chatID = $request['chat_id'];
      $sender = 'customer';

      if (isset($request['sender'])) :
        $sender = sanitize_text_field($request['sender']);
      endif;

      $params = array('sender' => $sender, 'chatID' => $chatID);

      if ($request['type'] === 'message') {
        $params['messageText'] = sanitize_text_field($request['message_text']);
      }

      if ($request['type'] === 'expert') {
        $params['expertID'] = $request['expert_id'];
      }

      if ($request['type'] === 'payment') {
        $params['paymentLink'] = $request['payment_link'];
      }

      if ($request['type'] === 'rate') {
        $params['isRate'] = TRUE;
      }

      WorkServiceDB::set_messages($params);

      $response = new WP_REST_Response('Message Sent');
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
        $response = new WP_REST_Response(WorkServiceDB::get_user_requests());
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

      WorkServiceDB::delete_trusted_by($id);

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
    # ======

    # ====== Admin ======

    public function get_all_profiles()
    {
      try {
        $profiles = WorkServiceDB::get_all_profile();
        $response = new WP_REST_Response($profiles);
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        return new WP_Error(
          'error processing profiles', # code
          "an error occured while trying to get profiles", # data
          array('status' => 400) # status
        );
      }
    }
  }
  new WorkServiceDatabaseRestAPI;
endif;
