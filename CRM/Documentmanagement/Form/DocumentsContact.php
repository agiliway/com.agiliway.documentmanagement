<?php

use CRM_Documentmanagement_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Documentmanagement_Form_DocumentsContact extends CRM_Core_Form {

  /**
   * Document types.
   *
   * @var array
   */
  private $types = [];

  /**
   * This virtual function is used to build the form.
   *
   * @throws HTML_QuickForm_Error
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    $this->add('text', 'title', ts('Title'));
    $this->add('select', 'type_id', ts('Type'), ['' => ts('- any -')] + $this->types);
    $this->add('datepicker', 'from_date', ts('From'), '', FALSE, ['time' => FALSE]);
    $this->add('datepicker', 'to_date', ts('To'), '', FALSE, ['time' => FALSE]);
  }

  /**
   * Preprocess form.
   */
  public function preProcess() {
    parent::preProcess();

    CRM_Utils_System::setTitle(E::ts('Documents'));

    $this->types = CRM_Core_PseudoConstant::get('CRM_Documentmanagement_DAO_Document', 'type_id');

    $this->assign('contactId', $this->getContactID());
  }

}
