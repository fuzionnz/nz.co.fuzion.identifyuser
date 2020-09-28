<?php

require_once 'identifyuser.civix.php';
use CRM_Identifyuser_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function identifyuser_civicrm_config(&$config) {
  _identifyuser_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function identifyuser_civicrm_xmlMenu(&$files) {
  _identifyuser_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function identifyuser_civicrm_install() {
  _identifyuser_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function identifyuser_civicrm_postInstall() {
  _identifyuser_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function identifyuser_civicrm_uninstall() {
  _identifyuser_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function identifyuser_civicrm_enable() {
  _identifyuser_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function identifyuser_civicrm_disable() {
  _identifyuser_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function identifyuser_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _identifyuser_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function identifyuser_civicrm_managed(&$entities) {
  _identifyuser_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function identifyuser_civicrm_caseTypes(&$caseTypes) {
  _identifyuser_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function identifyuser_civicrm_angularModules(&$angularModules) {
  _identifyuser_civix_civicrm_angularModules($angularModules);
}

function identifyuser_civicrm_postProcess($formName, $form) {
  if (in_array($formName, ['CRM_Contribute_Form_ContributionPage_Settings', 'CRM_Event_Form_ManageEvent_Registration'])) {
    $enableLookup = Civi::settings()->get('enable_lookup');
    $setting = ($formName == 'CRM_Contribute_Form_ContributionPage_Settings') ? 'contribution' : 'event';
    $entityID = $form->getVar('_id') ?? NULL;

    if (!empty($entityID) && isset($form->_submitValues['enable_lookup'])) {
      $enableLookup["{$setting}_{$entityID}"] = $form->_submitValues['enable_lookup'];
      Civi::settings()->set('enable_lookup', $enableLookup);
    }
  }
}

function identifyuser_civicrm_buildForm($formName, &$form) {
  if (in_array($formName, ['CRM_Contribute_Form_ContributionPage_Settings', 'CRM_Event_Form_ManageEvent_Registration'])) {
    $enableLookup = Civi::settings()->get('enable_lookup');
    $setting = ($formName == 'CRM_Contribute_Form_ContributionPage_Settings') ? 'contribution' : 'event';
    $entityID = $form->getVar('_id') ?? NULL;

    if (!empty($entityID) && !empty($enableLookup["{$setting}_{$entityID}"])) {
      $defaults = ['enable_lookup' => $enableLookup["{$setting}_{$entityID}"]];
      $form->setDefaults($defaults);
    }
    $form->add('advcheckbox', 'enable_lookup', ts('Enable User Lookup on the page?'));
    $form->assign("formName", $formName);

    $templatePath = realpath(dirname(__FILE__)."/templates");
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "{$templatePath}/enable_lookup.tpl"
    ));
  }
  elseif (in_array($formName, ['CRM_Event_Form_Registration_Register', 'CRM_Contribute_Form_Contribution_Main'])) {
    $enableLookup = Civi::settings()->get('enable_lookup');
    $setting = ($formName == 'CRM_Contribute_Form_Contribution_Main') ? 'contribution' : 'event';
    if ($formName == 'CRM_Event_Form_Registration_Register') {
      $entityID = $form->_eventId;
    }
    elseif ($formName == 'CRM_Contribute_Form_Contribution_Main') {
      $entityID = $form->_id;
      $form->assign("page_id", $form->_id);
    }

    $contactID = $form->getContactID();
    if (empty($contactID) && !empty($enableLookup["{$setting}_{$entityID}"])) {
      $defaultIndivUnsup = civicrm_api3('RuleGroup', 'getsingle', [
        'contact_type' => "Individual",
        'used' => "Unsupervised",
      ])['id'];
      $dedupeRuleID = $defaultIndivUnsup;
      if ($formName == 'CRM_Event_Form_Registration_Register') {
        $form->assign("event_id", $form->_eventId);
        if (!empty($form->_values['event']['dedupe_rule_group_id'])) {
          $dedupeRuleID = $form->_values['event']['dedupe_rule_group_id'];
        }
      }
      elseif ($formName == 'CRM_Contribute_Form_Contribution_Main') {
        $form->assign("page_id", $form->_id);
      }

      $form->assign("rule_id", $dedupeRuleID);
      $templatePath = realpath(dirname(__FILE__)."/templates");
      CRM_Core_Region::instance('page-body')->add(array(
        'template' => "{$templatePath}/dedupe_form.tpl"
      ));
    }
  }
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function identifyuser_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _identifyuser_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function identifyuser_civicrm_entityTypes(&$entityTypes) {
  _identifyuser_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function identifyuser_civicrm_themes(&$themes) {
  _identifyuser_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function identifyuser_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function identifyuser_civicrm_navigationMenu(&$menu) {
  _identifyuser_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _identifyuser_civix_navigationMenu($menu);
} // */
