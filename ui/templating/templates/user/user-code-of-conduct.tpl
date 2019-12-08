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
            <small>Accept TWB Code of Conduct</small><br>
            <small>{Localisation::getTranslation('common_denotes_a_required_field')}</small>
        </h1>
    </div>
{/if}

<div class="well alert-info">
    <p><strong>Please take a few moments to accept the Translator Code of Conduct. You will need to fill in this form to continue accessing Kató Platform. Thank you!<br />
<a href="https://translatorswithoutborders.org/wp-content/uploads/2018/05/Code-of-Conduct-for-Translators-May-2018.pdf" target="_blank">Click here to read the TWB Code of Conduct</a></p>
</div>

<div class="well">

    {if isset($flash['error'])}
        <p class="alert alert-error">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}

    <div id="placeholder_for_errors_1"></div>

    <form method="post" action="{urlFor name="user-private-profile" options="user_id.$user_id"}" enctype="multipart/form-data" accept-charset="utf-8">
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

                <label for='over18'><strong>Please confirm you are over the age of 18 years old: <span style="color: red">*</span></strong></label>
                <p class="desc">Translators without Borders (TWB) is a non-profit organization with strong child protection principles, and we cannot consider for volunteer work anyone below the age of 18.</p>
                <input type="checkbox" value="1" name="over18" id="over18" {if $profile_completed}checked="checked"{/if} /> I confirm I am over the age of 18.<br /><br />

                <label for='conduct'><strong>Please read the <a href="http://translatorswithoutborders.org/wp-content/uploads/2018/05/Code-of-Conduct-for-Translators-May-2018.pdf" target="_blank">TWB Code of Conduct for Translators</a>: <span style="color: red">*</span></strong></label>
                <input type="checkbox" value="1" name="conduct" id="conduct" {if $profile_completed}checked="checked"{/if} /> I agree to abide by the TWB Code of Conduct for translators.<br /><br />
            </td></tr>

            <tr><td>
                <hr/>
                <label for='receiveCredit'><strong>Do you want all the above to be visible to all members of the TWB community?:</strong></label>
                <p class="desc">If at any point you wish to change this setting, you can always do that. Additionally you will be able to have a link to this information which you can share with selected people.</p>
                <input type="checkbox" value="1" name="receiveCredit" id="receiveCredit" {if $userPersonalInfo->getReceiveCredit()}checked="checked"{/if} /> Make the above information visible to TWB community
                <hr/>
            </td></tr>

            <tr><td>
                <label for='twbprivacy'><strong>Please read the <a href="https://translatorswithoutborders.org/privacy-policy/" target="_blank">TWB Privacy Policy</a>: <span style="color: red">*</span></strong></label>
                <p class="desc">TWB is committed to protecting personal data and will use the information you provide to send you updates, information and news from Translators without Borders, including our newsletter, volunteer and job opportunities, and crisis alerts. If at any point you wish to unsubscribe from TWB communications, you can always do that.</p>
                <input type="checkbox" value="1" name="twbprivacy" id="twbprivacy" {if $profile_completed}checked="checked"{/if} /> I agree to communications from Translators without Borders by email
                <hr/>
            </td></tr>

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
