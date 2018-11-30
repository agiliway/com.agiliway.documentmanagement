<?php

/**
 * Class CRM_Documentmanagement_Attachments
 */
abstract class CRM_Documentmanagement_Attachments {

  /**
   * The mode of operation for a form.
   * See \CRM_Core_Action.
   *
   * @var int
   */
  protected $action;

  /**
   * EntityID attachments are created for.
   *
   * @var int
   */
  protected $entityId;

  /**
   * EntityTable attachments are created for.
   *
   * @var null
   */
  protected $entityTable = NULL;

  /**
   * @var \CRM_Core_Form
   */
  protected $form;

  public function __construct(CRM_Core_Form &$form) {
    $this->form = $form;
    $this->action = $this->form->getAction();
  }

  /**
   * Build fields and Default values.
   *
   * @return void
   */
  public function buildForm() {
    if ($this->action === CRM_Core_Action::ADD || $this->action === CRM_Core_Action::UPDATE) {
      $name = $this->form->getVar('_name');

      CRM_Core_Resources::singleton()
        ->addScriptFile('com.agiliway.documentmanagement', 'js/attach.js', 200, 'page-footer');
      CRM_Core_Resources::singleton()
        ->addVars('documents', ['formName' => $name]);
      CRM_Core_BAO_File::buildAttachment($this->form, $this->entityTable, $this->entityId);
      $this->form->assign('ShowAttachments', TRUE);
    }
    CRM_Core_Region::instance('page-body')->add([
      'template' => $this->getTemplateFileName(),
    ]);
  }

  /**
   * Process a form submission.
   * Creates and deletes attachments.
   *
   * @return void
   */
  public function postProcess() {
    $submitValues = $this->form->controller->exportValues();

    switch ($this->action) {
      case CRM_Core_Action::ADD:
      case CRM_Core_Action::UPDATE:
        if (!empty($submitValues['is_delete_attachment'])) {
          CRM_Core_BAO_File::deleteEntityFile($this->entityTable, $this->entityId);
        }

        $numAttachments = Civi::settings()->get('max_attachments');

        for ($i = 1; $i <= $numAttachments; $i++) {
          if (isset($submitValues['attachFile_' . $i])) {
            $submitValues['attachFile_' . $i]['uri'] = $submitValues['attachFile_' . $i]['name'];
            $submitValues['attachFile_' . $i]['location'] = $submitValues['attachFile_' . $i]['name'];
            $submitValues['attachFile_' . $i]['description'] = $submitValues['attachDesc_' . $i];
          }
        }

        if ($this->entityId) {
          CRM_Core_BAO_File::processAttachment(
            $submitValues,
            $this->entityTable,
            $this->entityId
          );
          break;
        }
      case CRM_Core_Action::DELETE:
        CRM_Core_BAO_File::deleteEntityFile($this->entityTable, $this->entityId);
        break;
    }
  }

  /**
   * Gets path for template
   *
   * @return string
   */
  public function getTemplateFileName() {
    return CRM_Documentmanagement_ExtensionUtil::path() . '/templates/CRM/Documentmanagement/Form/attachment.tpl';
  }

  /**
   * Gets last added ID into entityTables.
   *
   * @return int
   */
  protected function getLastId() {
    $dao = CRM_Core_DAO::executeQuery('SELECT id AS id FROM ' . $this->entityTable . ' WHERE id = @@Identity;');
    $dao->fetch();
    return $dao->id;
  }

}
