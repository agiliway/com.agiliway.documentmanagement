<?php

use CRM_Documentmanagement_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Documentmanagement_Form_DocumentsSearch extends CRM_Core_Form {

  /**
   * Form Url.
   *
   * @var string
   */
  protected $formUrl = 'civicrm/documents/documents-search';

  /**
   * Documents types.
   *
   * @var array
   */
  private $types = [];

  /**
   * Documents categories.
   *
   * @var array
   */
  private $categories = [];

  /**
   * Campaigns.
   *
   * @var array
   */
  private $campaigns = [];

  /**
   * Preprocess form.
   */
  public function preProcess() {
    parent::preProcess();

    CRM_Utils_System::setTitle(E::ts('Search documents'));

    $this->types = CRM_Core_PseudoConstant::get('CRM_Documentmanagement_DAO_Document', 'type_id');
    $this->categories = CRM_Core_PseudoConstant::get('CRM_Documentmanagement_DAO_Document', 'category_id');

    $documents = CRM_Documentmanagement_BAO_Document::getRows([
      'last_updater_id' => CRM_Utils_Request::retrieve('last_updater_id', 'Int'),
      'contact_id' => CRM_Utils_Request::retrieve('contact_id', 'Int'),
      'from_date' => CRM_Utils_Request::retrieve('from_date', 'String'),
      'to_date' => CRM_Utils_Request::retrieve('to_date', 'String'),
      'title' => CRM_Utils_Request::retrieve('title', 'String'),
      'type_id' => CRM_Utils_Request::retrieve('type_id', 'Int'),
      'campaign_id' => CRM_Utils_Request::retrieve('campaign_id', 'Int'),
      'category_id' => CRM_Utils_Request::retrieve('category_id', 'Int'),
    ]);

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

    $this->assign('documents', $documents);
  }

  /**
   * This virtual function is used to build the form.
   *
   * @throws HTML_QuickForm_Error
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    $this->add('text', 'title', ts('Document Title'));
    $this->add('select', 'type_id', ts('Type'), ['' => ts('- any -')] + $this->types);
    $this->add('datepicker', 'from_date', ts('From'), '', FALSE, ['time' => FALSE]);
    $this->add('datepicker', 'to_date', ts('To'), '', FALSE, ['time' => FALSE]);
    $this->add('select', 'campaign_id', ts('Campaign'), ['' => ts('- select -')] + $this->campaigns, FALSE, ['class' => 'crm-select2']);
    $this->add('select', 'category_id', ts('Category'), ['' => ts('- select -')] + $this->categories, FALSE, ['class' => 'crm-select2']);
    $this->addEntityRef('last_updater_id', ts('Modified by'), ['api' => ['params' => ['contact_type' => 'Individual']]]);
    $this->addEntityRef('contact_id', ts('Owner'));

    $this->addButtons([
      [
        'type' => 'next',
        'name' => ts('Search'),
        'isDefault' => TRUE,
      ],
    ]);
  }

  /**
   * This virtual function is used to set the default values of various form
   * elements.
   *
   * @return array
   *   reference to the array of default values
   * @throws \CRM_Core_Exception
   */
  public function setDefaultValues() {
    $defaults = [
      'last_updater_id' => CRM_Utils_Request::retrieve('last_updater_id', 'Int'),
      'contact_id' => CRM_Utils_Request::retrieve('contact_id', 'Int'),
      'from_date' => CRM_Utils_Request::retrieve('from_date', 'String'),
      'to_date' => CRM_Utils_Request::retrieve('to_date', 'String'),
      'title' => CRM_Utils_Request::retrieve('title', 'String'),
      'type_id' => CRM_Utils_Request::retrieve('type_id', 'Int'),
      'campaign_id' => CRM_Utils_Request::retrieve('campaign_id', 'Int'),
      'category_id' => CRM_Utils_Request::retrieve('category_id', 'Int'),
    ];

    return $defaults;
  }

  /**
   * Process the form submission.
   */
  public function postProcess() {
    $params = $this->exportValues();

    $this->controller->setDestination(CRM_Utils_System::url($this->formUrl, http_build_query([
      'last_updater_id' => $params['last_updater_id'],
      'contact_id' => $params['contact_id'],
      'title' => $params['title'],
      'type_id' => $params['type_id'],
      'campaign_id' => $params['campaign_id'],
      'category_id' => $params['category_id'],
      'from_date' => $params['from_date'],
      'to_date' => $params['to_date'],
    ])));
  }

}
