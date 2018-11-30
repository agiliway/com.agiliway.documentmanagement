<?php

require_once 'documentmanagement.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function documentmanagement_civicrm_config(&$config) {
  _documentmanagement_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function documentmanagement_civicrm_xmlMenu(&$files) {
  _documentmanagement_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function documentmanagement_civicrm_install() {
  _documentmanagement_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function documentmanagement_civicrm_postInstall() {
  _documentmanagement_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function documentmanagement_civicrm_uninstall() {
  _documentmanagement_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function documentmanagement_civicrm_enable() {
  _documentmanagement_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function documentmanagement_civicrm_disable() {
  _documentmanagement_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function documentmanagement_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _documentmanagement_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function documentmanagement_civicrm_managed(&$entities) {
  _documentmanagement_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function documentmanagement_civicrm_caseTypes(&$caseTypes) {
  _documentmanagement_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function documentmanagement_civicrm_angularModules(&$angularModules) {
  _documentmanagement_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function documentmanagement_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _documentmanagement_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Add new type of permission
 */
function documentmanagement_civicrm_permission(&$permissions) {
  $prefix = ts('CiviCRM') . ': ';
  $permissions += [
    'create_document' => $prefix . ts('Create document'),
    'view_own_documents' => $prefix . ts('View own documents'),
    'view_all_documents' => $prefix . ts('View all documents'),
    'edit_own_documents' => $prefix . ts('Edit own documents'),
    'edit_all_documents' => $prefix . ts('Edit all documents'),
    'delete_own_documents' => $prefix . ts('Delete own documents'),
    'delete_all_documents' => $prefix . ts('Delete all documents'),
  ];
}

/**
 * Implementation of hook_civicrm_tabs
 *
 * @param $allTabs
 * @param null $contactID
 */
function documentmanagement_civicrm_tabs(&$allTabs, $contactID = NULL) {
  if ($contactID && CRM_Documentmanagement_BAO_Document::viewAccess()) {
    $allTabs[] = [
      'title' => ts('Documents'),
      'id' => 'document',
      'url' => CRM_Utils_System::url('civicrm/documents/documents-contact', "cid=$contactID"),
      'weight' => 150,
      'count' => CRM_Documentmanagement_BAO_Document::getCount(['contact_id' => $contactID]),
    ];
  }
}

/**
 * Check if user is a director of organization
 *
 * @param int $userID
 * @param int $organizationID
 *
 * @return bool
 */
function documentmanagement_check_organization_director($userID, $organizationID) {
  $relationships = new CRM_Contact_DAO_Relationship();

  $relationships->contact_id_a = $userID;
  $relationships->contact_id_b = $organizationID;

  if ($relationships->find(TRUE)) {
    $relationshipType = new CRM_Contact_DAO_RelationshipType();
    $relationshipType->id = $relationships->relationship_type_id;

    if ($relationshipType->find(TRUE)) {
      if ($relationshipType->is_permission_a_b == 1) {
        return TRUE;
      }
    }
  }

  return FALSE;
}

/**
 * Implements hook_civicrm_pageRun().
 *
 * @param $page
 */
function documentmanagement_civicrm_pageRun(&$page) {
  $pageName = $page->getVar('_name');

  if ($pageName == 'CRM_Event_Page_EventInfo') {
    $attachments = CRM_Core_BAO_File::getEntityFile("civicrm_event", $page->get('id'));
    if (!empty($attachments)) {
      $page->assign('attachments', $attachments);
      $page->assign('ShowAttachments', TRUE);
      CRM_Core_Region::instance('page-body')->add([
        'template' => CRM_Documentmanagement_ExtensionUtil::path() . '/templates/CRM/Documentmanagement/Page/Attachment.tpl',
      ]);
    }
  }
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * Sets fields and a default value for attachment.
 *
 * @param $formName
 * @param $form
 */
function documentmanagement_civicrm_buildForm($formName, &$form) {

  $class = [
    'CRM_Campaign_Form_Campaign' => 'CRM_Documentmanagement_Hook_buildForm_Campaign',
    'CRM_Event_Form_ManageEvent_EventInfo' => 'CRM_Documentmanagement_Hook_buildForm_Event',
  ];

  if (array_key_exists($formName, $class)) {

    $object = new $class[$formName]($form);
    if (method_exists($object, 'run')) {
      $object->run();
    }
  }
}

/**
 * Implements hook_civicrm_postProcess().
 *
 * Adds files for entities.
 *
 * @param $formName
 * @param $form
 */
function documentmanagement_civicrm_postProcess($formName, &$form) {

  $class = [
    'CRM_Campaign_Form_Campaign' => 'CRM_Documentmanagement_Hook_postProcess_Campaign',
    'CRM_Event_Form_ManageEvent_EventInfo' => 'CRM_Documentmanagement_Hook_postProcess_Event',
  ];

  if (array_key_exists($formName, $class)) {
    $object = new $class[$formName]($form);
    if (method_exists($object, 'run')) {
      $object->run();
    }
  }

  return;
}

/**
 * Implements hook_civicrm_post().
 *
 * @param string $op
 * @param string $objectName
 * @param int $objectId
 * @param object $objectRef
 */
function documentmanagement_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($op == 'create' && $objectName == 'Campaign') {
    CRM_Documentmanagement_Hook_postProcess_Campaign::$lastIDs[] = $objectId;
  }
  if ($op == 'create' && $objectName == 'Event') {
    CRM_Documentmanagement_Hook_postProcess_Event::$lastIDs[] = $objectId;
  }
}