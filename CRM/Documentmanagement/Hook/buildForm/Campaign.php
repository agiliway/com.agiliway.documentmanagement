<?php

/**
 * Hook buildForm for CRM_Campaign_Form_Campaign form.
 *
 * Class CRM_Documentmanagement_Hook_buildForm_Campaign
 */
class CRM_Documentmanagement_Hook_buildForm_Campaign extends CRM_Documentmanagement_Attachments{

  public function __construct(&$form) {
    parent::__construct($form);

    $this->entityId = $this->form->getVar('_campaignId');
    $this->action = $this->form->getAction() ?: 1;
    $this->entityTable = 'civicrm_campaign';
  }

  /**
   * Build fields and Default values.
   */
  public function run() {
    $this->buildForm();
  }

}
