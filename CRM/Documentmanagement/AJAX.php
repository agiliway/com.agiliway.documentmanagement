<?php

/**
 * This class contains all contact related functions that are called using AJAX
 * (jQuery)
 */
class CRM_Documentmanagement_AJAX {

  /**
   * Gets documents JSON by params
   */
  public static function getDocuments() {
    $documents = CRM_Documentmanagement_BAO_Document::getRows([
      'contact_id' => CRM_Utils_Request::retrieve('contact_id', 'Int'),
      'from_date' => CRM_Utils_Request::retrieve('from_date', 'String'),
      'to_date' => CRM_Utils_Request::retrieve('to_date', 'String'),
      'title' => CRM_Utils_Request::retrieve('title', 'String'),
      'type_id' => CRM_Utils_Request::retrieve('type_id', 'Int'),
      'campaign_id' => CRM_Utils_Request::retrieve('campaign_id', 'Int'),
    ]);

    $returnArray = [];
    $returnArray['data'] = $documents;

    $countDocuments = count($documents);

    $returnArray['recordsTotal'] = $countDocuments;
    $returnArray['recordsFiltered'] = $countDocuments;

    CRM_Utils_JSON::output($returnArray);
  }

  /**
   * Gets contacts IDs by groups
   */
  public static function getContactsIdsByGroups() {
    $groups = CRM_Utils_Request::retrieve('groups', 'String');
    $contactsIds = [];

    foreach ($groups as $groupId) {
      $groupContactsData = civicrm_api3('GroupContact', 'get', [
        'sequential' => 1,
        'group_id' => $groupId,
        'contact_id.contact_type' => 'Individual',
      ]);

      if ($groupContactsData['count'] > 0) {
        foreach ($groupContactsData['values'] as $k => $v) {
          $contactsIds[] = $v['contact_id'];
        }
      }
    }

    if (!empty($contactsIds)) {
      $contactsIds = array_unique($contactsIds);
      CRM_Utils_JSON::output(array_values($contactsIds));
    }
    else {
      CRM_Utils_JSON::output(FALSE);
    }
  }

}
