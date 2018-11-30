{* this template is used for adding/editing relationship types  *}
<div class="crm-block crm-form-block crm-document-form-block">

    {if $action eq 1 OR $action eq 2}
        <table class="{if $action eq 4}crm-info-panel{else}form-layout{/if}">
            <tr class="crm-document-form-block-contact_id">
                <td class="label">{$form.contact_id.label}</td><td class="view-value">{$form.contact_id.html}</td>
            </tr>
            {if $showGroups}
            <tr class="crm-document-form-block-groups">
                <td class="label">{$form.groups.label}</td><td class="view-value">{$form.groups.html}</td>
            </tr>
            {/if}
            <tr class="crm-document-form-block-title">
                <td class="label">{$form.title.label}</td><td class="view-value">{$form.title.html}</td>
            </tr>
            <tr class="crm-document-form-block-title">
                <td class="label">{$form.category_id.label}</td><td class="view-value">{$form.category_id.html}</td>
            </tr>
            <tr class="crm-document-form-block-type_id">
                <td class="label">{$form.type_id.label}</td><td class="view-value">{$form.type_id.html}</td>
            </tr>
            <tr class="crm-document-form-block-type_id">
                <td class="label">{$form.campaign_id.label}</td><td class="view-value">{$form.campaign_id.html}</td>
            </tr>
            <tr class="crm-document-form-block-attachment">
                <td class="label">
                    <label for="attachFile_a">{ts}Attach File{/ts} *</label>
                </td>
                <td class="view-value">
                    {if $action eq 2}
                        <span class="lineHght">{$fileUrl}</span>
                    {else}
                        <div class="fileInputName">
                        <div class="fileInputNameText">{ts}Please choose file{/ts}</div>
                            <span class="add-file-field">{$form.attachFile_1.html}</span>
                            <span class="clear-button">
                            <a href="#" class="crm-hover-button crm-clear-attachment" style="visibility: hidden;"
                            title="{ts}Clear{/ts}"><i class="crm-i fa-times"></i></a>
                            </span>
                        </div>
                    {/if}
                </td>
            </tr>
        </table>
    {/if}

    {if $action eq 4}
        <table class="form-layout-compressed crm-wrapper topBottomPadd">
            {crmAPI entity='OptionValue' action="getSingle" option_group_id="document_types" value="`$document.type_id`" var="type"}
            {crmAPI entity='OptionValue' action="getSingle" option_group_id="document_categories" value="`$document.category_id`" var="category"}

            <tr class="crm-document-form-block-title">
                <td class="label lineHght">{ts}Title{/ts}</td>
                <td class="col-md-8 view-value">
                    <span class="lineHght">{$document.title}</span>
                </td>
            </tr>
            <tr class="crm-document-form-block-owner_contact_name">
                <td class="label lineHght">{ts}Owner{/ts}</td>
                <td class="view-value">
                    <span class="lineHght">
                        <a href="{crmURL p="civicrm/contact/view" q="cid=`$document.contact_id`"}">
                            {$document.contact_name}
                        </a>
                    </span>
                </td>
            </tr>
            <tr class="crm-document-form-block-category">
                <td class="label lineHght">{ts}Category{/ts}</td>
                <td class="view-value">
                    <span class="lineHght">{$category.label}</span>
                </td>
            </tr>
            <tr class="crm-document-form-block-type">
                <td class="label lineHght">{ts}Type{/ts}</td>
                <td class="view-value">
                    <span class="lineHght">{$type.label}</span>
                </td>
            </tr>
            {if $campaign}
            <tr class="crm-document-form-block-campaign">
                <td class="label lineHght">{ts}Campaign{/ts}</td>
                <td class="view-value">
                    <span class="lineHght">{$campaign}</span>
                </td>
            </tr>
            {/if}

            <tr class="crm-document-form-block-create_date">
                <td class="label">{ts}Date created{/ts}</td>
                <td class="view-value">{$document.create_date|crmDate}</td>
            </tr>

            <tr class="crm-document-form-block-last_updater">
                <td class="label">{ts}Modified by{/ts}</td>
                <td class="view-value">
                    <span class="lineHght">
                        <a href="{crmURL p="civicrm/contact/view" q="cid=`$document.last_updater_contact_id`"}">
                            {$document.last_updater_name}
                        </a>
                    </span>
                </td>
            </tr>
            <tr class="crm-document-form-block-date_modified">
                <td class="label">{ts}Date modified{/ts}</td>
                <td class="view-value">{$document.last_update_date|crmDate}</td>
            </tr>
            <tr class="crm-document-form-block-attachment">
                <td class="label">
                    <label for="attachFile_a">{ts}Attach File{/ts} *</label>
                </td>
                <td class="view-value">
                    {if $fileUrl}
                    <span class="lineHght">{$fileUrl}</span>
                    {/if}
                </td>
            </tr>
        </table>
    {/if}

    {if $action eq 8}
        <div class="hiddenElement">
            {$form.id.html}
            {$form.back_page.html}
        </div>
        <div class="asset__status-wrap clearfix">
            <div class="custStatus">
                <p class="status">{ts}Do you want delete this document?{/ts}</p>
            </div>
        </div>
    {/if}

    <div class="crm-submit-buttons">
        {if $action eq 4}
            {if $permissions EQ 'edit'}
                {assign var='urlParams' value="reset=1&action=update&reset=1&id=$entityID&cid=$contactId&context=$context"}
                <a href="{crmURL p='civicrm/documents/document' q=$urlParams}" class="edit button" title="{ts}Edit{/ts}"><span><i class="crm-i fa-pencil"></i> {ts}Edit{/ts}</span></a>
            {/if}

            {if $permissions EQ 'delete'}
                {assign var='urlParams' value="reset=1&action=delete&reset=1&id=$entityID&cid=$contactId&context=$context"}
                <a href="{crmURL p='civicrm/documents/document' q=$urlParams}" class="delete button" title="{ts}Delete{/ts}"><span><i class="crm-i fa-trash"></i> {ts}Delete{/ts}</span></a>
            {/if}
        {/if}

        {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
</div>

{literal}
<script type="text/javascript">
    CRM.$(function ($) {
        var $form = $("form.{/literal}{$form.formClass}{literal}");
        var messageEmpty = "{/literal}{ts}Please choose file{/ts}{literal}";

        $form.on('change', '.crm-document-form-block-attachment :input', function () {
            var file = $(this).val().split('\\').pop();
            if (file) {
                $(this).closest('.crm-document-form-block-attachment').find('.crm-clear-attachment').css('visibility', 'visible');
            } else {
                $(this).closest('.crm-document-form-block-attachment').find('.crm-clear-attachment').css('visibility', 'hidden');
            }
            if ($(this).attr('type') == 'file') {
                $(this).closest('.crm-document-form-block-attachment').find('.crm-form-text').focus();
            }

            var fileInputNameText = $(this).closest(".fileInputMainItem").find(".fileInputNameText");
            fileInputNameText.text((file == '') ? messageEmpty : file);

        });

        $form.on('click', '.crm-clear-attachment', function (e) {
            e.preventDefault();
            $(this).css('visibility', 'hidden');
            $(this).closest('.crm-document-form-block-attachment').find('input').val("");
            $(this).closest('.crm-document-form-block-attachment').find(".fileInputNameText").text(messageEmpty);
        });
    });

    CRM.$('#contact_id').on('click', function (e) {
        var value = CRM.$(this).val();
        if (value.length == 0) {
        } else {
            CRM.$('#groups').select2('val', '');
        }
    });

    CRM.$('#groups').on('click', function (e) {
        var value = CRM.$(this).val();
        if (value != null) {
            if (value.length == 0) {
                CRM.$('#contact_id').select2('val', '');
            } else {
                CRM.$('#contact_id').select2('val', '');
                CRM.$.ajax({
                    url: "{/literal}{crmURL p='civicrm/ajax/get-contacts-ids-by-groups' h=0}{literal}",
                    type: "POST",
                    data: {groups: value},
                    success: function (data, textStatus, jqXHR) {
                        if (typeof data != 'undefined') {
                            //set target contacts ids
                            if (data.length != '') {
                                CRM.$('#contact_id').select2('val', data);
                            }
                        }
                    }
                });
            }
        } else {
            CRM.$('#contact_id').select2('val', '');
        }
    });

    {/literal}{if $action eq 1 or $action eq 2}{literal}

    CRM.$('#category_id').on('change', function (e) {
        /*Never change ...== 3 */
        if (CRM.$(this).val() == 3) {
            /*Never change ..."val", 18 */
            CRM.$("#type_id").select2("val", 18);
            CRM.$("#type_id").addClass('readOnly');
        }else{
            CRM.$("#type_id").select2("val", 0);
            CRM.$("#type_id").removeClass('readOnly');
        }
    });

    CRM.$(window).on('load', function () {
        /*Never change ...val() == 18 */
        if (CRM.$("#type_id").val() == 18) {
            CRM.$("#type_id").addClass('readOnly');
        }
    });

    {/literal}{/if}{literal}
</script>
{/literal}