<h3>Dry Run - Show Synchronization message for {$action}</h3>

{if $localContact}
    <div class="crm-submit-buttons">
        {if $remoteContact}
        <a class="button new-option" href="{$retrieveUrl}" id="remove_claims_form_batch">
            <span><div class="crm-i fa-sign-in"></div>...Retrieve</span>
        </a>
            <a class="button new-option" href="" id="remove_claims_form_batch">
                <span><div class="crm-i fa-exchange"></div>...Merge (not functional)...</span>
            </a>
        {/if}
        <a class="button new-option" href="{$sendUrl}" id="remove_claims_form_batch">
            <span>Send ...<div class="crm-i fa-paper-plane"></div></span>
        </a>
    </div>

<table cellpadding="0" cellspacing="0" border="0">
    <tr class="columnheader">
        <th>Name</th>
        <th>Local</th>
        <th>Remote</th>
        <th>Merged</th>
    </tr>
    {foreach from=$localContact key=mkey item=mfield}
        {if $mkey eq 'address'}
            {foreach from=$mfield key=akey item=afield}
                <tr>
                    <td>{$akey}</td>
                    <td>{$afield}</td>
                    <td>{$remoteContact[$mkey][$akey]}</td>
                    <td>{$mergeContact[$mkey][$akey]}</td>
                </tr>
            {/foreach}

        {else}
        <tr>
            <td>{$mkey}</td>
            <td>{$mfield}</td>
            <td>{$remoteContact[$mkey]}</td>
            <td>{$mergeContact[$mkey]}</td>
        </tr>
        {/if}
    {/foreach}
</table>
{else}
    <p>This contact cannot by synchronized. Possible it is not part of a region that can be synced</p>
{/if}
