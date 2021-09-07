<?php

use CRM_Identifyuser_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Identifyuser_Form_VerifyOTP extends CRM_Core_Form {
  protected $_isAJAX = TRUE;


  public function buildQuickForm() {
    CRM_Utils_System::setTitle(ts('Verify OTP'));

    $this->ruleID = CRM_Utils_Request::retrieve('rule_id', 'Positive', $this);
    $this->eventID = CRM_Utils_Request::retrieve('event_id', 'Positive', $this);
    $this->pageID = CRM_Utils_Request::retrieve('page_id', 'Positive', $this);
    $this->contactID = CRM_Utils_Request::retrieve('contact_id', 'Positive', $this);
    $this->_isAJAX = TRUE;
    if (empty($_GET['snippet'])) {
      $this->_isAJAX = FALSE;
    }

    $this->add('text', 'otp', 'Enter OTP', NULL, TRUE);

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Verify'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    $this->addFormRule(['CRM_Identifyuser_Form_VerifyOTP', 'formRule']);
    parent::buildQuickForm();
  }

  /**
   * Global form rule.
   *
   * @param array $fields
   *   The input form values.
   *
   * @return bool|array
   *   true if no errors, else array of errors
   */
  public static function formRule($fields, $files, $self) {
    $errors = [];
    $validOTP = CRM_Identifyuser_Utils::verifyOTP($fields['otp'], $self->contactID);
    if (empty($validOTP)) {
      $errors['otp'] = ts('Invalid OTP');
    }
    return $errors;
  }


  public function postProcess() {
    $values = $this->exportValues();
    if (!empty($values['otp'])) {
      if (empty($this->_isAJAX)) {
        $setting = CRM_Identifyuser_Form_IdentifyUserSetting::getSetting();
        if ($setting['group']) {
          civicrm_api3('GroupContact', 'create', [
            'group_id' => $setting['group'],
            'contact_id' => $this->contactID,
          ]);
        }
        CRM_Core_Session::setStatus(ts($setting['success_message'] ?? 'OTP verified successfully.'), ts(''), 'success');
        return;
      }
      $url = CRM_Identifyuser_Utils::getChecksumURL($this->contactID, $this->eventID, $this->pageID);

      CRM_Core_Page_AJAX::returnJsonResponse([
        'checksum_url' => $url,
      ]);
    }
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
