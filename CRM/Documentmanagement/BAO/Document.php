<?php

/**
 * @package Documents
 * @copyright AgiliWay (c) 2018
 */

class CRM_Documentmanagement_BAO_Document extends CRM_Documentmanagement_DAO_Document {

  const LAST_HOUR_SECOND = '23:59:59';

  /**
   * Fetch object based on array of properties.
   *
   * @param array $params
   *   (reference ) an assoc array of name/value pairs.
   * @param array $defaults
   *   (reference ) an assoc array to hold the flattened values.
   *
   * @return CRM_Documentmanagement_BAO_Document|null
   *   object on success, null otherwise
   */
  public static function retrieve(&$params, &$defaults) {
    $object = new CRM_Documentmanagement_BAO_Document();
    $object->copyValues($params);

    if ($object->find(TRUE)) {
      CRM_Core_DAO::storeValues($object, $defaults);
      $object->free();
      return $object;
    }

    return NULL;
  }

  /**
   * @param $params
   *
   * @return \CRM_Core_DAO
   */
  public static function add(&$params) {
    $entity = new CRM_Documentmanagement_DAO_Document();
    $entity->copyValues($params);
    return $entity->save();
  }

  /**
   * @param $params
   *
   * @return \CRM_Core_DAO
   */
  public static function &create(&$params) {
    $transaction = new CRM_Documentmanagement_BAO_Document();

    if (!empty($params['id'])) {
      CRM_Utils_Hook::pre('edit', self::getEntityName(), $params['id'], $params);
    }
    else {
      CRM_Utils_Hook::pre('create', self::getEntityName(), NULL, $params);
    }

    $entityData = self::add($params);

    if (is_a($entityData, 'CRM_Core_Error')) {
      $transaction->rollback();
      return $entityData;
    }

    $transaction->commit();

    if (!empty($params['id'])) {
      CRM_Utils_Hook::post('edit', self::getEntityName(), $entityData->id, $entityData);
    }
    else {
      CRM_Utils_Hook::post('create', self::getEntityName(), $entityData->id, $entityData);
    }

    return $entityData;
  }

  /**
   * Delete
   *
   * @param int $id
   */
  public static function del($id) {
    $entity = new CRM_Documentmanagement_DAO_Document();
    $entity->id = $id;
    $params = [];
    if ($entity->find(TRUE)) {
      CRM_Utils_Hook::pre('delete', self::getEntityName(), $entity->id, $params);
      $entity->delete();
      CRM_Utils_Hook::post('delete', self::getEntityName(), $entity->id, $entity);
    }
  }

  /**
   * Build select query
   *
   * @param string $returnValue
   * @param array $params
   *
   * @return string
   */
  private static function buildSelectQuery($returnValue = 'rows', $params = []) {
    $query = CRM_Utils_SQL_Select::from(CRM_Documentmanagement_BAO_Document::getTableName() . ' documents');

    if ($returnValue == 'count') {
      $query->select('COUNT(documents.id)');
    }
    else {
      $query->select('
        documents.id,
        documents.title,
        documents.category_id,
        documents.type_id,
        documents.campaign_id,
        documents.create_date,
        documents.last_update_date,
        documents.file_id,
        owner_contact.id as contact_id,
        owner_contact.sort_name as contact_name,
        last_updater_contact.id as last_updater_contact_id,
        last_updater_contact.sort_name as last_updater_name
      ');
    }

    $query->join('owner_contact', 'JOIN ' . CRM_Contact_BAO_Contact::getTableName() . ' owner_contact ON owner_contact.id = documents.contact_id')
      ->join('last_updater_contact', 'JOIN ' . CRM_Contact_BAO_Contact::getTableName() . ' last_updater_contact ON last_updater_contact.id = documents.last_updater_id');

    if (!empty($params['id'])) {
      $query->where('documents.id = #id', ['id' => $params['id']]);
    }

    if (!empty($params['contact_id'])) {
      $query->where('documents.contact_id = #contact_id', ['contact_id' => $params['contact_id']]);
    }

    if (!empty($params['last_updater_id'])) {
      $query->where('documents.last_updater_id = #last_updater_id', ['last_updater_id' => $params['last_updater_id']]);
    }

    if (!empty($params['from_date']) && empty($params['to_date'])) {
      $query->where('documents.last_update_date >= @from', ['from' => $params['from_date']]);
    }

    if (!empty($params['to_date']) && empty($params['from_date'])) {
      $query->where('documents.last_update_date <= @to', ['to' => $params['to_date'] . ' ' . self::LAST_HOUR_SECOND]);
    }

    if (!empty($params['to_date']) && !empty($params['from_date'])) {
      $where = 'documents.last_update_date BETWEEN @from AND @to';

      $query->where($where, [
        'from' => $params['from_date'],
        'to' => $params['to_date'] . ' ' . self::LAST_HOUR_SECOND,
      ]);
    }

    if (!empty($params['title'])) {
      $query->where('documents.title LIKE @title', ['title' => '%' . $params['title'] . '%']);
    }

    if (!empty($params['category_id'])) {
      $query->where('documents.category_id = #category_id', ['category_id' => $params['category_id']]);
    }

    if (!empty($params['type_id'])) {
      $query->where('documents.type_id = #type_id', ['type_id' => $params['type_id']]);
    }

    if (!empty($params['campaign_id'])) {
      $query->where('documents.campaign_id = #campaign_id', ['campaign_id' => $params['campaign_id']]);
    }

    if (!empty($params['file_id'])) {
      $query->where('documents.file_id = #file_id', ['file_id' => $params['file_id']]);
    }

    return $query->toSQL();
  }

  /**
   * Build where query
   *
   * @param \CRM_Utils_SQL_Select $query
   * @param array $params
   *
   * @return \CRM_Utils_SQL_Select
   */
  private static function buildWhereQuery($query, $params = []) {

    return $query;
  }

  /**
   * Find documents by params
   *
   * @param array $params
   *
   * @return array
   */
  public static function getAll($params = []) {
    $query = self::buildSelectQuery('rows', $params);

    return CRM_Core_DAO::executeQuery($query)->fetchAll();
  }

  /**
   * Find count documents by params
   *
   * @param array $params
   *
   * @return int
   */
  public static function getCount($params = []) {
    $query = self::buildSelectQuery('count', $params);
    $queryReturn = CRM_Core_DAO::executeQuery($query);

    while ($queryReturn->fetch()) {
      $queryReturn = (array) $queryReturn;

      return (int) $queryReturn['COUNT(documents_id)'];
    }
  }

  /**
   * Get single document
   *
   * @param $id
   *
   * @return array
   */
  public static function getSingle($id) {
    $query = self::buildSelectQuery('rows', ['id' => $id]);
    $document = CRM_Core_DAO::executeQuery($query);

    while ($document->fetch()) {
      return (array) $document;
    }
    return [];
  }

  /**
   * Check page access
   *
   * @return bool
   */
  public static function pageAccess() {
    switch (CRM_Utils_Request::retrieve('action', 'String')) {
      case CRM_Core_Action::ADD:
        return CRM_Core_Permission::check('create_document');

      case CRM_Core_Action::UPDATE:
        return self::editAccess();

      case CRM_Core_Action::DELETE:
        return self::deleteAccess();

      case CRM_Core_Action::VIEW:
      default:
        return self::pageViewAccess();
    }
  }

  /**
   * Check page view access
   *
   * @return bool
   */
  private static function pageViewAccess() {
    $documentID = CRM_Utils_Request::retrieve('id', 'Integer');
    $document = CRM_Documentmanagement_BAO_Document::getSingle($documentID);

    $contactID = $document['contact_id'];
    $currentUserID = CRM_Core_Session::singleton()->get('userID');

    try {
      $templatesCategory = civicrm_api3('OptionValue', 'getSingle', [
        'sequential' => 1,
        'option_group_id' => CRM_Documentmanagement_Upgrader::CATEGORIES_GROUP,
        'name' => CRM_Documentmanagement_Upgrader::TEMPLATES_CATEGORY,
      ]);

      if (
        CRM_Core_Permission::check('view_all_documents')
        ||
        ($currentUserID == $contactID && CRM_Core_Permission::check('view_own_documents'))
        ||
        ($document['category_id'] == $templatesCategory['value'] && CRM_Core_Permission::check('view_own_documents'))
      ) {
        return TRUE;
      }

      return FALSE;
    } catch (Exception $e) {
      return FALSE;
    }
  }

  /**
   * Check view access
   *
   * @param int|null $id
   *
   * @return bool
   */
  public static function viewAccess($id = NULL) {
    return self::actionAccess('view_all_documents', 'view_own_documents', $id);
  }

  /**
   * Check edit access
   *
   * @param int|null $id
   *
   * @return bool
   */
  public static function editAccess($id = NULL) {
    return self::actionAccess('edit_all_documents', 'edit_own_documents', $id);
  }

  /**
   * Check delete access
   *
   * @param int|null $id
   *
   * @return bool
   */
  public static function deleteAccess($id = NULL) {
    return self::actionAccess('delete_all_documents', 'delete_own_documents', $id);
  }

  /**
   * Check action access
   *
   * @param string $allPermission
   * @param string $personalPermission
   * @param int|null $id
   *
   * @return bool
   */
  private static function actionAccess($allPermission, $personalPermission, $id = NULL) {
    $currentUserID = CRM_Core_Session::singleton()->get('userID');
    $contactID = CRM_Utils_Request::retrieve('cid', 'Integer') ? CRM_Utils_Request::retrieve('cid', 'Integer') : CRM_Utils_Request::retrieve('contact_id', 'Integer');

    if (!$contactID) {
      $documentID = $id ? $id : CRM_Utils_Request::retrieve('id', 'Integer');
      $document = CRM_Documentmanagement_BAO_Document::getSingle($documentID);

      $contactID = $document['contact_id'];
    }

    $contactType = CRM_Contact_BAO_Contact::getContactType($contactID);

    if (
      CRM_Core_Permission::check($allPermission)
      ||
      (
        $contactType == 'Organization' &&
        CRM_Core_Permission::check($personalPermission)
      )
      ||
      (
        $contactType == 'Individual' &&
        CRM_Core_Permission::check($personalPermission) &&
        $currentUserID == $contactID
      )
    ) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Gets build documents rows
   *
   * @param array $params
   *
   * @return array
   */
  public static function getRows($params = []){
    $documents = self::getAll($params);

    $currentUserID = CRM_Core_Session::singleton()->get('userID');

    foreach ($documents as &$document) {
      $document['DT_RowId'] = $document['id'];

      $permissions = [];
      if (CRM_Documentmanagement_BAO_Document::viewAccess($document['id'])) {
        $permissions[] = CRM_Core_Permission::VIEW;
      }
      if (CRM_Documentmanagement_BAO_Document::editAccess($document['id'])) {
        $permissions[] = CRM_Core_Permission::EDIT;
      }
      if (CRM_Documentmanagement_BAO_Document::deleteAccess($document['id'])) {
        $permissions[] = CRM_Core_Permission::DELETE;
      }
      $mask = CRM_Core_Action::mask($permissions);

      $actions = [
        'id' => $document['id'],
        'cid' => $currentUserID,
        'back_page' => 'documents-contact',
      ];

      if (CRM_Documentmanagement_BAO_Document::viewAccess($document['id'])) {
        $document['title'] = '
          <a href="' . CRM_Utils_System::url('civicrm/documents/document', 'id=' . $document['id'] . '&action=view&back_page=documents-contact') . '">
            ' . $document['title'] . '
          </a>
        ';
      }

      try {
        $typeOption = civicrm_api3('OptionValue', 'getSingle', [
          'sequential' => 1,
          'option_group_id' => CRM_Documentmanagement_Upgrader::TYPES_GROUP,
          'value' => $document['type_id'],
        ]);

        $document['type_label'] = $typeOption['label'];
      } catch (Exception $e) {
        $document['type_label'] = '';
      }

      $document['owner_contact_name'] = '
        <a href="' . CRM_Utils_System::url('civicrm/contact/view', 'cid=' . $document['contact_id']) . '">
          ' . $document['contact_name'] . '
        </a>
      ';

      $document['last_updater_name'] = '
        <a href="' . CRM_Utils_System::url('civicrm/contact/view', 'cid=' . $document['last_updater_contact_id']) . '">
          ' . $document['last_updater_name'] . '
        </a>
      ';

      $document['last_update_date'] = CRM_Utils_Date::customFormat($document['last_update_date']);

      $links = [
        CRM_Core_Action::VIEW => [
          'name' => ts('View'),
          'url' => 'civicrm/documents/document',
          'qs' => 'reset=1&action=view&id=%%id%%&cid=%%cid%%&back_page=%%back_page%%',
          'title' => ts('View'),
        ],
        CRM_Core_Action::UPDATE => [
          'name' => ts('Edit'),
          'url' => 'civicrm/documents/document',
          'qs' => 'reset=1&action=update&id=%%id%%&cid=%%cid%%&back_page=%%back_page%%',
          'title' => ts('Edit'),
        ],
        CRM_Core_Action::DELETE => [
          'name' => ts('Delete'),
          'url' => 'civicrm/documents/document',
          'qs' => 'action=delete&reset=1&id=%%id%%&cid=%%cid%%&back_page=%%back_page%%',
          'title' => ts('Delete'),
        ],
      ];

      $document['actions'] = CRM_Core_Action::formLink(
        $links,
        $mask, $actions,
        ts('more'),
        FALSE,
        'contribution.selector.row',
        'Contribution',
        $document['id']
      );
    }

    return $documents;
  }

}
