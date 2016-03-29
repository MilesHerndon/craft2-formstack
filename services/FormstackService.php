<?php

namespace Craft;

class FormstackService extends BaseApplicationComponent
{
  protected $oauthToken;
  protected $defaultForm;

  /**
   * Constructor
   */
  public function __construct()
  {
    $settings = craft()->plugins->getPlugin('formstack')->getSettings();

    $this->oauthToken = $settings->oauthToken;
    $this->defaultForm = $settings->defaultForm;
  }

  /**
   * Get forms from Formstack
   *
   * @return array Array of Formstack Forms
   */
  public function getForms($options = array())
  {
    $all_forms_url = 'https://www.formstack.com/api/v2/form.json?oauth_token='.$this->oauthToken;

    try{

      $all_forms_result = @file_get_contents($all_forms_url);
      if ($all_forms_result === false) {
        return "Your forms are not working at the moment.";
      }
      else{
        $all_forms_obj = json_decode($all_forms_result);

        return $all_forms_obj->forms;
      }

    } catch(\Exception $e) {
      return;
    }

  }

  // /**
  //  * Get specific form from Formstack
  //  *
  //  * @param $options: Formstack form id
  //  * @return array Formstack Form
  //  */
  public function getFormById($options)
  {

    $settings = craft()->plugins->getPlugin('formstack')->getSettings();

    $options = $options == null ? $settings->defaultForm : $options;

    $field_url = 'https://www.formstack.com/api/v2/form/'.$options.'/field.json?oauth_token=' . $settings->oauthToken;
    $field_result = file_get_contents($field_url);
    $field_obj = json_decode($field_result);

    $obj['field_result'] = $field_obj;

    return $obj;

  }

  public function getWholeFormById($options)
  {

    $settings = craft()->plugins->getPlugin('formstack')->getSettings();

    $options = $options == null ? $settings->defaultForm : $options;

    $field_url = 'https://www.formstack.com/api/v2/form/'.$options.'.json?oauth_token=' . $settings->oauthToken;
    $field_result = file_get_contents($field_url);
    $field_obj = json_decode($field_result);

    $obj['field_result'] = $field_obj;

    return $obj;

  }

}