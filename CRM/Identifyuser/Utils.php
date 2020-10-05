<?php

use CRM_Identifyuser_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Identifyuser_Utils {

  /**
   * Verify OTP received by the contact.
   */
  public static function verifyOTP($otp, $contactID) {
    $smsActivity = civicrm_api3('Activity', 'get', [
      'sequential' => 1,
      'target_contact_id' => $contactID,
      'activity_type_id' => "SMS",
      'activity_date_time' => ['>=' => "today"],
      'options' => ['sort' => "id DESC ", 'limit' => 1],
    ]);
    if (!empty($smsActivity['count'])) {
      if (strpos($smsActivity['values'][0]['details'], $otp) !== false) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Send OTP to the contact.
   */
  public static function sendOTP($contactID, $phone) {
    $contactIds[] = $contactID;
    $params = [
      'sms_text_message' => 'Your OTP is ' . mt_rand(100000, 999999),
    ];
    // use the default SMS provider
    $providers = CRM_SMS_BAO_Provider::getProviders(NULL, array('is_default' => 1));
    if (empty($providers)) {
      throw new CRM_Core_Exception('No SMS providers found - Cannot send SMS. Please enable at least one!');
    }
    $provider = current($providers);
    $provider['provider_id'] = $provider['id'];
    $contactDetails = [];

    CRM_Activity_BAO_Activity::sendSMS($contactDetails, $params, $provider, $contactIds, $contactID);
  }

  /**
   * Send checksum link for the contribution page.
   */
  public static function sendChecksumLinkToContact($contactID, $toEmail, $eventID = NULL, $pageID = NULL) {
    $urlWithChecksum = self::getChecksumURL($contactID, $eventID, $pageID);
    if (!empty($toEmail) && !empty($urlWithChecksum)) {
      $mailParams = [
        'from' => CRM_Core_BAO_Domain::getNoReplyEmailAddress(),
        'toName' => 'Test',
        'toEmail' => $toEmail,
        'subject' => 'Event Form',
      ];
      $mailParams['html'] = "Click here " . $urlWithChecksum;
      $result = CRM_Utils_Mail::send($mailParams);
      if (!$result || is_a($result, 'PEAR_Error')) {
        CRM_Core_Session::setStatus(ts("Failed to send email to the user address."), ts(''), 'error');
        return ['email_fail' => 'Failed to send message'];
      }
      CRM_Core_Session::setStatus(ts("Email with the checksum link has been sent to %1.", [1 => $toEmail]), ts(''), 'success');
    }
    else {
      CRM_Core_Session::setStatus(ts("No email found for the contact."), ts(''), 'error');
    }
  }

  /**
   * Get checksum URL for event or contirbution page
   */
  public static function getChecksumURL($contactID, $eventID = NULL, $pageID = NULL) {
    $checkSum = CRM_Contact_BAO_Contact_Utils::generateChecksum($contactID);
    if (!empty($eventID)) {
      return CRM_Utils_System::url('civicrm/event/register', "reset=1&id={$eventID}&cid={$contactID}&cs={$checkSum}", TRUE);
    }
    else {
      return CRM_Utils_System::url('civicrm/contribute/transact', "reset=1&id={$pageID}&cid={$contactID}&cs={$checkSum}", TRUE);
    }
  }

}
