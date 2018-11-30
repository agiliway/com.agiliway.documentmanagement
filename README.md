# Document Management

The **Document Management**  extension  (`com.agiliway.documentmanagement`) provides an easy way to create, store and share documents in the CiviCRM system.

**Document Management** extension features:
* **Categories** - the extension allows creating various categories of files. For example, users can create a separate category of templates that will be accessible to all users irrespective of access rights.
* **Storage in the database** - the extension helps overcome the limitation of a Document module, which is storing of documents in personal accounts of users. This way, it ensures that all documents remain accessible even if the constituent leaves the organisation.
* **Search** - search options allow to find the necessary document by title, approximate date of creation, category, type (general, proceedings, agreement, etc.), owner or editor.
* **Uploading of documents to events and campaigns** - managing a campaign or an event, users may upload relevant files to it.
* **Access control** - while templates are accessible to all system users, documents are accessible only to the users that are authorised to view them.


## Screenshots

![Screenshot](/img/screenshot_create_document.png)
---
![Screenshot](/img/screenshot_search_document.png)


## Requirements

 * CiviCRM v5.x


## Installation (git/cli)

To install the extension on an existing CiviCRM site:

```
mkdir sites/all/modules/civicrm/ext
cd sites/default/files/civicrm/ext
git clone https://github.com/agiliway/com.agiliway.documentmanagement com.agiliway.documentmanagement
```
