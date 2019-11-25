<!-- Editor Hint: ¿áéíóú -->
{include file='header.tpl'}

<span class="hidden">

    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
    <div id="siteAPI">{$siteAPI}</div>
    <div id="user_id">{$user_id}</div>
    <div id="userQualifiedPairsCount">{$userQualifiedPairsCount}</div>
    {assign var="i" value=0}
    {foreach $userQualifiedPairs as $userQualifiedPair}
        <div id="userQualifiedPairLanguageCodeSource_{$i}">{$userQualifiedPair['language_code_source']}</div>
        <div id="userQualifiedPairLanguageCodeTarget_{$i}">{$userQualifiedPair['language_code_target']}</div>
        <div id="userQualifiedPairQualificationLevel_{$i}">{$userQualifiedPair['qualification_level']}</div>
        {assign var="i" value=$i+1}
    {/foreach}
    <div id="isSiteAdmin">{if $isSiteAdmin}1{else}0{/if}</div>
    <div id="langPrefSelectCodeSaved">{$langPrefSelectCode}</div>

    <!-- Templates... -->
    <div id="template_language_options">
        <option value="0"></option>
        {foreach from=$languages key=codes item=language}
            <option value="{$codes}" >{$language}</option>
        {/foreach}
    </div>

    <div id="template_qualification_options">
        <option value="1">{Localisation::getTranslation('user_qualification_level_1')}</option>
        <option value="2">{Localisation::getTranslation('user_qualification_level_2')}</option>
        <option value="3">{Localisation::getTranslation('user_qualification_level_3')}</option>
    </div>

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
            <small>Translator Registration Form</small><br>
            <small>{Localisation::getTranslation('common_denotes_a_required_field')}</small>
        </h1>
    </div>
{/if}

<div class="well alert-info">
    <p><strong>Thank you for your interest in Translators without Borders! Please fill out the form below to join the TWB Kató Translator community. Anyone fluent in at least two languages is welcome to apply.<br /><br />
If you have any questions about submitting the form, please email <a href="mailto:translators@translatorswithoutborders.org?subject={rawurlencode('Translator Registration Form')}" target="_blank">translators@translatorswithoutborders.org</a></strong></p>
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
            <tr valign="top" align="center">
                <td width="50%">
                    <div id="loading_warning">
                        <p><i>{Localisation::getTranslation('common_loading')}</i></p>
                    </div>
                    <label for='displayName'><strong>{Localisation::getTranslation('common_display_name')}: <span style="color: red">*</span></strong></label>
                    <input type='text' style="width: 80%" value="{$user->getDisplayName()|escape:'html':'UTF-8'}" name="displayName" id="displayName" />

                    <label for='over18'><strong>Please confirm you are over the age of 18 years old: <span style="color: red">*</span></strong></label>
                    <p class="desc">Translators without Borders (TWB) is a non-profit organization with strong child protection principles, and we cannot consider for volunteer work anyone below the age of 18.</p>
                    <input type="checkbox" style="width: 80%" value="1" name="over18" id="over18" {if $over18}checked="checked"{/if} /> I confirm I am over the age of 18.

                    <div id="language_area">
                        <div id = "nativeLanguageDiv">
                            <label><strong>{Localisation::getTranslation('common_native_language')}: <span style="color: red">*</span></strong></label>
                            <p class="desc">Your native language is a language you have been exposed to from birth or within a long period of your life or education. It generally is the language you are more fluent in.</p>
                            <select name="nativeLanguageSelect" id="nativeLanguageSelect" style="width: 41%">
                                <option value="0"></option>
                                {foreach from=$languages key=codes item=language}
                                    <option value="{$codes}" >{$language} {if $codes == $nativeLanguageSelectCode}selected="selected"{/if}>{{$language}}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div id="secondaryLanguageDiv">
                            <hr style="width: 60%" />
                            <p class="desc">In general, people translate from any language they are proficient in, into their first/native language. For example, if the language you are more fluent in is Spanish, and you are also fluent in English and French, you can translate from English to Spanish and from French to Spanish</p>
                            <button onclick="addSecondaryLanguage(); return false;" class="btn btn-success" id="addLanguageButton" {if $userQualifiedPairsCount >= 120}disabled{/if}>
                                <i class="icon-upload icon-white"></i> {Localisation::getTranslation('user_private_profile_add_secondary_language')}
                            </button>
                            <button onclick="removeSecondaryLanguage(); return false;" class="btn btn-inverse" id="removeLanguageButton" {if $userQualifiedPairsCount <= 1}disabled{/if}>
                                <i class="icon-fire icon-white"></i> {Localisation::getTranslation('common_remove')}
                            </button>
                            <hr style="width: 60%" />
                        </div>
                    </div>

                    <label for='biography'><strong>{Localisation::getTranslation('common_biography')}:</strong></label>
                    <textarea cols='40' rows='7' style="width: 80%" name="biography" id="biography">{$user->getBiography()|escape:'html':'UTF-8'}</textarea>

                    <div id="siteLangSelectDiv">
                        <label for='languagePreference' style="margin-top:6px;"'><strong>{Localisation::getTranslation('common_language_preference')}:</strong></label>
                        <select name="langPrefSelect" id="langPrefSelect" style="width: 80%">
                            {foreach $locs as $loc}
                                <option value="{$loc->getCode()}" {if $loc->getCode() == $langPrefSelectCode}selected="selected"{/if}>{$loc->getName()}</option>
                            {/foreach}
                        </select>

                    </div>
                    <div id="loading_warning1">
                        <p><i>{Localisation::getTranslation('common_loading')}</i></p>
                    </div>
                </td>
                <td width="50%">
                    <label for='firstName'><strong>{Localisation::getTranslation('common_first_name')}:</strong></label>
                    <input type='text' value="{$userPersonalInfo->getFirstName()|escape:'html':'UTF-8'}" style="width: 80%" name="firstName" id="firstName"/>

                    <label for='lastName'><strong>{Localisation::getTranslation('common_last_name')}:</strong></label>
                    <input type='text' value="{$userPersonalInfo->getLastName()|escape:'html':'UTF-8'}" style="width: 80%" name="lastName" id="lastName" />

                    <label for='mobileNumber'><strong>{Localisation::getTranslation('common_mobile_number')}:</strong></label>
                    <input type='text' value="{$userPersonalInfo->getMobileNumber()|escape:'html':'UTF-8'}" style="width: 80%" name="mobileNumber" id="mobileNumber" />

                    <label for='businessNumber'><strong>{Localisation::getTranslation('common_business_number')}:</strong></label>
                    <input type='text' value="{$userPersonalInfo->getBusinessNumber()|escape:'html':'UTF-8'}" style="width: 80%" name="businessNumber" id="businessNumber" />

                    <label for='jobTitle'><strong>{Localisation::getTranslation('common_job_title')}:</strong></label>
                    <input type='text' value="{$userPersonalInfo->getJobTitle()|escape:'html':'UTF-8'}" style="width: 80%" name="jobTitle" id="jobTitle" />

                    <label for='address'><strong>{Localisation::getTranslation('common_address')}:</strong></label>
                    <textarea cols='40' rows='5' style="width: 80%" name="address" id="address">{$userPersonalInfo->getAddress()|escape:'html':'UTF-8'}</textarea>

                    <label for='city'><strong>{Localisation::getTranslation('common_city')}:</strong></label>
                    <input type='text' value="{$userPersonalInfo->getCity()|escape:'html':'UTF-8'}" style="width: 80%" name="city" id="city" />

                    <label for='country'><strong>{Localisation::getTranslation('common_country')}:</strong></label>
                    <input type='text' value="{$userPersonalInfo->getCountry()|escape:'html':'UTF-8'}" style="width: 80%" name="country" id="country" />

                    <label for="receiveCredit"><strong>{Localisation::getTranslation('user_private_profile_receive_credit')}:</strong></label>
                    <input type="checkbox" {if $userPersonalInfo->getReceiveCredit()}checked="checked"{/if} name="receiveCredit" id="receiveCredit" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr/>
                    {if $isSiteAdmin}
                        {if !(isset($strict))}
                            {assign var="strict" value=false}
                        {/if}
                    {else}
                        {if !(isset($strict))}
                            {assign var="strict" value=true}
                        {/if}
                        {if !(isset($intervalId))}
                            {assign var="intervalId" value={NotificationIntervalEnum::DAILY}}
                        {/if}
                    {/if}
                    <p>
                        <strong>{Localisation::getTranslation('user_task_stream_notification_edit_0')}</strong>
                    </p>
                    <p>
                        {Localisation::getTranslation('user_task_stream_notification_edit_1')}
                        See <a href="https://community.translatorswb.org/t/signing-up-for-kato-platform-email-notifications/121" target="_blank">Signing up for Kató Platform email notifications</a>.
                    </p>
                    <p>
                        <label for='interval'><strong>{Localisation::getTranslation('user_task_stream_notification_edit_6')}:</strong></label>
                        <select name="interval">
                            <option value="0"
                                {if !isset($intervalId)}
                                    selected="true"
                                {/if}
                            >
                               {Localisation::getTranslation('user_task_stream_notification_edit_never')}
                            </option>
                            <option value="{NotificationIntervalEnum::DAILY}"
                                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::DAILY}
                                    selected="true"
                                {/if}
                            >
                                {Localisation::getTranslation('user_task_stream_notification_edit_daily')}
                            </option>
                            <option value="{NotificationIntervalEnum::WEEKLY}"
                                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::WEEKLY}
                                    selected="true"
                                {/if}
                            >
                                {Localisation::getTranslation('user_task_stream_notification_edit_weekly')}
                            </option>
                            <option value="{NotificationIntervalEnum::MONTHLY}"
                                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::MONTHLY}
                                    selected="true"
                                {/if}
                            >
                                {Localisation::getTranslation('user_task_stream_notification_edit_monthly')}
                            </option>

                            {if $isSiteAdmin}
                            <option value="10">
                                Set this volunteer as in-kind sponsor
                            </option>
                            {/if}
                        </select>
                        {if $in_kind}&nbsp;In-kind Sponsor{/if}
                    </p>
                    <hr/>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table>
                        <tr>
                            <td colspan="3" align="center" style="font-weight: bold">
                                {Localisation::getTranslation('user_private_profile_task_type_preferences')}:
                            </td>
                        </tr>
                        <tr align="center">
                            <td>
                                {Localisation::getTranslation('user_private_profile_translating')}
                            </td>
                            <td>
                                {Localisation::getTranslation('user_private_profile_proofreading')}
                            </td>
                            <td>
                                {Localisation::getTranslation('user_private_profile_interpreting')}
                            </td>
                        </tr>
                        <tr align="center">
                            <td>
                                <input type="checkbox" {if $translator}checked="checked"{/if} name="translator" id="translator" />
                            </td>
                            <td>
                                <input type="checkbox" {if $proofreader}checked="checked"{/if} name="proofreader" id="proofreader" />
                            </td>
                            <td>
                                <input type="checkbox" {if $interpreter}checked="checked"{/if} name="interpreter" id="interpreter" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-bottom: 20px">
                    <hr/>
                    <div id="placeholder_for_errors_2"></div>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button type="submit" onclick="return validateForm();" class='btn btn-primary' id="updateBtn">
                        <i class="icon-refresh icon-white"></i> {Localisation::getTranslation('user_private_profile_update_profile_details')}
                    </button>
                    <button onclick="deleteUser(); return false;" class="btn btn-inverse" id="deleteBtn">
                        <i class="icon-fire icon-white"></i> {Localisation::getTranslation('user_private_profile_delete_user_account')}
                    </button>
                </td>
            </tr>
        </table>

        <input type="hidden" name="sesskey" value="{$sesskey}" />
    </form>
</div>

{include file='footer.tpl'}
