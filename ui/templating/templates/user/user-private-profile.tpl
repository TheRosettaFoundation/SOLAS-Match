<!-- Editor Hint: ¿áéíóú -->
{include file='header.tpl'}

<span class="hidden">

    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
    <div id="siteAPI">{$siteAPI}</div>
    <div id="user_id">{$user_id}</div>
    <div id="userQualifiedPairsLimit">{$userQualifiedPairsLimit}</div>
    <div id="userQualifiedPairsCount">{$userQualifiedPairsCount}</div>
    {assign var="i" value=0}
    {foreach $userQualifiedPairs as $userQualifiedPair}
        <div id="userQualifiedPairLanguageCodeSource_{$i}">{$userQualifiedPair['language_code_source']}</div>
        <div id="userQualifiedPairLanguageCodeTarget_{$i}">{$userQualifiedPair['language_code_target']}</div>
        <div id="userQualifiedPairQualificationLevel_{$i}">{$userQualifiedPair['qualification_level']}</div>
        {assign var="i" value=$i+1}
    {/foreach}
    <div id="isSiteAdmin">{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}1{else}0{/if}</div>
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

<div class="well">

    {if isset($flash['error'])}
        <p class="alert alert-error">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}

    <h2 class="twb_color">Please complete your profile <span class="tabcounter twb_color tabcounter1"></span></h2>

    <div id="placeholder_for_errors_1"></div>

    <form method="post" id="userprofile" action="{urlFor name="user-private-profile" options="user_id.$user_id"}" enctype="multipart/form-data" accept-charset="utf-8">
        <ul id="myTab" class="nav nav-tabs">
            <li class="active"><a href="#home" data-toggle="tab" id="btnTrigger"><span class="clear_brand"><strong>{Localisation::getTranslation('ff_personal')}</strong></span></a></li>
            <li class="not-active" id="prof"><a href="#profile" data-toggle="tab" id="btnTrigger"><span class="clear_brand"><strong>{Localisation::getTranslation('ff_language_and')}</strong></span></a></li>
            <li class="not-active"><a href="#verifications" data-toggle="tab" id="btnTrigger"><span class="clear_brand"><strong>{Localisation::getTranslation('ff_optional_certs')}</strong></span></a></li>
        </ul>
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="home">
                <br />
                <div class="row-fluid">
                    <div class="span6">
                        <div>
                            <label for='displayName' class="clear_brand required">{Localisation::getTranslation('ff_username')}</label>
                            <input type="text" name="displayName" value="{$user->getDisplayName()|escape:'html':'UTF-8'}" id="displayName" placeholder="{Localisation::getTranslation('ff_username')}" />
                        </div>
                        <div>
                            <label for='firstName' class="clear_brand required">{Localisation::getTranslation('ff_first')}</label>
                            {if !empty($userPersonalInfo)}
                            <input type="text" name="firstName" value="{$userPersonalInfo->getFirstName()|escape:'html':'UTF-8'}" id="firstName" placeholder="{Localisation::getTranslation('ff_first')}" />
                            {else}
                            <input type="text" name="firstName" value="" id="firstName" placeholder="{Localisation::getTranslation('ff_first')}" />
                            {/if}
                        </div>
                        <div>
                            <label for='lastName' class="clear_brand required">{Localisation::getTranslation('ff_last')}</label>
                            {if !empty($userPersonalInfo)}
                            <input type="text" name="lastName" value="{$userPersonalInfo->getLastName()|escape:'html':'UTF-8'}" id="lastName" placeholder="{Localisation::getTranslation('ff_last')}" />
                            {else}
                            <input type="text" name="lastName" value="" id="lastName" placeholder="{Localisation::getTranslation('ff_last')}" />
                            {/if}
                        </div>
                        <div>
                            <label for='city' class="clear_brand">{Localisation::getTranslation('ff_city')}</label>
                            {if !empty($userPersonalInfo)}
                            <input type="text" name="city" id="city" value="{$userPersonalInfo->getCity()|escape:'html':'UTF-8'}" placeholder="{Localisation::getTranslation('ff_your_city')}" />
                            {else}
                            <input type="text" name="city" id="city" value="" placeholder="{Localisation::getTranslation('ff_your_city')}" />
                            {/if}
                        </div>
                        <div>
                            <label class="clear_brand">{Localisation::getTranslation('ff_country')}</label>
                            <select name="country" id="country" class="country">
                                {if !empty($userPersonalInfo)}
                                <option value="{$userPersonalInfo->getCountry()|escape:'html':'UTF-8'}" selected="selected">{$userPersonalInfo->getCountry()|escape:'html':'UTF-8'}</option>
                                {/if}
                                {foreach $countries as $country}
                                    {if $country->getCode() != 'LATN' && $country->getCode() != 'CYRL' && $country->getCode() != '419' && $country->getCode() != 'HANS' && $country->getCode() != 'HANT' && $country->getCode() != 'ARAB' && $country->getCode() != 'BENG' && $country->getCode() != 'ROHG'}
                                    <option value="{$country->getName()|escape:'html':'UTF-8'}" data-id="{$country->getCode()}">  {$country->getName()|escape:'html':'UTF-8'}</option>
                                    {/if}
                                {/foreach}
                            </select>
                        </div>
                        <br />
                    
                        <div>
                            <label class="checkbox clear_brand">
                            {if !empty($userPersonalInfo)}
                            <input type="checkbox" name="receiveCredit" value="1" {if $userPersonalInfo->getReceiveCredit()}checked="checked"{/if} /> {Localisation::getTranslation('ff_make_my_info')}
                            {else}
                            <input type="checkbox" name="receiveCredit" value="1" /> {Localisation::getTranslation('ff_make_my_info')}
                            {/if}
                            </label>
                        </div>
                    </div>
                    <div class="span6 clear_brand">
                        <div>
                            <label class="clear_brand">About me</label>
                            <textarea name="biography" id="biography" style="width:400px;" rows="8">{$user->getBiography()|escape:'html':'UTF-8'}</textarea>
                        </div>
                        {foreach from=$url_list key=name item=url}
                            <div>
                                <label for='{$name}'><strong>{$url['desc']}:</strong></label>
                                <input type='text' value="{$url['state']|escape:'html':'UTF-8'}" style="width: 400px;" name="{$name}" id="{$name}" />
                            </div>
                        {/foreach}
                        <div>
                            <label class="checkbox clear_brand">
                            <input type="checkbox" name="communications_consent" id="communications_consent" value="1" {if $communications_consent}checked="checked"{/if} /> {Localisation::getTranslation('ff_subscribe_news')}
                            <br /><small><i>{Localisation::getTranslation('ff_you_can_un')} </i></small>
                            </label>
                        </div>
                    </div>
                </div>
                <button onclick="deleteUser(); return false;" class="btn btn-inverse" id="deleteBtn">
                    <i class="icon-fire icon-white"></i> {Localisation::getTranslation('ff_delete_user')}
                </button>

                <a style="cursor:pointer;color:#FFFFFF;" href="#profile1" class="pull-right nexttab nnext1 nnext111 pull-right btn btn-primary" id="btnTrigger1">Next</a>
            </div>
            <div class="tab-pane fade profile" id="profile">
                <br />

                {if $user_task_limitation_current_user['limit_profile_changes'] != 0}
                    <input type="hidden" name="nativeLanguageSelect" id="nativeLanguageSelect" value="{$nativeLanguageSelectCode}" />
                    <input type="hidden" name="nativeCountrySelect"  id="nativeCountrySelect"  value="{$nativeCountrySelectCode}" />
                {else}
                <div class="row-fluid" >
                    <div class="span5">
                        <label class="clear_brand required label_space"><strong>{Localisation::getTranslation('ff_native_lang')}</strong> <i class="icon-question-sign" id="tool5" data-toggle="tooltip" title="{Localisation::getTranslation('ff_please_coose_native')}"></i></label>
                        <select name="nativeLanguageSelect" class="nativeLanguageSelect" id="nativeLanguageSelect">
                            {if $nativeLanguageSelectCode != '999999999'}
                                <option value="{$nativeLanguageSelectCode}" selected="selected">{$nativeLanguageSelectName}</option>
                            {/if}
                        </select>
                    </div>
                    <div class="span4">
                        <label class="clear_brand required label_space"><strong>{Localisation::getTranslation('ff_variant')}</strong> <i class="icon-question-sign" id="tool4" data-toggle="tooltip" title="{Localisation::getTranslation('ff_please_choose_variant')}"></i></label>
                        <select name="nativeCountrySelect" id="nativeCountrySelect" class="variant">
                            <option value="">{Localisation::getTranslation('ff_select_dash')}</option>
                            {foreach $countries as $country}
                                {if $country->getCode() != 'LATN' && $country->getCode() != 'CYRL' && $country->getCode() != '419' && $country->getCode() != 'HANS' && $country->getCode() != 'HANT' && $country->getCode() != 'ARAB' && $country->getCode() != 'BENG' && $country->getCode() != 'ROHG'}
                                <option value="{$country->getCode()}" {if $country->getCode() == $nativeCountrySelectCode}selected="selected"{/if}>{$country->getName()}</option>
                                {/if}
                            {/foreach}
                        </select>
                    </div>
                </div>
                <br/>
                {/if}

                {if $user_task_limitation_current_user['limit_profile_changes'] == 0}
                <div id="buildyourform">
                    <div class="row-fluid" >
                        <div class="span5">
                            <label class="clear_brand required"><strong>{Localisation::getTranslation('ff_I_can_trans')}</strong> <i class="icon-question-sign" id="tool3" data-toggle="tooltip" title="{Localisation::getTranslation('ff_please_choose_lang')}"></i></label>
                        </div>
                        <div class="span4">
                            <label class="clear_brand required"><strong>{Localisation::getTranslation('ff_to_lang')}</strong> <i class="icon-question-sign" id="tool2" data-toggle="tooltip" title="{Localisation::getTranslation('ff_we_emcourage')}"></i></label>
                        </div>
                        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                        <div class="span2">
                            <label class="clear_brand required"><strong>Qualification Level</strong> <i class="icon-question-sign" id="tool2" data-toggle="tooltip" title="--"></i></label>
                        </div>
                        {/if}
                        <span id="btnclick" class="countclick"></span>
                    </div>
                </div>
                {/if}

                {if $user_task_limitation_current_user['limit_profile_changes'] == 0}
                <div class="row-fluid">
                    <div class="span6 clear_brand">
                        {if !($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                            {if !(isset($intervalId))}
                                {assign var="intervalId" value={NotificationIntervalEnum::DAILY}}
                            {/if}
                        {/if}
                        <strong> {Localisation::getTranslation('ff_how_often_tasks')} <i class="icon-question-sign" id="tool1" data-toggle="tooltip" title="{Localisation::getTranslation('ff_let_us_how_often_tasks')}"></i></strong><br />
                        <select name="interval" class="interval">
                            <option value="0"
                                {if !isset($intervalId)}
                                    selected="true"
                                {/if}
                            >
                               {Localisation::getTranslation('ff_never')}
                            </option>
                            <option value="{NotificationIntervalEnum::DAILY}"
                                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::DAILY}
                                    selected="true"
                                {/if}
                            >
                                {Localisation::getTranslation('ff_daily')}
                            </option>
                            <option value="{NotificationIntervalEnum::WEEKLY}"
                                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::WEEKLY}
                                    selected="true"
                                {/if}
                            >
                                {Localisation::getTranslation('ff_weekly')}
                            </option>
                            <option value="{NotificationIntervalEnum::MONTHLY}"
                                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::MONTHLY}
                                    selected="true"
                                {/if}
                            >
                                {Localisation::getTranslation('ff_monthly')}
                            </option>
                            {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                            <option value="10">
                                Set this volunteer as in-kind sponsor
                            </option>
                            {/if}
                        </select>
                        {if $in_kind}&nbsp;In-kind Sponsor{/if}
                    </div>
                </div>
                {/if}

                <div class="row-fluid">
                    <div class="span4 clear_brand">
                        <label class="clear_brand required"><strong>{Localisation::getTranslation('ff_services_i')}</strong></label>
                        <div class="ch1" id="ch1" style="color:#F00;"></div>
                        {assign var="i" value=0}
                        {foreach from=$capability_list key=name item=capability}
                            <input type="checkbox" class="capabilities" {if $capability['state']}checked="checked"{/if} name="{$name}" id="capability{$i}" /> {$capability['desc']|escape:'html':'UTF-8'}  <br />
                            {assign var="i" value=$i+1}
                        {/foreach}
                    </div>
                    <div class="span8 clear_brand">
                        <div class="span6">
                            <label class="clear_brand required"><strong>{Localisation::getTranslation('ff_fields_exp')}</strong></label>
                            <div class="ch" id="ch" style="color:#F00;"></div>
                            {assign var="i" value=0}
                            {foreach from=$expertise_list key=name item=expertise}
                                <input type="checkbox" class="expertise" {if $expertise['state']}checked="checked"{/if} name="{$name}" id="expertise{$i}" /> {$expertise['desc']|escape:'html':'UTF-8'}<br />
                                {assign var="i" value=$i+1}
                            {/foreach}
                        </div>
                    </div>
                </div>
                <br />
        
                <a style="cursor:pointer;color:#FFFFFF;margin-right:18%;" href="#verifications" class="pull-right nexttab1 next111 btn btn-primary" id="btnTrigger1">{Localisation::getTranslation('ff_next_page')}</a> <a style="cursor:pointer;color:#FFFFFF;" href="#home" class="pull-right next111 btn btn-primary" id="btnTrigger11">{Localisation::getTranslation('ff_prev_page')}</a>
            
            </div>
            <div class="tab-pane fade" id="verifications">
                <br />
                <p class="desc">{Localisation::getTranslation('ff_this_step')}<br /><br />
{Localisation::getTranslation('ff_if_you_have_cert')}<br /><br />
{Localisation::getTranslation('ff_if_you_dont_have_cert')}<br /><br />
{Localisation::getTranslation('ff_after_coml')}<br /><br />
{Localisation::getTranslation('ff_any_quest')}</p>
                <ul>
                    {foreach from=$certification_list key=name item=certification}
                        <li>{if $certification['state']}Already submitted{if $certification['reviewed'] == 1} and reviewed{/if}: {/if}<a href="{urlFor name="user-uploads" options="user_id.$user_id|cert_id.$name"}" target="_blank">{$certification['desc']|escape:'html':'UTF-8'}</a></li>
                    {/foreach}
                </ul>
                <br />
                <h4 style="font-weight: bold"><br />{Localisation::getTranslation('ff_other_certs')}</h4>
                <p class="desc">{Localisation::getTranslation('ff_certs_or_other')}</p>
                <a href="{urlFor name="user-uploads" options="user_id.$user_id|cert_id.TRANSLATOR"}" target="_blank">{Localisation::getTranslation('ff_upload_file')}</a>
                <br />
                <button type="submit"  class='pull-right btn btn-primary' id="updateBtn">
                    <i class="icon-refresh icon-white"></i> {Localisation::getTranslation('ff_complete')}
                </button>
                <a style="cursor:pointer;color:#FFFFFF;" href="#profile1" class="pull-right next111 btn btn-primary" id="btnTrigger11">{Localisation::getTranslation('ff_prev_page')}</a>
                <br />
                <br />
                
            </div>
        </div>
        <input type="hidden" name="sesskey" value="{$sesskey}" />

        <div id="placeholder_for_errors_2"></div>

    </form>
</div>

{include file='footer.tpl'}
