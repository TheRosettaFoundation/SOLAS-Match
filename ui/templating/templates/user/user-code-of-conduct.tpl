<!-- Editor Hint: ¿áéíóú -->
{include file='header.tpl'}

<span class="hidden">
    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
    <div id="siteAPI">{$siteAPI}</div>
    <div id="user_id">{$user_id}</div>
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

            <div class="pull-right">
                <a href='{urlFor name="user-private-profile" options="user_id.$user_id"}' class='btn btn-primary'>
                    <i class="icon-wrench icon-white"></i> Edit Full Linguist Profile Details
                </a>
            </div>

            <br />
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

    <form method="post" action="{urlFor name="user-code-of-conduct" options="user_id.$user_id"}" enctype="multipart/form-data" accept-charset="utf-8">
        <table>
            <tr><td>
                <div id="loading_warning">
                    <p><i>{Localisation::getTranslation('common_loading')}</i></p>
                </div>
                <label for='displayName'><strong>{Localisation::getTranslation('common_display_name')}: <span style="color: red">*</span></strong></label>
                <input type='text' style="width: 80%" value="{$user->getDisplayName()|escape:'html':'UTF-8'}" name="displayName" id="displayName" />

                <label for='firstName'><strong>{Localisation::getTranslation('common_first_name')}: <span style="color: red">*</span></strong></label>
                <input type='text' value="{$userPersonalInfo->getFirstName()|escape:'html':'UTF-8'}" style="width: 80%" name="firstName" id="firstName"/>

                <label for='lastName'><strong>{Localisation::getTranslation('common_last_name')}: <span style="color: red">*</span></strong></label>
                <input type='text' value="{$userPersonalInfo->getLastName()|escape:'html':'UTF-8'}" style="width: 80%" name="lastName" id="lastName" />

                <br /><br />
            </td></tr>

            <tr><td>
                <hr/>
            </td></tr>

            <tr><td>
                <label for='communications_consent'><strong>Communications Consent:</strong></label>
                <p class="desc">We’d like to keep in touch with you about the lives we can change thanks to your support.</p>
                <input type="checkbox" value="1" name="communications_consent" id="communications_consent" {if $communications_consent}checked="checked"{/if} /> Subscribe to the TWB email newsletter. <i>You can unsubscribe at any time.</i>
                <hr/>
            </td></tr>

            <tr><td style="padding-bottom: 20px">
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
