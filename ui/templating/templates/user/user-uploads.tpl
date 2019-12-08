<!-- Editor Hint: ¿áéíóú -->
{include file='header.tpl'}

<span class="hidden">

    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
    <div id="siteAPI">{$siteAPI}</div>
    <div id="user_id">{$user_id}</div>
    <div id="isSiteAdmin">{if $isSiteAdmin}1{else}0{/if}</div>
</span>

{if isset($user)}
    <div class="page-header">
        <h1>
            <img src="https://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=80&r=g" alt="" />
            {if $user->getDisplayName() != ''}
                {$user->getDisplayName()|escape:'html':'UTF-8'}
            {else}
                {Localisation::getTranslation('user_private_profile_private_profile')}
            {/if}
            <small>Upload Certification Form</small><br>
            <small>{Localisation::getTranslation('common_denotes_a_required_field')}</small>
        </h1>
    </div>
{/if}

<div class="well">

    {if isset($flash['error'])}
        <p class="alert alert-error">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}

    <div id="placeholder_for_errors_1"></div>

    <form method="post" action="{urlFor name="user-uploads" options="user_id.$user_id|cert_id.$name"}" enctype="multipart/form-data" accept-charset="utf-8">
        <table>
            <tr><td>
                <div id="loading_warning">
                    <p><i>{Localisation::getTranslation('common_loading')}</i></p>
                </div>
Note if admin or different PM
                <label for='firstName'><strong>{Localisation::getTranslation('common_first_name')}: <span style="color: red">*</span></strong></label>
                <input type='text' value="{$userPersonalInfo->getFirstName()|escape:'html':'UTF-8'}" style="width: 80%" name="firstName" id="firstName"/>
            </td></tr>

            <tr><td style="font-weight: bold">Please submit a proof of certification</td></tr>
            <tr><td><p class="desc">Please upload (translation certification)it here. You will be upgraded to Verified Translator, which will give you immediate access to all projects available, for the verified combination.</p></td></tr>
            <tr><td><input type="file" name="projectFile" id="projectFile" /></td></tr>


            <tr><td style="padding-bottom: 20px">
                <hr/>
                <div id="placeholder_for_errors_2"></div>
            </td></tr>

            <tr><td align="center">
                <div id="loading_warning1">
                    <p><i>{Localisation::getTranslation('common_loading')}</i></p>
                </div>
                <button type="submit" onclick="return validateForm();" class='btn btn-primary' id="updateBtn">
                    <i class="icon-refresh icon-white"></i> {Localisation::getTranslation('user_private_profile_update_profile_details')}
                </button>
                <button onclick="deleteUser(); return false;" class="btn btn-inverse" id="deleteBtn">
                    <i class="icon-fire icon-white"></i> {Localisation::getTranslation('user_private_profile_delete_user_account')}
                </button>
            </td></tr>
        </table>

        <input type="hidden" name="sesskey" value="{$sesskey}" />
    </form>
</div>

{include file='footer.tpl'}
