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
    $post_items = array();
    foreach ( $_POST as $key => $value){
      if (strpos($key, '-') != 0){
        $field_name = substr($key, 0, strpos($key, '-'));
        $field_subname = substr($key, strpos($key, '-')+1);
        $post_items[$field_name.'['.$field_subname.']'] = $value;
      }
      else{
        $post_items[$key] = $value;
      }
    }
    // FILES DATA
    $data = $field = undefined;
    foreach ($_FILES as $key => $value) {
      $field = key((array)$_FILES);
      $path = $_FILES[$key]['tmp_name'];
      $name = $_FILES[$key]['name'];
      $type = mime_content_type($path);
      $data = file_get_contents($path);
      $data = $name . ';' . base64_encode($data);
      $post_items[$field] = $data;
    }

    // GRAB SPECIFIC FORM ID
    $form_id = $_POST['form'];
    // DEFINE WHERE TO SEND THE POST REQUEST
    $post_url = 'https://www.formstack.com/api/v2/form/'.$form_id.'/submission.json';

    $curl_connection = curl_init();
    curl_setopt($curl_connection, CURLOPT_URL, $post_url);
    curl_setopt($curl_connection, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$oauth_token));
    curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_items);

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
