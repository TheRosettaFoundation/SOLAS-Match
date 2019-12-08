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
    <input type='text' value="{$langPrefSelectCode}" id="langPrefSelect"/>
    <div id="capabilityCount">{$capabilityCount}</div>
    <div id="expertiseCount">{$expertiseCount}</div>

    <!-- Templates... -->
    <div id="template_language_options">
        <option value="0"></option>
        {foreach from=$language_selection key=codes item=language}
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
    <p><strong>Thank you for your interest in Translators without Borders! Please fill out the form below to join the TWB Kató Translator community.<br />
Anyone fluent in at least two languages is welcome to apply.<br /><br />
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
            <tr valign="top">
                <td>
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

                    {foreach from=$url_list key=name item=url}
                    <label for='{$name}'><strong>{$url['desc']}:</strong></label>
                    <input type='text' value="{$url['state']|escape:'html':'UTF-8'}" style="width: 80%" name="{$name}" id="{$name}" />
                    {/foreach}

                    <label for='mobileNumber'><strong>{Localisation::getTranslation('common_mobile_number')}:</strong></label>
                    <input type='text' value="{$userPersonalInfo->getMobileNumber()|escape:'html':'UTF-8'}" style="width: 80%" name="mobileNumber" id="mobileNumber" />

                    <label for='city'><strong>{Localisation::getTranslation('common_city')}:</strong></label>
                    <input type='text' value="{$userPersonalInfo->getCity()|escape:'html':'UTF-8'}" style="width: 80%" name="city" id="city" />

                    <label for='country'><strong>{Localisation::getTranslation('common_country')}:</strong></label>
                    <input type='text' value="{$userPersonalInfo->getCountry()|escape:'html':'UTF-8'}" style="width: 80%" name="country" id="country" />

                    <div id="language_area">
                        <div id = "nativeLanguageDiv">
                            <label><strong>{Localisation::getTranslation('common_native_language')}: <span style="color: red">*</span></strong></label>
                            <p class="desc">Your native language is a language you have been exposed to from birth or within a long period of your life or education. It generally is the language you are more fluent in.</p>
                            <select name="nativeLanguageSelect" id="nativeLanguageSelect" style="width: 41%">
                                <option value=""></option>
                                {foreach $languages as $language}
                                    <option value="{$language->getCode()}" {if $language->getCode() == $nativeLanguageSelectCode}selected="selected"{/if}>{$language->getName()}</option>
                                {/foreach}
                            </select>
                            <select name="nativeCountrySelect" id="nativeCountrySelect" style="width: 41%">
                                <option value=""></option>
                                {foreach $countries as $country}
                                    <option value="{$country->getCode()}" {if $country->getCode() == $nativeCountrySelectCode}selected="selected"{/if}>{$country->getName()}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div id="secondaryLanguageDiv">
                            <hr/>
                            <label><strong>Language Pairs (From/To): <span style="color: red">*</span></strong></label>
                            <p class="desc">In general, people translate from any language they are proficient in, into their first/native language. For example, if the language you are more fluent in is Spanish, and you are also fluent in English and French, you can translate from English to Spanish and from French to Spanish.</p>
                            <button onclick="addSecondaryLanguage(); return false;" class="btn btn-success" id="addLanguageButton" {if $userQualifiedPairsCount >= 120}disabled{/if}>
                                <i class="icon-upload icon-white"></i> {Localisation::getTranslation('user_private_profile_add_secondary_language')}
                            </button>
                            <button onclick="removeSecondaryLanguage(); return false;" class="btn btn-inverse" id="removeLanguageButton" {if $userQualifiedPairsCount <= 1}disabled{/if}>
                                <i class="icon-fire icon-white"></i> {Localisation::getTranslation('common_remove')}
                            </button>
                            <hr/>
                        </div>
                    </div>

                    <label for='conduct'><strong>Please read the <a href="http://translatorswithoutborders.org/wp-content/uploads/2018/05/Code-of-Conduct-for-Translators-May-2018.pdf" target="_blank">TWB Code of Conduct for Translators</a>: <span style="color: red">*</span></strong></label>
                    <input type="checkbox" value="1" name="conduct" id="conduct" {if $profile_completed}checked="checked"{/if} /> I agree to abide by the TWB Code of Conduct for translators.<br /><br />

                    <label for='biography'><strong>About Me:</strong></label>
                    <textarea cols='40' rows='7' style="width: 80%" name="biography" id="biography">{$user->getBiography()|escape:'html':'UTF-8'}</textarea>

                    <div id="loading_warning1">
                        <p><i>{Localisation::getTranslation('common_loading')}</i></p>
                    </div>
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
                        <tr><td colspan="1" align="center" style="font-weight: bold">Services I can provide: <span style="color: red">*</span></td></tr>
                        {assign var="i" value=0}
                        {foreach from=$capability_list key=name item=capability}
                            <tr align="center"><td><input type="checkbox" {if $capability['state']}checked="checked"{/if} name="{$name}" id="capability{$i}" /> {$capability['desc']}</td></tr>
                            {assign var="i" value=$i+1}
                        {/foreach}

                        <tr><td colspan="1" align="center" style="font-weight: bold">My fields of expertise are: <span style="color: red">*</span></td></tr>
                        {assign var="i" value=0}
                        {foreach from=$expertise_list key=name item=expertise}
                            <tr align="center"><td><input type="checkbox" {if $expertise['state']}checked="checked"{/if} name="{$name}" id="expertise{$i}" /> {$expertise['desc']}</td></tr>
                            {assign var="i" value=$i+1}
                        {/foreach}
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for='receiveCredit'><strong>Do you want all the above to be visible to all members(?) of the TWB community?:</strong></label>
                    <p class="desc">If at any point you wish to change this setting, you can always do that. Additionally you will be able to have a link to this information which you can share with selected people.</p>
                    <input type="checkbox" value="1" name="receiveCredit" id="receiveCredit" {if $userPersonalInfo->getReceiveCredit()}checked="checked"{/if} /> Make the above information visible to TWB community
                    <hr/>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for='twbprivacy'><strong>Please read the <a href="https://translatorswithoutborders.org/privacy-policy/" target="_blank">TWB Privacy Policy</a>: <span style="color: red">*</span></strong></label>
                    <p class="desc">TWB is committed to protecting personal data and will use the information you provide to send you updates, information and news from Translators without Borders, including our newsletter, volunteer and job opportunities, and crisis alerts. If at any point you wish to unsubscribe from TWB communications, you can always do that.</p>
                    <input type="checkbox" value="1" name="twbprivacy" id="twbprivacy" {if $profile_completed}checked="checked"{/if} /> I agree to communications from Translators without Borders by email
                    <hr/>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table>
                        <tr align="center"><td colspan="1" align="center" style="font-weight: bold">Where did you hear about TWB? <span style="color: red">*</span></td></tr>
                        <tr align="center"><td colspan="1" align="center"><select name="howheard" id="howheard">
                            <option value="0"></option>
                            {foreach from=$howheard_list key=name item=howheard}
                                <option value="{$name}" {if $howheard['state']}selected="selected"{/if}>{$howheard['desc']}</option>
                            {/foreach}
                        </select></td></tr>

                        <tr><td colspan="1" align="center" style="font-weight: bold">Certifications
                            <p class="desc">If you hold a certification or membership from any of the organizations below, you could qualify to be a verified translator. Please select the organization and click to submit a proof of certification/membership. You will be upgraded to Verified Translator, which will give you immediate access to all projects available, for the verified combination. if you have any questions or can't upload the certificate, please email <a href="mailto:translators@translatorswithoutborders.org?subject={rawurlencode('Translation Certification')}" target="_blank">translators@translatorswithoutborders.org</a></p>
                        </td></tr>
                        {foreach from=$certification_list key=name item=certification}
                            <tr align="center"><td colspan="1" align="center">{if $certification['state']}Already submitted{if $certification['reviewed']} and reviewed{/if}: {/if}<a href="{urlFor name="user-uploads" options="user_id.$user_id|cert_id.$name"}" target="_blank">{$certification['desc']}</a></td></tr>
                        {/foreach}
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
