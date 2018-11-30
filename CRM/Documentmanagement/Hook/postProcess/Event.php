<?php

class CRM_Documentmanagement_Hook_postProcess_Event extends CRM_Documentmanagement_Attachments {

  /**
   * Last creates/updates IDs.
   * Takes with hook post.
   *
   * @var array
   */
  public static $lastIDs = [];

  /**
   * Some time hook_postProcess call's Several times. This variable includes IDs
   * for what postProcess has already done.
   *
   * @var array
   */
  public static $updatedFor = [];

  public function __construct(&$form) {
    parent::__construct($form);
    $this->entityTable = 'civicrm_event';
    $this->action = $this->form->getAction() ?: 1;

    $this->entityId = $this->form->getVar('_id') ?: $this->getLastId();
  }

  /**
   * Runs postProcess.
   */
  public function run() {
    if (!empty(self::$lastIDs)) {
      foreach (static::$lastIDs as $id) {
        if(empty(self::$updatedFor[$id])) {
          $this->entityId = $id;
          $this->postProcess();
          self::$updatedFor[$this->entityId] = TRUE;
        }
      }
    } else {
      if(empty(self::$updatedFor[$this->entityId])){
        $this->postProcess();
        self::$updatedFor[$this->entityId] = TRUE;
      }
    }
  }

}
