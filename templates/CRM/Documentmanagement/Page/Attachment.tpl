
{if $ShowAttachments}
    <div class="crm-section crm-attachments">
        <div class="crm-attachment">
            {foreach from=$attachments key=attachment_id item=attachment}
                <div class="crm-section attachment_file-section">
                    <div class="label"><label>{ts}Attachment{/ts}:</label></div>
                    <div class="content">
                        <div class="crm-attachment_file">{$attachment.href}</div>
                    </div>
                </div>
                {if $attachment.description}
                <div class="crm-section attachment_description-section">
                    <div class="label"><label>{ts}Description{/ts}:</label></div>
                    <div class="content">
                        <div class="crm-attachment_file">{$attachment.description}</div>
                    </div>
                </div>
                {/if}
            {/foreach}
            <div class="clear"></div>
        </div>
    </div>
{/if}