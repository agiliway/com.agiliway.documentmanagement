DELETE FROM civicrm_entity_file WHERE entity_table = 'civicrm_documents';
DELETE FROM civicrm_file WHERE id IN (SELECT file_id as id FROM civicrm_documents);
DROP TABLE IF EXISTS `civicrm_documents`;