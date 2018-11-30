{* this template is used for adding/editing relationship types  *}
<div class="view-content">
    {if call_user_func(array('CRM_Core_Permission','check'), 'create_document')}
        <div class="action-link">
            <a href='{crmURL p='civicrm/documents/document' q='action=add'}'
               title="{ts}Create document{/ts}"
               class="button newEventOnDashboard
            ">
                {ts}Create document{/ts}
            </a>
        </div>
    {/if}

    <div class="crm-accordion-wrapper crm-documents-contact-search-accordion collapsed">
        <div class="crm-accordion-header crm-accordion-header">
            {ts}Edit Search Criteria{/ts}
        </div><!-- /.crm-accordion-header -->
        <div class="crm-accordion-body">
            <table class="no-border form-layout-compressed activity-search-options">
                <tbody><tr>
                    <td class="crm-contact-form-block-activity_type_filter_id crm-inline-edit-field">
                        {$form.from_date.label}<br>
                        {$form.from_date.html}
                    </td>
                    <td class="crm-contact-form-block-activity_type_exclude_filter_id crm-inline-edit-field">
                        {$form.to_date.label}<br>
                        {$form.to_date.html}
                    </td>
                    <td class="crm-contact-form-block-activity_type_filter_id crm-inline-edit-field">
                        {$form.title.label}<br>
                        {$form.title.html}
                    </td>
                    <td>
                        {$form.type_id.label}<br>
                        {$form.type_id.html|crmAddClass:'crm-select2'}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="documents-contact-table-wrapper">
        <table class="contact-document-selector-document selector row-highlight">
            <thead class="">
            <tr>
                <th data-data="title" scope="col">{ts}Label{/ts}</th>
                <th data-data="type_label" scope="col">{ts}Type{/ts}</th>
                <th data-data="owner_contact_name" scope="col">{ts}Contact{/ts}</th>
                <th data-data="last_update_date" scope="col" class="sorting-icon">{ts}Date modified{/ts}</th>
                <th data-data="last_updater_name" scope="col">{ts}Modified by{/ts}</th>
                <th data-data="actions" scope="col" class="action-links"></th>
            </tr>
            </thead>
        </table>
    </div>
</div>

{literal}
<script type="text/javascript">
    CRM.$(function ($) {
        var $table = $('.documents-contact-table-wrapper table');

        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();
        var title = $('#title').val();
        var typeId = $('#type_id').val();
        var postUrl = "{/literal}{crmURL p='civicrm/documents/get-documents' h=0}{literal}";

        $table.DataTable({
            "ajax": {
                "url": postUrl,
                "data": function (d) {
                    d.contact_id = {/literal}'{$contactId}{literal}';
                    d.from_date = fromDate;
                    d.to_date = toDate;
                    d.title = title;
                    d.type_id = typeId;
                }
            },
            pagingType: "full_numbers",
            bJQueryUI: true,
            sDom: '<"crm-datatable-pager-top clearfix"lfp>rt<"crm-datatable-pager-bottom clearfix"ip>',
            processing: true,
            order: [[ 3, "desc" ]]
        }).draw();

        $('#from_date, #to_date, #title, #type_id').change(function () {
            fromDate = $('#from_date').val();
            toDate = $('#to_date').val();
            title = $('#title').val();
            typeId = $('#type_id').val();

            $table.DataTable().ajax.reload();
        });
    });
</script>
{/literal}