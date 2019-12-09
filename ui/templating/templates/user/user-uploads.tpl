<!-- Editor Hint: ¿áéíóú -->
{include file='header.tpl'}

<span class="hidden">

    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
    <div id="siteAPI">{$siteAPI}</div>
    <div id="user_id">{$user_id}</div>
    <div id="isSiteAdmin">{if $isSiteAdmin}1{else}0{/if}</div>
</span>

<div class="page-header">
    <h1>
        <img src="https://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=80&r=g" alt="" />
        {$user->getDisplayName()|escape:'html':'UTF-8'}
        <small>Upload Certification Form</small><br>
        <small>{Localisation::getTranslation('common_denotes_a_required_field')}</small>
    </h1>
</div>

<div class="well">

    {if isset($flash['error'])}
        <p class="alert alert-error">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}

    <div id="placeholder_for_errors_1"></div>

    <form method="post" action="{urlFor name="user-uploads" options="user_id.$user_id|cert_id.$name"}" enctype="multipart/form-data" accept-charset="utf-8">
        <table>
            {if $isSiteAdmin}
            <tr><td>
                <label for='note'><strong>{Localisation::getTranslation('common_first_name')}: <span style="color: red">*</span></strong></label>
                <input type='text' value="" style="width: 80%" name="note" id="note" />
            </td></tr>
            {/if}

            <tr><td style="font-weight: bold">Please submit a proof of certification</td></tr> <span style="color: red">*</span></strong></label>
            <tr><td><p class="desc">You will be upgraded to Verified Translator, which will give you immediate access to all projects available, for the verified combination.</p></td></tr>
            <tr><td><input type="file" name="userFile" id="userFile" /></td></tr>

            <tr><td style="padding-bottom: 20px">
                <hr/>
                <div id="placeholder_for_errors_2"></div>
            </td></tr>

            <tr><td align="center">
                <button type="submit" class='btn btn-primary' id="updateBtn">
                    <i class="icon-refresh icon-white"></i> Upload the Certificate
                </button>
            </td></tr>
        </table>

        <input type="hidden" name="sesskey" value="{$sesskey}" />
    </form>
</div>

{include file='footer.tpl'}
