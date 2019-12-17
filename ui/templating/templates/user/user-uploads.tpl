<!-- Editor Hint: ¿áéíóú -->
{include file='header.tpl'}

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

    <form method="post" action="{urlFor name="user-uploads" options="user_id.$user_id|cert_id.$cert_id"}" enctype="multipart/form-data" accept-charset="utf-8">
        <table>
            <tr><td>
                {if $desc != ''}<label for='note'><strong>Note: <span style="color: red">*</span></strong></label>{/if}
                <input type='text' value="{$desc}" style="width: 80%" name="note" id="note" {if $desc != ''}readonly="readonly"{/if}/>
            </td></tr>

            <tr><td style="font-weight: bold">Please submit a proof of certification</td></tr>
            <tr><td><p class="desc">You will be upgraded to Verified Translator, which will give you immediate access to all projects available, for the verified combination.</p></td></tr>
            <tr><td><input type="file" name="userFile" id="userFile" /> <span style="color: red">*</span></strong></label></td></tr>

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
