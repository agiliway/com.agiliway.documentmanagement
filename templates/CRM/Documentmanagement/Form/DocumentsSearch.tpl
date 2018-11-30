{* this template is used for adding/editing relationship types  *}
<div class="view-content">
    <div class="crm-block crm-form-block crm-documents-contact-search-form-block">
        <div class="crm-accordion-wrapper crm-search_filters-accordion">
            <div class="crm-accordion-header">
                {ts}Edit Search Criteria{/ts}
            </div><!-- /.crm-accordion-header -->
            <div class="crm-accordion-body" style="display: block;">
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
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {$form.campaign_id.label}<br>
                            {$form.campaign_id.html}
                        </td>
                        <td>
                            {$form.type_id.label}<br>
                            {$form.type_id.html|crmAddClass:'crm-select2'}
                        </td>

                        <td>
                            {$form.category_id.label}<br>
                            {$form.category_id.html}
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            {$form.contact_id.label}<br>
                            {$form.contact_id.html}
                        </td>

                        <td colspan="2">
                            {$form.last_updater_id.label}<br>
                            {$form.last_updater_id.html}
                        </td>

                    </tr>
                    </tbody></table>
                <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
            </div><!-- /.crm-accordion-body -->
        </div>
    </div>
    <div class="documents-table-wrapper">
        <table class="selector row-highlight">
            <thead class="">
            <tr>
                <th data-data="title" scope="col">{ts}Label{/ts}</th>
                <th data-data="type_label" scope="col">{ts}Type{/ts}</th>
                <th data-data="owner_contact_name" scope="col">{ts}Owner{/ts}</th>
                <th data-data="last_update_date" scope="col">{ts}Date modified{/ts}</th>
                <th data-data="last_updater_name" scope="col">{ts}Modified by{/ts}</th>
                <th data-data="actions" scope="col" class="action-links"></th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$documents item=row}
                {crmAPI entity='OptionValue' action="getSingle" option_group_id="document_types" value="`$row.type_id`" var="type"}
                <tr class="{cycle values="odd-row,even-row"}">
                    <td>{$row.title}</td>
                    <td>{$type.label}</td>
                    <td>
                        <a href="{crmURL p='civicrm/contact/view' q="cid=`$row.contact_id`"}">{$row.owner_contact_name}</a>
                    </td>
                    <td>{$row.last_update_date|crmDate}</td>
                    <td>
                        <a href="{crmURL p='civicrm/contact/view' q="cid=`$row.last_updater_id`"}">{$row.last_updater_name}</a>
                    </td>
                    <td class="action-links">
                        {$row.actions}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>

{*Add table pagination*}
{literal}
    <script type="text/javascript">
        CRM.$('.documents-table-wrapper table').dataTable({
            destroy: true,
            bFilter: false,
            bAutoWidth: false,
            bProcessing: false,
            bLengthChange: true,
            sPaginationType: "full_numbers",
            sDom: '<"crm-datatable-pager-top"lfp>rt<"crm-datatable-pager-bottom"ip>',
            bJQueryUI: true
        });
    </script>
{/literal}