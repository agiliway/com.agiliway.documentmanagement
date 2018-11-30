<?php

use CRM_Documentmanagement_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Documentmanagement_Form_Document extends CRM_Core_Form {

  /**
   * Information of updates/deletes document.
   *
   * @var array
   */
  private $document;

  /**
   * Documents categories.
   *
   * @var array
   */
  private $categories = [];

  /**
   * Documents types.
   *
   * @var array
   */
  private $types = [];

  /**
   * Campaigns.
   *
   * @var array
   */
  private $campaigns = [];

  /**
   * Classes extending CRM_Core_Form should implement this method.
   *
   * @throws Exception
   */
  public function getDefaultEntity() {
    return 'Document';
  }

  /**
   * This virtual function is used to build the form.
   *
   * @throws   HTML_QuickForm_Error
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    $this->applyFilter('__ALL__', 'trim');

    switch ($this->_action) {
      case CRM_Core_Action::ADD:
        $this->buildCreateAndUpdateFields();

        #groups of contacts
        $groups = CRM_Contact_BAO_Group::getGroups();

        if (!empty($groups)) {
          $groups_options = [];

          foreach ($groups as $k => $v) {
            $groups_options[$v->id] = $v->title;
          }

          if (!empty($groups_options)) {
            $this->add('select', 'groups', ts('Groups'), $groups_options, FALSE, [
              'class' => 'crm-select2',
              'multiple' => 'multiple',
              'placeholder' => ts('- select -'),
              'is_active' => TRUE,
            ]);
            $this->assign('showGroups', TRUE);
          }
        }

        CRM_Core_BAO_File::buildAttachment($this, CRM_Documentmanagement_BAO_Document::getTableName());

        $this->addButtons([
          [
            'type' => 'upload',
            'name' => ts('Create'),
            'isDefault' => TRUE,
          ],
          [
            'type' => 'cancel',
            'name' => ts('Cancel'),
          ],
        ]);

        break;

      case CRM_Core_Action::UPDATE:
        $this->buildCreateAndUpdateFields();

        if (CRM_Documentmanagement_BAO_Document::deleteAccess()) {
          $this->addButtons([
            [
              'type' => 'upload',
              'name' => ts('Save'),
              'isDefault' => TRUE,
            ],
            [
              'type' => 'cancel',
              'name' => ts('Cancel'),
            ],
          ]);
        }
        else {
          $this->addButtons([
            [
              'type' => 'upload',
              'name' => ts('Edit'),
              'isDefault' => TRUE,
            ],
            [
              'type' => 'cancel',
              'name' => ts('Cancel'),
            ],
          ]);
        }

        break;

      case CRM_Core_Action::VIEW:
        $this->addButtons([
          [
            'type' => 'cancel',
            'name' => ts('Done'),
          ],
        ]);

        break;

      case CRM_Core_Action::DELETE:
        $this->addButtons([
          [
            'type' => 'upload',
            'name' => ts('Delete'),
            'isDefault' => TRUE,
          ],
          [
            'type' => 'cancel',
            'name' => ts('Cancel'),
          ],
        ]);

        break;
    }
  }

  /**
   * Builds create/update fields.
   */
  private function buildCreateAndUpdateFields() {
    $this->addEntityRef('contact_id', ts('Owner'), ['multiple' => $this->_action == CRM_Core_Action::ADD ? TRUE : FALSE], TRUE);
    $this->add('text', 'title', ts('Title'), '', TRUE);
    $this->add('select', 'category_id', ts('Category'), ['' => ts('- any -')] + $this->categories, TRUE, ['class' => 'crm-select2']);
    $this->add('select', 'type_id', ts('Type'), ['' => ts('- any -')] + $this->types, TRUE, ['class' => 'crm-select2']);
    $this->add('select', 'campaign_id', ts('Campaign'), ['' => ts('- select -')] + $this->campaigns, FALSE, ['class' => 'crm-select2']);
  }

  /**
   * This virtual function is used to set the default values of various form
   * elements.
   *
   * @return array|NULL
   *   reference to the array of default values
   */
  public function setDefaultValues() {
    $defaults = [];

    switch ($this->_action) {
      case CRM_Core_Action::ADD:
        $defaults['action'] = 'add';
        $defaults['campaign_id'] = CRM_Utils_Request::retrieve('pid', 'Integer');

        break;

      case CRM_Core_Action::UPDATE:
        $defaults['id'] = $this->id;
        $defaults['back_page'] = CRM_Utils_Request::retrieve('back_page', 'String', $this, FALSE);
        $defaults['action'] = 'update';
        $defaults['contact_id'] = $this->document['contact_id'];
        $defaults['title'] = $this->document['title'];
        $defaults['category_id'] = $this->document['category_id'];
        $defaults['type_id'] = $this->document['type_id'];
        $defaults['campaign_id'] = $this->document['campaign_id'];

        break;

      case CRM_Core_Action::VIEW:
        $defaults['id'] = $this->id;
        $defaults['back_page'] = CRM_Utils_Request::retrieve('back_page', 'String', $this, FALSE);

        break;

      case CRM_Core_Action::DELETE:
        $defaults['id'] = $this->id;
        $defaults['back_page'] = CRM_Utils_Request::retrieve('back_page', 'String', $this, FALSE);

        break;
    }

    return $defaults;
  }

  /**
   * Preprocess form.
   */
  public function preProcess() {
    parent::preProcess();

    switch ($this->_action) {
      case CRM_Core_Action::ADD:
        CRM_Utils_System::setTitle(E::ts('Create document'));

        $this->add('hidden', 'action');

        $this->getTypesAndCategories();
        $this->getCampaigns();

        break;

      case CRM_Core_Action::UPDATE:
        CRM_Utils_System::setTitle(E::ts('Edit document'));

        $this->getTypesAndCategories();
        $this->getCampaigns();

        $this->add('hidden', 'id');
        $this->add('hidden', 'action');
        $this->add('hidden', 'back_page');

        $this->id = CRM_Utils_Request::retrieve('id', 'Integer', $this, FALSE);
        $this->document = CRM_Documentmanagement_BAO_Document::getSingle($this->id);
        $fileUrl = CRM_Core_BAO_File::attachmentInfo('civicrm_documents', $this->id);

        $this->assign('fileUrl', $fileUrl);

        break;

      case CRM_Core_Action::VIEW:
        CRM_Utils_System::setTitle(E::ts('View document'));

        $this->add('hidden', 'id');
        $this->add('hidden', 'back_page');

        $this->id = CRM_Utils_Request::retrieve('id', 'Integer', $this, FALSE);
        $this->document = CRM_Documentmanagement_BAO_Document::getSingle($this->id);
        $fileUrl = CRM_Core_BAO_File::attachmentInfo('civicrm_documents', $this->id);

        $this->assign('document', $this->document);
        $this->assign('fileUrl', $fileUrl);

        $this->getCampaigns();

        if (!empty($this->campaigns[$this->document['campaign_id']])) {
          $this->assign('campaign', $this->campaigns[$this->document['campaign_id']]);
        }

        break;

      case CRM_Core_Action::DELETE:
        CRM_Utils_System::setTitle(E::ts('Delete document'));

        $this->add('hidden', 'id');
        $this->add('hidden', 'back_page');

        $this->id = CRM_Utils_Request::retrieve('id', 'Integer', $this, FALSE);
        $this->document = CRM_Documentmanagement_BAO_Document::getSingle($this->id);
        break;
    }
  }

  /**
   * Adds custom rules
   *
   * @throws \HTML_QuickForm_Error
   */
  public function addRules() {
    switch ($this->_action) {
      case CRM_Core_Action::ADD:
        $this->addFormRule([
          'CRM_Documentmanagement_Form_Document',
          'validateCreateRules',
        ]);

        break;
    }
  }

  /**
   * Validates custom rules.
   *
   * @param $values
   *
   * @return array|bool
   */
  public static function validateCreateRules($values) {
    $errors = [];

    if (!$_FILES['attachFile_1']['name']) {
      $errors['attachFile_1'] = ts('This field is required.');
    }

    return empty($errors) ? TRUE : $errors;
  }

  /**
   * Sets document categories and types to class variables.
   */
  private function getTypesAndCategories() {
    $this->categories = CRM_Core_PseudoConstant::get('CRM_Documentmanagement_DAO_Document', 'category_id');
    $this->types = CRM_Core_PseudoConstant::get('CRM_Documentmanagement_DAO_Document', 'type_id');
  }

  /**
   * Gets CRM Campaigns.
   *
   * @throws \CiviCRM_API3_Exception
   */
  private function getCampaigns() {
    $campaignData = civicrm_api3('Campaign', 'get', [
      'sequential' => 1,
      'return' => ['id', 'title'],
      'options' => ['limit' => 0, 'sort' => 'title ASC'],
    ]);

    if (!empty($campaignData['values'])) {
      $campaignsArray = [];

      foreach ($campaignData['values'] as $k => $v) {
        $campaignsArray[$v['id']] = $v['title'];
      }

      $this->campaigns = $campaignsArray;
    }
  }

  /**
   * Process the form submission.
   *
   * @throws Exception
   */
  public function postProcess() {
    $params = $this->controller->exportValues($this->_name);
    $loggedContactId = CRM_Core_Session::singleton()->getLoggedInContactID();
    $backURL = $this->getBackURL(CRM_Utils_Request::retrieve('back_page', 'String'), $this->document['contact_id']);

    if($this->_action & CRM_Core_Action::DELETE){
      $this->deleteDocument();

      CRM_Utils_System::redirect($backURL);

      return;
    }

    $numAttachments = Civi::settings()->get('max_attachments');

    for ($i = 1; $i <= $numAttachments; $i++) {
      if (isset($params['attachFile_' . $i])) {
        $params['attachFile_' . $i]['uri'] = $params['attachFile_' . $i]['name'];
        $params['attachFile_' . $i]['location'] = $params['attachFile_' . $i]['name'];
        $params['attachFile_' . $i]['description'] = '';
      }
    }

    $params['last_updater_id'] = $loggedContactId;
    $params['last_update_date'] = date('Y-m-d H:i:s');

    if ($this->_action & CRM_Core_Action::ADD) {
      $contactIds = explode(',', $params['contact_id']);
      $entityFile = NULL;
      $params['creator_id'] = $loggedContactId;

      foreach ($contactIds as $contactId) {
        $params['contact_id'] =  $contactId;

        $document = CRM_Documentmanagement_BAO_Document::create($params);

        if (!$entityFile) {
          CRM_Core_BAO_File::processAttachment(
            $params,
            'civicrm_documents',
            $document->id
          );

          $entityFile = new CRM_Core_DAO_EntityFile();
          $entityFile->entity_table = CRM_Documentmanagement_BAO_Document::getTableName();
          $entityFile->entity_id = $document->id;
          $entityFile->find(TRUE);
        }
        else {
          $newEntityFile = new CRM_Core_DAO_EntityFile();

          $newEntityFile->entity_table = CRM_Documentmanagement_BAO_Document::getTableName();
          $newEntityFile->entity_id = $document->id;
          $newEntityFile->file_id = $entityFile->file_id;

          $newEntityFile->save();
        }

        $document->file_id = $entityFile->file_id;
        $document->save();
      }
    }

    if ($this->_action & CRM_Core_Action::UPDATE) {
      $buttonName = $this->controller->getButtonName();
      $entity = $this->getDefaultEntity();

      switch ($buttonName) {
        case '_qf_' . $entity . '_upload':

          CRM_Documentmanagement_BAO_Document::add($params);

          break;
      }
    }


    CRM_Utils_System::redirect($backURL);
  }

  /**
   * Delete document.
   */
  private function deleteDocument() {
    $countFileDocuments = CRM_Documentmanagement_BAO_Document::getCount(['file_id' => $this->document['file_id']]);

    if ($countFileDocuments == 1) {
      CRM_Core_BAO_File::deleteEntityFile('civicrm_documents', $this->document['id'], NULL, $this->document['file_id']);
    }
    else {
      $entityFile = new CRM_Core_DAO_EntityFile();

      $entityFile->entity_table = CRM_Documentmanagement_BAO_Document::getTableName();
      $entityFile->entity_id = $this->document['id'];
      $entityFile->file_id = $this->document['file_id'];

      if ($entityFile->find(TRUE)) {
        $entityFile->delete();
      }
    }

    CRM_Documentmanagement_BAO_Document::del($this->document['id']);
  }

  /**
   * Gets owner ContactId of a document.
   *
   * @return int|null
   */
  public function getOwnerContactId() {
    return !empty($this->document) ? $this->document['contact_id'] : NULL;
  }

  /**
   * Gets redirect url after create/update Document.
   *
   * @param string $back
   * @param int $ownerContactId
   *
   * @return string
   */
  private function getBackURL($back, $ownerContactId) {
    switch ($back) {
      case 'documents-contact':
        return CRM_Utils_System::url('civicrm/contact/view', 'cid=' . $ownerContactId . '&selectedChild=document');

      case 'templates-search':
      case 'documents-search':
      case 'founding-documents-search':
        return CRM_Utils_System::url('civicrm/documents/civicrm/documents/documents-search');

      default:
        return CRM_Utils_System::url('civicrm/contact/view', 'cid=' . $ownerContactId . '&selectedChild=document');
    }
  }

  /**
   * Function that can be defined in Form to override or
   * perform specific action on cancel action.
   */
  public function cancelAction() {
    $documentId = CRM_Utils_Request::retrieve('id', 'Integer');

    if ($documentId) {
      $document = CRM_Documentmanagement_BAO_Document::getSingle($documentId);
      $backURL = $this->getBackURL(CRM_Utils_Request::retrieve('back_page', 'String'), $document['contact_id']);
    }
    else {
      $backURL = CRM_Utils_System::url('civicrm/');
    }

    CRM_Utils_System::redirect($backURL);
  }

}
