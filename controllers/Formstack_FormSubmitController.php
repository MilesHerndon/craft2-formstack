<?php
namespace Craft;

class Formstack_FormSubmitController extends BaseController
{
  protected $allowAnonymous = true;
  public function actionFormstackSubmit()
  {

    // GET REFERER PAGE
    $url = $this->getActionParams()['p'];
    $url = stripslashes($url);

    // DONT ALLOW ACCESS TO THIS FILE UNLESS IT IS POSTED TO
    $this->requirePostRequest();
    // GET OAUTH FROM SETTINGS
    $oauth_token = craft()->plugins->getPlugin('formstack')->getSettings()->oauthToken;
    // REMOVE UNNECESSARY FIELDS FROM POST VARIABLE FOR SENDING TO FORMSTACK
    foreach ( $_POST as $key => $value){
      // if ($key != 'action' || $key != 'redirect' || $key != '_submit' || $key != 'viewkey') {
        if (strpos($key, '-') != 0){
          $field_name = substr($key, 0, strpos($key, '-'));
          $field_subname = substr($key, strpos($key, '-')+1);
          $post_items[] = $field_name.'['.$field_subname.']' . '=' . $value;
        }
        else{
          $post_items[] = $key . '=' . $value;
        }
      // }
    }
    $post_string = implode ('&', $post_items);

    // GRAB SPECIFIC FORM ID
    $form_id = $_POST['form'];
    // DEFINE WHERE TO SEND THE POST REQUEST
    $post_url = 'https://www.formstack.com/api/v2/form/'.$form_id.'/submission.json?oauth_token='.$oauth_token;

    $curl_connection = curl_init($post_url);
    curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl_connection, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_connection, CURLINFO_HEADER_OUT, true);

    $result = curl_exec($curl_connection);

    curl_close($curl_connection);

    $message = json_decode($result)->message;
    $message = str_replace(array('<p>','</p>'), '', $message);

    if(craft()->request->isAjaxRequest()){
      $this->returnJson($result);
    }
    else{
      $url = $url . '?message='.urlencode($message).'&submitted=true#newsletter-wrapper';
      $this->redirect($url);
    }

  }
}
?>