<?php

use CRM_Identifyuser_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Identifyuser_Form_DedupeIdentifier extends CRM_Core_Form {
  public function buildQuickForm() {
    $this->ruleID = CRM_Utils_Request::retrieve('rule_id', 'Positive', $this);
    $this->eventID = CRM_Utils_Request::retrieve('event_id', 'Positive', $this);
    $this->pageID = CRM_Utils_Request::retrieve('page_id', 'Positive', $this);

    $contactFields = civicrm_api3('Contact', 'getfields', [
      'api_action' => "get",
    ])['values'];

    $ruleFields = civicrm_api3('Rule', 'get', [
      'sequential' => 1,
      'return' => ["rule_field"],
      'dedupe_rule_group_id' => $this->ruleID,
    ]);
    if (empty($ruleFields['count'])) {
      return;
    }
    $replaceFields = [
      'phone_numeric' => 'phone',
    ];
    foreach ($ruleFields['values'] as $fields) {
      if (!empty($replaceFields[$fields['rule_field']])) {
        $fields['rule_field'] = $replaceFields[$fields['rule_field']];
      }
      $field = $contactFields[$fields['rule_field']] ?? NULL;
      if (!empty($field)) {
        //Add some keys to avoid notice error.
        $keys = ['attributes', 'rule', 'is_view', 'is_required', 'field_type'];
        foreach ($keys as $key) {
          $field[$key] = $field[$key] ?? NULL;
        }
        CRM_Core_BAO_UFGroup::buildProfile($this, $field, NULL);
      }
    }

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
    $ruleGroup = civicrm_api3('RuleGroup', 'getsingle', [
      'id' => $this->ruleID,
    ]);
    $contactID = CRM_Contact_BAO_Contact::getFirstDuplicateContact($values, 'Individual', $ruleGroup['used'], [], FALSE, $this->ruleID);
    if (!empty($contactID)) {
      if (!empty($values['phone'])) {
        CRM_Identifyuser_Utils::sendOTP($contactID, $values['phone']);
        CRM_Core_Page_AJAX::returnJsonResponse([
          'contact_id' => $contactID,
          'otp_sent' => TRUE
        ]);
      }
      else {
        $toEmail = !empty($values['email']) ? $values['email'] : CRM_Contact_BAO_Contact::getPrimaryEmail($contactID);
        CRM_Identifyuser_Utils::sendChecksumLinkToContact($contactID, $toEmail, $this->eventID, $this->pageID);
      }
    }
    else {
      CRM_Core_Session::setStatus(ts("No User Found with these details. Please fill the complete form."), ts(''), 'success');
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
