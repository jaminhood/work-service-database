<?php

if (!defined('ABSPATH')) exit(); # No direct access allowed.

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if (!class_exists('WorkServiceDB')) :
  class WorkServiceDB
  {

    public function __construct()
    {
    }

    # ====== Tables ======
    private static function create_table($table, $stmt)
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_' . $table;
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name $stmt $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_categories_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_categories';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        categoryID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        categoryName VARCHAR(255) NOT NULL,
        categoryIcon INT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_sub_categories_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_sub_categories';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        subCategoryID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        categoryID INT NOT NULL,
        subCategoryName VARCHAR(255) NOT NULL,
        subCategoryIcon INT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_services_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_services';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        serviceID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        subCategoryID INT NOT NULL,
        serviceName VARCHAR(255) NOT NULL,
        serviceIcon INT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_address_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_address';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        addressID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        userID INT,
        streetAddress VARCHAR(255),
        city VARCHAR(50),
        state VARCHAR(50),
        country VARCHAR(50)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_requests_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_requests';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        requestsID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        userID INT NOT NULL,
        requestName VARCHAR(255) NOT NULL,
        requestDesc TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_expert_earnings_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_expert_earnings';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        expert_earnings_id INT NOT NULL AUTO_INCREMENT,
        user_id INT NOT NULL,
        expert_earnings_name TEXT NOT NULL,
        expert_earnings_desc TEXT NOT NULL,
        time_stamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (expert_earnings_id)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_profile_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_profile';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        profileID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        userID INT NOT NULL,
        profileImg INT NOT NULL DEFAULT 0,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_chat_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_chat';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        chatID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        serviceID INT NOT NULL DEFAULT 0,
        chatName VARCHAR(255) NOT NULL,
        isActive BOOLEAN NOT NULL DEFAULT TRUE,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_user_chat_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_user_chat';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        userID INT,
        chatID INT
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_messages_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_messages';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        messageID INT PRIMARY KEY AUTO_INCREMENT,
        sender TEXT,
        chatID INT,
        expertID INT,
        paymentLink TEXT,
        isRate BOOLEAN NOT NULL DEFAULT FALSE,
        messageText TEXT,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_bookings_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_bookings';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        bookingID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        userID INT,
        serviceID INT NOT NULL DEFAULT 0,
        bookingStatus INT NOT NULL DEFAULT 1,
        bookingPrice INT NOT NULL DEFAULT 0,
        bookingType TEXT NOT NULL,
        bookingDesc TEXT NOT NULL,
        bookingDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_ratings_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_ratings';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        ratingID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        userID INT,
        bookingID INT,
        ratingValue INT
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_reviews_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_reviews';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        ratingID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        userID INT,
        bookingID INT,
        reviewText TEXT,
        reviewDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_trusted_by_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_trusted_by';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        trustedId INT NOT NULL AUTO_INCREMENT,
        trustedImg INT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (trustedId)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_benefits_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_benefits';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        benefitID INT NOT NULL AUTO_INCREMENT,
        benefitHeading TEXT NOT NULL,
        benefitParagraph TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (benefitID)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_news_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_news';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        newsID INT NOT NULL AUTO_INCREMENT,
        newsImg INT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (newsID)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_about_us_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_about_us';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        aboutID INT NOT NULL AUTO_INCREMENT,
        aboutStory TEXT NOT NULL,
        expertise TEXT NOT NULL,
        convenience TEXT NOT NULL,
        trust TEXT NOT NULL,
        innovation TEXT NOT NULL,
        PRIMARY KEY (aboutID)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_team_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_team';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        teamID INT NOT NULL AUTO_INCREMENT,
        teamName TEXT NOT NULL,
        teamRole TEXT NOT NULL,
        teamImg INT NOT NULL,
        PRIMARY KEY (teamID)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_download_links_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_download_links';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        linkID INT NOT NULL AUTO_INCREMENT,
        linkIOS TEXT NOT NULL,
        linkAndroid TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (linkID)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_faqs_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_faqs';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        faqID INT NOT NULL AUTO_INCREMENT,
        faqQuestion TEXT NOT NULL,
        faqAnswer TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (faqID)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_faqs_submit_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_faqs_submit';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        faqSubmitID INT NOT NULL AUTO_INCREMENT,
        faqSubmitName TEXT NOT NULL,
        faqSubmitEmail TEXT NOT NULL,
        faqSubmitQuestion TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (faqSubmitID)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_contact_form_submit_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_contact_form_submit';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        contactFormSubmitID INT NOT NULL AUTO_INCREMENT,
        contactFormSubmitName TEXT NOT NULL,
        contactFormSubmitEmail TEXT NOT NULL,
        contactFormSubmitPhone TEXT NOT NULL,
        contactFormSubmitSubject TEXT NOT NULL,
        contactFormSubmitMessage TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (contactFormSubmitID)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_expert_orders_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_expert_orders';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        order_id INT NOT NULL AUTO_INCREMENT,
        expert_id INT NOT NULL,
        booking_id INT NOT NULL,
        order_accepted INT NOT NULL,
        order_completed INT NOT NULL,
        time_stamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (order_id)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_contact_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_contact';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        contactID INT NOT NULL AUTO_INCREMENT,
        contactMail TEXT,
        contactPhone TEXT,
        contactLocation TEXT,
        contactLocationLink TEXT,
        contactFacebook TEXT,
        contactFacebookLink TEXT,
        contactWhatsApp TEXT,
        contactWhatsAppLink TEXT,
        contactLinkedIn TEXT,
        contactLinkedInLink TEXT,
        contactInstagram TEXT,
        contactInstagramLink TEXT,
        PRIMARY KEY (contactID)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    private static function create_user_password_table()
    {
      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . 'ws_user_password';
      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        ID INT NOT NULL AUTO_INCREMENT,
        userID TEXT,
        userPass TEXT,
        PRIMARY KEY (ID)
      ) $charset_collate;";

      dbDelta($sql);
      add_option("jal_db_version", $jal_db_version);
    }

    public static function create_tables()
    {
      self::create_categories_table();
      self::create_sub_categories_table();
      self::create_services_table();
      self::create_address_table();
      self::create_requests_table();
      // self::create_expert_earnings_table();
      self::create_profile_table();
      self::create_chat_table();
      self::create_user_chat_table();
      self::create_messages_table();
      self::create_bookings_table();
      self::create_ratings_table();
      self::create_reviews_table();
      self::create_trusted_by_table();
      self::create_benefits_table();
      self::create_news_table();
      self::create_about_us_table();
      self::create_team_table();
      self::create_download_links_table();
      self::create_faqs_table();
      self::create_faqs_submit_table();
      self::create_contact_form_submit_table();
      // self::create_expert_orders_table();
      self::create_contact_table();
      self::create_user_password_table();
    }

    public static function delete_tables()
    {
      global $wpdb;

      $table_extensions = array(
        'ws_categories',
        'ws_sub_categories',
        'ws_services',
        'ws_address',
        'ws_requests',
        'ws_expert_earnings',
        'ws_profile',
        'ws_chat',
        'ws_user_chat',
        'ws_messages',
        'ws_bookings',
        'ws_ratings',
        'ws_reviews',
        'ws_trusted_by',
        'ws_benefits',
        'ws_news',
        'ws_about_us',
        'ws_team',
        'ws_download_links',
        'ws_faqs',
        'ws_faqs_submit',
        'ws_contact_form_submit',
        'ws_expert_orders',
        'ws_contact',
        'ws_user_password',
      );

      foreach ($table_extensions as $extension) {
        $table_name = $wpdb->prefix . $extension;
        $wpdb->query("DROP TABLE IF EXISTS " . $table_name);
      }
    }

    # ====== Accounts ======
    private static function create_new_account(array $user_data, string $phone)
    {
      $user_id = wp_insert_user($user_data);
      add_user_meta($user_id, 'phone_number', $phone);
      add_user_meta($user_id, 'account_balance', 0);
      return $user_id;
    }

    public static function setup_new_account(array $user_profile)
    {
      $first_name = $user_profile['first_name'];
      $last_name = $user_profile['last_name'];
      $email = $user_profile['email'];
      $phone = $user_profile['phone'];
      $password = $user_profile['password'];
      $username = $user_profile['username'];
      $role = $user_profile['role'];

      $user_data = [
        'user_pass'     => $password,
        'user_login'    => $username,
        'user_nicename' => $last_name,
        'user_email'    => $email,
        'display_name'  => $first_name,
        'last_name'     => $last_name,
        'role'          => $role
      ];

      $user_id = self::create_new_account($user_data, $phone);

      $email_content = [
        'email'     => $email,
        'title'     => "Customer Registration Successful",
        'name'      => $first_name,
        'user_msg'  => "",
        'admin_msg' => "",
      ];

      WorkServiceSettings::send_emails($email_content);

      return $user_id;
    }

    public static function delete_account($userID)
    {
      self::delete_user_password($userID);
      self::delete_user_profile($userID);
      self::delete_user_ratings($userID);
      self::delete_user_requests($userID);
      self::delete_user_address($userID);
      self::delete_sender_message($userID);

      delete_user_meta($userID, 'phone_number');
      delete_user_meta($userID, 'account_balance');
      wp_delete_user($userID);
    }

    public static function delete_accounts(string $role)
    {
      # Include the user file with the user administration API
      require_once(ABSPATH . 'wp-admin/includes/user.php');

      # Get a list of users that belongs to the specified role
      $users = get_users(['role' => [$role]]);

      # Delete all the user of the specified role
      foreach ($users as $user) :
        $customer_id = $user->ID;
        self::delete_account($customer_id);
      endforeach;
    }

    # ===== Getters
    public static function getter($table, $sql = "ORDER BY timestamp DESC")
    {
      global $wpdb;
      $table_name = $wpdb->prefix . $table;
      $results = $wpdb->get_results("SELECT * FROM $table_name $sql");
      return $results;
    }

    public static function get_categories()
    {
      return self::getter('ws_categories');
    }

    public static function get_category($id)
    {
      return self::getter('ws_categories', "WHERE categoryID=$id");
    }

    public static function get_sub_categories()
    {
      global $wpdb;
      $linked_table = $wpdb->prefix . 'ws_categories';
      return self::getter('ws_sub_categories', "AS A JOIN $linked_table AS B ON A.categoryID = B.categoryID ORDER BY A.timestamp DESC");
    }

    public static function get_sub_categories_of_category($id)
    {
      global $wpdb;
      $linked_table = $wpdb->prefix . 'ws_categories';
      return self::getter('ws_sub_categories', "AS A JOIN $linked_table AS B ON A.categoryID = B.categoryID WHERE A.categoryID=$id ORDER BY A.timestamp DESC");
    }

    public static function get_sub_category($id)
    {
      global $wpdb;
      $linked_table = $wpdb->prefix . 'ws_categories';
      return self::getter('ws_sub_categories', "AS A JOIN $linked_table AS B ON A.categoryID = B.categoryID WHERE A.subCategoryID=$id");
    }

    public static function get_services()
    {
      global $wpdb;
      $categories_table = $wpdb->prefix . 'ws_categories';
      $sub_categories_table = $wpdb->prefix . 'ws_sub_categories';
      return self::getter('ws_services', "AS A JOIN $sub_categories_table AS B ON A.subCategoryID = B.subCategoryID JOIN $categories_table AS C ON B.categoryID = C.categoryID ORDER BY A.timestamp DESC");
    }

    public static function get_services_by_sub_category($sub_category_id)
    {
      global $wpdb;
      $categories_table = $wpdb->prefix . 'ws_categories';
      $sub_categories_table = $wpdb->prefix . 'ws_sub_categories';
      return self::getter('ws_services', "AS A JOIN $sub_categories_table AS B ON A.subCategoryID = B.subCategoryID JOIN $categories_table AS C ON B.categoryID = C.categoryID WHERE A.subCategoryID=$sub_category_id ORDER BY A.timestamp DESC");
    }

    public static function get_service($id)
    {
      global $wpdb;
      $categories_table = $wpdb->prefix . 'ws_categories';
      $sub_categories_table = $wpdb->prefix . 'ws_sub_categories';
      return self::getter('ws_services', "AS A JOIN $sub_categories_table AS B ON A.subCategoryID = B.subCategoryID JOIN $categories_table AS C ON B.categoryID = C.categoryID WHERE A.serviceID=$id");
    }

    public static function get_address()
    {
      global $wpdb;
      $user_table_name = $wpdb->prefix . 'users';
      return self::getter('ws_address', "AS A JOIN $user_table_name AS B ON A.userID = B.ID");
    }

    private static function userGetter($table, $userID)
    {
      global $wpdb;
      $user_table_name = $wpdb->prefix . 'users';
      return self::getter($table, "AS A JOIN $user_table_name AS B ON A.userID = B.ID WHERE A.userID=$userID");
    }

    public static function get_user_address()
    {
      return self::userGetter('ws_address', get_current_user_id());
    }

    public static function get_user_requests()
    {
      return self::userGetter('ws_requests', get_current_user_id());
    }

    public static function get_all_profile()
    {
      global $wpdb;
      $user_table_name = $wpdb->prefix . 'users';
      return self::getter('ws_profile', "AS A JOIN $user_table_name AS B ON A.userID = B.ID ORDER BY A.timestamp DESC");
    }

    public static function get_user_profile()
    {
      return self::userGetter('ws_profile', get_current_user_id());
    }

    public static function get_single_profile($userID)
    {
      return self::userGetter('ws_profile', $userID);
    }

    private static function get_customers()
    {
      $args = ['role' => 'customers'];
      $wp_user_query = new WP_User_Query($args);
      $customers = $wp_user_query->get_results();
      return $customers;
    }

    private static function get_single_customer(int $id)
    {
      $user = array();

      return $user;
    }

    private static function get_experts()
    {
      $args = ['role' => 'experts'];
      $wp_user_query = new WP_User_Query($args);
      $experts = $wp_user_query->get_results();
      return $experts;
    }

    private static function get_single_expert(int $id)
    {
      $user = array();

      return $user;
    }

    private static function get_user($id)
    {
      return get_user_by('ID', $id);
    }

    public static function get_customer_bookings($customer_id)
    {
      global $wpdb;
      $service_table = $wpdb->prefix . 'ws_services';
      $ratings_table = $wpdb->prefix . 'ws_ratings';
      $reviews_table = $wpdb->prefix . 'ws_reviews';
      return self::getter('ws_bookings', "AS A JOIN $service_table AS B ON A.serviceID = B.serviceID JOIN $reviews_table AS C ON A.bookingID = C.bookingID JOIN $ratings_table AS D ON A.bookingID = D.bookingID WHERE A.userID=$customer_id ORDER BY A.bookingDate DESC");
    }

    public static function get_bookings()
    {
      global $wpdb;
      $service_table = $wpdb->prefix . 'ws_services';
      $ratings_table = $wpdb->prefix . 'ws_ratings';
      $reviews_table = $wpdb->prefix . 'ws_reviews';
      return self::getter('ws_bookings', "AS A JOIN $service_table AS B ON A.serviceID = B.serviceID JOIN $reviews_table AS C ON A.bookingID = C.bookingID JOIN $ratings_table AS D ON A.bookingID = D.bookingID ORDER BY A.bookingDate DESC");
    }

    public static function get_news()
    {
      return self::getter('ws_news');
    }

    public static function get_about()
    {
      return self::getter('ws_about_us', "ORDER BY aboutID DESC");
    }

    public static function get_reason()
    {
      return self::getter('ws_benefits');
    }

    public static function get_team()
    {
      return self::getter('ws_team', "ORDER BY teamID DESC");
    }

    public static function get_chat()
    {
      return self::getter('ws_chat');
    }

    public static function get_user_chats($id)
    {
      $userChat =  self::getter('ws_user_chat', "WHERE userID=$id");
      $result = array();
      foreach ($userChat as $user) :
        $chatID = $user->chatID;
        $result[] = self::getter('ws_chat', "WHERE chatID=$chatID ORDER BY timestamp DESC")[0];
      endforeach;
      return $result;
    }

    public static function get_messages($chatID)
    {
      return self::getter('ws_messages', "WHERE chatID=$chatID");
    }

    public static function get_user_password()
    {
      $userID = get_current_user_id();
      return self::getter('ws_user_password', "WHERE userID=$userID")[0];
    }

    public static function get_trust()
    {
      return self::getter('ws_trusted_by');
    }

    public static function get_download_links()
    {
      return self::getter('ws_download_links');
    }

    public static function get_contact()
    {
      return self::getter('ws_contact', '');
    }

    public static function get_contact_form_submit()
    {
      return self::getter('ws_contact_form_submit');
    }

    public static function get_faqs()
    {
      return self::getter('ws_faqs');
    }

    public static function get_faqs_form()
    {
      return self::getter('ws_faqs_submit');
    }

    # ====== Setters
    private static function setter($table, $data)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . $table;
      $wpdb->insert($table_name, $data);
    }

    public static function set_category($data)
    {
      self::setter('ws_categories', $data);
    }

    public static function set_sub_category($data)
    {
      self::setter('ws_sub_categories', $data);
    }

    public static function set_service($data)
    {
      self::setter('ws_services', $data);
    }

    public static function set_user_address($data)
    {
      self::setter('ws_address', $data);
    }

    public static function set_requests($data)
    {
      self::setter('ws_requests', $data);
    }

    public static function set_user_profile($data)
    {
      self::setter('ws_profile', $data);
    }

    public static function set_chat($data)
    {
      self::setter('ws_chat', $data);
      $chat = self::getter('ws_chat');

      $params = array(
        'userID' => get_current_user_id(),
        'chatID' => $chat[0]->chatID
      );
      self::setter('ws_user_chat', $params);
    }

    public static function set_messages($data)
    {
      self::setter('ws_messages', $data);
    }

    public static function set_bookings($data)
    {
      $table = 'ws_bookings';
      self::setter($table, $data);
      return self::getter($table, "ORDER BY bookingDate DESC")[0];
    }

    public static function set_ratings($data)
    {
      self::setter('ws_ratings', $data);
    }

    public static function set_reviews($data)
    {
      self::setter('ws_reviews', $data);
    }

    public static function set_trust($data)
    {
      self::setter('ws_trusted_by', $data);
    }

    public static function set_benefits($data)
    {
      self::setter('ws_benefits', $data);
    }

    public static function set_news($data)
    {
      self::setter('ws_news', $data);
    }

    public static function set_about_us($data)
    {
      self::setter('ws_about_us', $data);
    }

    public static function set_team($data)
    {
      self::setter('ws_team', $data);
    }

    public static function set_download_links($data)
    {
      self::setter('ws_download_links', $data);
    }

    public static function set_faqs($data)
    {
      self::setter('ws_faqs', $data);
    }

    public static function set_faqs_submit($data)
    {
      self::setter('ws_faqs_submit', $data);
    }

    public static function set_contact_form_submit($data)
    {
      self::setter('ws_contact_form_submit', $data);
    }

    public static function set_contact($data)
    {
      self::setter('ws_contact', $data);
    }

    public static function set_reason(array $data)
    {
      self::setter('ws_benefits', $data);
    }

    public static function set_user_password(array $data)
    {
      self::setter('ws_user_password', $data);
    }

    # ====== Updaters
    private static function updater($table, $data, $key, $val)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . $table;
      $wpdb->update($table_name, $data, array($key => $val));
    }

    public static function update_category($data, $id)
    {
      self::updater('ws_categories', $data, 'categoryID', $id);
    }

    public static function update_sub_category($data,  $id)
    {
      self::updater('ws_sub_categories', $data, 'subCategoryID', $id);
    }

    public static function update_service($data,  $id)
    {
      self::updater('ws_services', $data, 'serviceID', $id);
    }

    public static function update_user_address($data,  $id)
    {
      self::updater('ws_address', $data, 'userID', $id);
    }

    public static function update_user_requests($data,  $id)
    {
      self::updater('ws_requests', $data, 'userID', $id);
    }

    public static function update_user_profile($data,  $id)
    {
      self::updater('ws_profile', $data, 'userID', $id);
    }

    public static function update_chat($id)
    {
      $data = array('isActive' => FALSE);
      self::updater('ws_chat', $data, 'chatID', $id);
    }

    public static function update_message($data,  $id)
    {
      self::updater('ws_messages', $data, 'messageID', $id);
    }

    public static function update_user_bookings($data,  $id)
    {
      self::updater('ws_bookings', $data, 'userID', $id);
    }

    public static function update_booking($data,  $id)
    {
      self::updater('ws_bookings', $data, 'bookingID', $id);
    }

    public static function update_booking_ratings($data,  $id)
    {
      self::updater('ws_ratings', $data, 'bookingID', $id);
    }

    public static function update_booking_reviews($data,  $id)
    {
      self::updater('ws_reviews', $data, 'bookingID', $id);
    }

    public static function update_trusted_by($data,  $id)
    {
      self::updater('ws_trusted_by', $data, 'trustedId', $id);
    }

    public static function update_benefits($data,  $id)
    {
      self::updater('ws_benefits', $data, 'benefitID', $id);
    }

    public static function update_news($data,  $id)
    {
      self::updater('ws_news', $data, 'newsID', $id);
    }

    public static function update_about_us($data,  $id)
    {
      self::updater('ws_about_us', $data, 'aboutID', $id);
    }

    public static function update_team($data,  $id)
    {
      self::updater('ws_team', $data, 'teamID', $id);
    }

    public static function update_download_links($data,  $id)
    {
      self::updater('ws_download_links', $data, 'linkID', $id);
    }

    public static function update_faqs($data,  $id)
    {
      self::updater('ws_faqs', $data, 'faqID', $id);
    }

    public static function update_faqs_submit($data,  $id)
    {
      self::updater('ws_faqs_submit', $data, 'faqSubmitID', $id);
    }

    public static function update_contact_form_submit($data,  $id)
    {
      self::updater('ws_contact_form_submit', $data, 'contactFormSubmitID', $id);
    }

    public static function update_contact($data,  $id)
    {
      self::updater('ws_contact', $data, 'contactID', $id);
    }

    public static function update_user_password($data,  $id)
    {
      self::updater('ws_user_password', $data, 'userID', $id);
    }

    # ====== Deleters
    private static function deleter($table, $key, $val)
    {
      global $wpdb;
      $table_name = $wpdb->prefix . $table;
      $wpdb->query("DELETE FROM $table_name WHERE $key='$val'");
    }

    public static function delete_category($id)
    {
      self::deleter('ws_categories', 'categoryID', $id);
    }

    public static function delete_sub_category($id)
    {
      self::deleter('ws_sub_categories', 'subCategoryID', $id);
    }

    public static function delete_service($id)
    {
      self::deleter('ws_services', 'serviceID', $id);
    }

    public static function delete_user_address($id)
    {
      self::deleter('ws_address', 'userID', $id);
    }

    public static function delete_user_requests($id)
    {
      self::deleter('ws_requests', 'userID', $id);
    }

    public static function delete_user_profile($id)
    {
      self::deleter('ws_profile', 'userID', $id);
    }

    public static function delete_service_chat($id)
    {
      self::deleter('ws_chat', 'serviceID', $id);
    }

    public static function delete_sender_message($id)
    {
      self::deleter('ws_messages', 'senderID', $id);
    }

    public static function delete_message($id)
    {
      self::deleter('ws_messages', 'messageID', $id);
    }

    public static function delete_chat_messages($id)
    {
      self::deleter('ws_messages', 'chatID', $id);
    }

    public static function delete_user_bookings($id)
    {
      self::deleter('ws_bookings', 'userID', $id);
    }

    public static function delete_user_ratings($id)
    {
      self::deleter('ws_ratings', 'userID', $id);
    }

    public static function delete_user_reviews($id)
    {
      self::deleter('ws_reviews', 'userID', $id);
    }

    public static function delete_trusted_by($id)
    {
      self::deleter('ws_trusted_by', 'trustedId', $id);
    }

    public static function delete_benefits($id)
    {
      self::deleter('ws_benefits', 'benefitID', $id);
    }

    public static function delete_news($id)
    {
      self::deleter('ws_news', 'newsID', $id);
    }

    public static function delete_about_us($id)
    {
      self::deleter('ws_about_us', 'aboutID', $id);
    }

    public static function delete_team($id)
    {
      self::deleter('ws_team', 'teamID', $id);
    }

    public static function delete_download_link($id)
    {
      self::deleter('ws_download_links', 'linkID', $id);
    }

    public static function delete_faqs($id)
    {
      self::deleter('ws_faqs', 'faqID', $id);
    }

    public static function delete_faqs_submit($id)
    {
      self::deleter('ws_faqs_submit', 'faqSubmitID', $id);
    }

    public static function delete_contact_form_submit($id)
    {
      self::deleter('ws_contact_form_submit', 'contactFormSubmitID', $id);
    }

    public static function delete_contact($id)
    {
      self::deleter('ws_contact', 'contactID', $id);
    }

    public static function delete_reason(int $id)
    {
      self::deleter('ws_benefits', 'benefitID', $id);
    }

    public static function delete_user_password($id)
    {
      self::deleter('ws_user_password', 'userID', $id);
    }

    # ====== Sub-Categories ======

    // public static function get_single_user_bookings(int $user_id)
    // {
    //   global $wpdb;
    //   $table_name = $wpdb->prefix . 'ws_bookings';
    //   $results = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id=$user_id");
    //   return $results;
    // }

    // public static function get_booking_for_user(int $user_id, int $id = 0)
    // {
    //   global $wpdb;
    //   $table_name = $wpdb->prefix . 'ws_bookings';
    //   $results = $wpdb->get_results("SELECT * FROM $table_name WHERE chat_user_id=$user_id AND id=$id");
    //   return $results;
    // }

    // public static function update_bookings(array $data, int $id)
    // {
    //   global $wpdb;
    //   $table_name = $wpdb->prefix . 'ws_bookings';
    //   $wpdb->update($table_name, $data, array('booking_id' => $id));
    // }

    // public static function delete_booking(int $id)
    // {
    //   global $wpdb;
    //   $table_name = $wpdb->prefix . 'ws_bookings';
    //   $wpdb->query("DELETE FROM $table_name WHERE service_id='$id'");
    // }

    # ======


    # ======


    // public static function delete_trust(int $id)
    // {
    //   global $wpdb;
    //   $table_name = $wpdb->prefix . 'ws_trusted_by';
    //   $wpdb->query("DELETE FROM $table_name WHERE trusted_id='$id'");
    // }

    # ======

    // public static function set_about(array $data)
    // {
    //   global $wpdb;
    //   $table_name = $wpdb->prefix . 'ws_about_us';
    //   $wpdb->insert($table_name, $data);
    // }

    # ======

    # ======
  }
endif;
