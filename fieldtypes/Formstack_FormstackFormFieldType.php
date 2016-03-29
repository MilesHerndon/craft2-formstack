<?php

namespace Craft;

class Formstack_FormstackFormFieldType extends BaseFieldType
{
  public function getName()
  {
    return Craft::t('Formstack Form');
  }

  /**
   * Get forms from Formstack
   *
   * @return array Array of Formstack Forms
   */
  public function getInputHtml($name, $value)
  {
    $forms = craft()->formstack->getForms();
    $settings = craft()->plugins->getPlugin('formstack')->getSettings();
    $default = $settings->defaultForm;
    foreach ($forms as $form) {
      $options[] = array(
              'label' => $form->name,
              'value' => $form->id
            );
    }

    return craft()->templates->render('formstack/_select', array(
      'name' => $name,
      'value' => $value,
      'options' => $options,
      'default' => $default
    ));
  }
}