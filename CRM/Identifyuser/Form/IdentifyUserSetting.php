<?php

use CRM_Identifyuser_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Identifyuser_Form_IdentifyUserSetting extends CRM_Core_Form {


  public static function getSetting() {
    $settingValues = Civi::settings()->get('identify_user_settings');
    if (!empty($settingValues)) {
      $settingValues = unserialize($settingValues);
    }
    return $settingValues;
  }

  public function setDefaultValues() {
    return self::getSetting();
  }

  public function buildQuickForm() {

    // add form elements
    $this->add('checkbox', 'enable_recaptcha', 'Enable ReCAPTCHA on the dedupe form?');
    $groups = ['' => ts('- select -')] + CRM_Core_PseudoConstant::nestedGroup();
    $this->addElement('select', 'group', ts('Add contact to group'), $groups, array(
      'class' => 'crm-select2',
    ));

    $this->add('textarea', 'success_message', 'Message to be shown on successful verification?');


    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    $setting = [
      'enable_recaptcha' => $values['group'] ?? 0,
      'group' => $values['group'] ?? NULL,
      'success_message' => $values['success_message'] ?? NULL,
    ];
    Civi::settings()->set('identify_user_settings', serialize($setting));
    CRM_Core_Session::setStatus(ts("Identify user setting is saved."), ts('Saved'), 'success');

    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
