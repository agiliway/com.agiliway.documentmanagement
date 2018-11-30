<?php

/**
 * Hook buildForm for CRM_Event_Form_ManageEvent_EventInfo form.
 *
 * Class CRM_Documentmanagement_Hook_buildForm_Event
 */
class CRM_Documentmanagement_Hook_buildForm_Event extends CRM_Documentmanagement_Attachments {

  public function __construct(&$form) {
    parent::__construct($form);

    $this->entityId = $this->form->getVar('_id');
    $this->action = $this->form->getAction() ?: 1;
    $this->entityTable = 'civicrm_event';
  }

  /**
   * Build fields and Default values.
   */
  public function run() {
    if ($this->form->controller->getPrint() || $this->form->getVar('_id') <= 0 || $this->action != CRM_Core_Action::DELETE) {
      $this->buildForm();
    }
  }

}
