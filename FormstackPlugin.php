<?php
namespace Craft;

class FormstackPlugin extends BasePlugin
{
    public function getName()
    {
         return Craft::t('Formstack');
    }

    public function getVersion()
    {
        return '1.0.0';
    }

    public function getDeveloper()
    {
        return 'MilesHerndon';
    }

    public function getDeveloperUrl()
    {
        return 'http://milesherndon.com';
    }

    protected function defineSettings()
    {
        return array(
            'oauthToken' => array(AttributeType::String, 'required' => true, 'label' => 'OAuth Token'),
            'defaultForm' => array(AttributeType::String, 'required' => true, 'label' => 'Default Form'),
        );
    }

    public function getSettingsHtml()
    {
        $settings = craft()->plugins->getPlugin('formstack')->getSettings();
        $forms_options = [];
        $all_forms_url = 'https://www.formstack.com/api/v2/form.json?oauth_token='.$settings->oauthToken;

        $all_forms_data = @file_get_contents($all_forms_url);
        if($all_forms_data === false){

        }
        else{
            $all_forms_obj = json_decode($all_forms_data);

            foreach($all_forms_obj->forms as $form){
                $forms_options[] = array(
                  'label' => $form->name,
                  'value' => $form->id
                );
            }
        }

        return craft()->templates->render('formstack/_settings', array(
            'settings' => $this->getSettings(),
            'formsoptions' => $forms_options
        ));
   }
}
