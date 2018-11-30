<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Documentmanagement_Upgrader extends CRM_Documentmanagement_Upgrader_Base {

  /**
   * Machine name optionValue fo document categories.
   */
  const DOCUMENTS_CATEGORY = 'documents_category';

  /**
   * Machine name optionValue fo document categories.
   */
  const TEMPLATES_CATEGORY = 'templates_category';

  /**
   * Machine name optionValue fo document categories.
   */
  const FOUNDING_DOCUMENTS_CATEGORY = 'founding_documents_category';

  /**
   * Machine name optionValue fo document type.
   */
  const FOUNDING_DOCUMENTS_TYPE = 'founding_documents_type';

  /**
   * Machine name optionGroup fo document categories.
   */
  const CATEGORIES_GROUP = 'document_categories';

  /**
   * Machine name optionGroup fo document types.
   */
  const TYPES_GROUP = 'document_types';

  /**
   * An install function
   */
  public function install() {
    $this->executeSqlFile('sql/install.sql');

    $this->createMenu();
    $this->createDocumentCategories();
    $this->createDocumentTypes();
  }

  /**
   * Create menu items
   */
  private function createMenu() {
    $items = [
      [
        'label' => ts('Create document'),
        'name' => 'create-document',
        'url' => "civicrm/documents/document?action=add",
      ],
      [
        'label' => ts('Search documents'),
        'name' => 'documents-search',
        'url' => "civicrm/documents/documents-search",
      ],
      [
        'label' => ts('Document categories'),
        'name' => 'documents-categories',
        'url' => "civicrm/admin/options/document_categories?reset=1",
        'separator' => 2,
      ],
      [
        'label' => ts('Document types'),
        'name' => 'documents-types',
        'url' => "civicrm/admin/options/document_types?reset=1",
      ],
    ];

    $mainItem = [
      'label' => ts('Documents'),
      'name' => 'documents-main-menu',
      'url' => "",
    ];
    $elementId = $this->createMenuItem($mainItem);

    foreach ($items as $item) {
      $this->createMenuItem($item, $elementId);
    }
  }

  /**
   * Gets document categories.
   *
   * @return array
   */
  public static function getDocumentCategories() {
    return [
      self::DOCUMENTS_CATEGORY => ts('Documents'),
      self::TEMPLATES_CATEGORY => ts('Templates'),
      self::FOUNDING_DOCUMENTS_CATEGORY => ts('Founding documents')
    ];
  }

  /**
   * Gets document types.
   *
   * @return array
   */
  public static function getDocumentTypes() {
    return [
      self::FOUNDING_DOCUMENTS_TYPE => [
        'label' => ts('Founding documents'),
        'value' => 18
      ]
    ];
  }

  /**
   * Get option groups.
   *
   * @return array
   */
  private function getOptionGroups() {
    return [self::CATEGORIES_GROUP, self::TYPES_GROUP];
  }

  /**
   * Creates document categories.
   */
  private function createDocumentCategories() {
    $documentCategories = self::getDocumentCategories();

    foreach ($documentCategories as $name => $label) {
      $params = [
        'sequential' => 1,
        'option_group_id' => self::CATEGORIES_GROUP,
        'name' => $name,
        'label' => $label,
        'is_active' => 1
      ];

      try {
        $result = civicrm_api3('OptionValue', 'get', $params);

        if ($result['count'] == 0) {
          civicrm_api3('OptionValue', 'create', $params);
        }
      } catch (Exception $e) {
      }
    }
  }

  /**
   * Creates document types.
   */
  private function createDocumentTypes() {
    $documentTypes = self::getDocumentTypes();

    foreach ($documentTypes as $name => $data) {
      $params = [
        'sequential' => 1,
        'option_group_id' => self::TYPES_GROUP,
        'name' => $name,
        'label' => $data['label'],
        'value' => $data['value'],
        'is_active' => 1
      ];

      try {
        $result = civicrm_api3('OptionValue', 'get', $params);

        if ($result['count'] == 0) {
          civicrm_api3('OptionValue', 'create', $params);
        }
      } catch (Exception $e) {
      }
    }
  }

  /**
   * Create Menu Item.
   *
   * @param $item
   * @param null $parent_id
   *
   * @return bool|int
   */
  private function createMenuItem($item, $parent_id = NULL) {
    $value = ['name' => $item['name']];

    CRM_Core_BAO_Navigation::retrieve($value, $navinfo);

    if (!$navinfo) {
      $navigation = [
        'permission' => 'create_document',
        'weight' => 0,
        'is_active' => 1,
        'parent_id' => $parent_id,
      ];
      $navigation = array_merge($item, $navigation);

      $element = CRM_Core_BAO_Navigation::add($navigation);
      $id = $element->id;

      CRM_Core_BAO_Navigation::resetNavigation();
    }
    else {
      $id = $navinfo['id'];
    }

    return $id;
  }

  public function uninstall() {
    $value = ['name' => 'documents-main-menu'];
    $navInfo = [];
    CRM_Core_BAO_Navigation::retrieve($value, $navInfo);

    if ($navInfo) {
      CRM_Core_BAO_Navigation::processDelete($navInfo['id']);
      CRM_Core_BAO_Navigation::resetNavigation();
    }

    $optionGroups = $this->getOptionGroups();

    foreach ($optionGroups as $group) {
      try {
        $optionGroup = civicrm_api3('OptionGroup', 'getsingle', [
          'sequential' => 1,
          'name' => $group,
        ]);

        civicrm_api3('OptionGroup', 'delete', [
          'sequential' => 1,
          'id' => $optionGroup['id'],
        ]);
      } catch (Exception $e) {
      }
    }

    $this->executeSqlFile('sql/uninstall.sql');
  }

  public function enable() {
    CRM_Core_BAO_Navigation::processUpdate(['name' => 'documents-main-menu'], ['is_active' => 1]);
    CRM_Core_BAO_Navigation::resetNavigation();
  }

  public function disable() {
    CRM_Core_BAO_Navigation::processUpdate(['name' => 'documents-main-menu'], ['is_active' => 0]);
    CRM_Core_BAO_Navigation::resetNavigation();
  }

  /**
   * Shows civicrm message.
   *
   * @param $message
   */
  public function showMessage($message) {
    CRM_Core_Session::setStatus($message);
  }

}
