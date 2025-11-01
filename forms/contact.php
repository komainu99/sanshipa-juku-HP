<?php
  /**
  * Requires the "PHP Email Form" library
  */

  // Replace contact@example.com with your real receiving email address
  $receiving_email_address = 'hidekazu419@gmail.com';

  if( file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php' )) {
    include( $php_email_form );
  } else {
    die( 'Unable to load the "PHP Email Form" Library!');
  }

  $contact = new PHP_Email_Form;
  $contact->ajax = true;
  
  $contact->to = $receiving_email_address;
  $contact->from_name = $_POST['last_name'] . ' ' . $_POST['first_name'];
  $contact->from_email = $_POST['email'];
  $contact->subject = '入塾テスト申し込み - ' . $_POST['last_name'] . ' ' . $_POST['first_name'];

  $contact->add_message( $_POST['last_name'], '姓');
  $contact->add_message( $_POST['first_name'], '名');
  $contact->add_message( $_POST['email'], 'メールアドレス');
  $contact->add_message( $_POST['phone'], '電話番号');
  $contact->add_message( $_POST['plan'], '希望プラン');
  $contact->add_message( $_POST['start_time'], '受講開始希望時期');
  $contact->add_message( $_POST['message'], 'メッセージ', 10);

  echo $contact->send();
?>
