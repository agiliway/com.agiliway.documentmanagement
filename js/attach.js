(function($) {

  var formName = 'crm-main-content-wrapper';
  if (typeof CRM.vars.documents !== 'undefined' && typeof CRM.vars.documents.formName !== 'undefined'){
    formName = CRM.vars.documents.formName
  }
  var element = $('#attachments-wrap').detach();
  $('#' + formName + ' .crm-submit-buttons').last().before(element);
})(CRM.$);
