{include file="new_header.tpl"}
<!-- Editor Hint: ¿áéíóú -->

<span class="d-none">
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
            <option value="{$codes}">{$language}</option>
        {/foreach}
    </div>

    <div id="template_qualification_options">
        <option value="1">{Localisation::getTranslation('user_qualification_level_1')}</option>
        <option value="2">{Localisation::getTranslation('user_qualification_level_2')}</option>
        <option value="3">{Localisation::getTranslation('user_qualification_level_3')}</option>
    </div>
</span>

<div class="container">
{if isset($flash['error'])}
    <div class="alert alert-danger alert-dismissible fade show mt-4">
        <p><strong>{Localisation::getTranslation('common_warning')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}
{if isset($flash['info'])}
    <div class="alert alert-info alert-dismissible fade show mt-4">
        <p><strong>{Localisation::getTranslation('common_note')} </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}
{if isset($flash['success'])}
    <div class="alert alert-success alert-dismissible fade show mt-4">
        <img src="{urlFor name='home'}ui/img/success.svg" alt="success" class="mx-1" />
        <strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}
{if isset($flash['warning'])}
    <div class="alert alert-warning alert-dismissible fade show mt-4">
        <p><strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['warning'])}</strong></p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}
</div>

<div class="container-xxl px-4 px-sm-5 px-lg-5 pb-5 pt-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="card bg-light-mariam custom-card p-4 card-border-top-accent">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h2 class="fs-3 fw-bold text-dark-mariam mb-0">
                        Please complete your profile
                        <span class="badge bg-secondary ms-2 tabcounter tabcounter1"></span>
                    </h2>
                </div>

                <div id="placeholder_for_errors_1"></div>

                <form method="post" id="userprofile" action="{urlFor name="user-private-profile" options="user_id.$user_id"}" enctype="multipart/form-data" accept-charset="utf-8">

                    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
                                <strong>{Localisation::getTranslation('ff_personal')}</strong>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">
                                <strong>{Localisation::getTranslation('ff_language_and')}</strong>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="verifications-tab" data-bs-toggle="tab" data-bs-target="#verifications" type="button" role="tab" aria-controls="verifications" aria-selected="false">
                                <strong>{Localisation::getTranslation('ff_optional_certs')}</strong>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">

                        <!-- Tab 1: Personal -->
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="displayName" class="form-label fw-semibold required text-dark-mariam">{Localisation::getTranslation('ff_username')}</label>
                                        <input type="text" name="displayName" value="{$user->getDisplayName()|escape:'html':'UTF-8'}" id="displayName" class="form-control" placeholder="{Localisation::getTranslation('ff_username')}" />
                                    </div>
                                    <div class="mb-3">
                                        <label for="firstName" class="form-label fw-semibold required text-dark-mariam">{Localisation::getTranslation('ff_first')}</label>
                                        {if !empty($userPersonalInfo)}
                                        <input type="text" name="firstName" value="{$userPersonalInfo->getFirstName()|escape:'html':'UTF-8'}" id="firstName" class="form-control" placeholder="{Localisation::getTranslation('ff_first')}" />
                                        {else}
                                        <input type="text" name="firstName" value="" id="firstName" class="form-control" placeholder="{Localisation::getTranslation('ff_first')}" />
                                        {/if}
                                    </div>
                                    <div class="mb-3">
                                        <label for="lastName" class="form-label fw-semibold required text-dark-mariam">{Localisation::getTranslation('ff_last')}</label>
                                        {if !empty($userPersonalInfo)}
                                        <input type="text" name="lastName" value="{$userPersonalInfo->getLastName()|escape:'html':'UTF-8'}" id="lastName" class="form-control" placeholder="{Localisation::getTranslation('ff_last')}" />
                                        {else}
                                        <input type="text" name="lastName" value="" id="lastName" class="form-control" placeholder="{Localisation::getTranslation('ff_last')}" />
                                        {/if}
                                    </div>
                                    <div class="mb-3">
                                        <label for="city" class="form-label fw-semibold text-dark-mariam">{Localisation::getTranslation('ff_city')}</label>
                                        {if !empty($userPersonalInfo)}
                                        <input type="text" name="city" id="city" value="{$userPersonalInfo->getCity()|escape:'html':'UTF-8'}" class="form-control" placeholder="{Localisation::getTranslation('ff_your_city')}" />
                                        {else}
                                        <input type="text" name="city" id="city" value="" class="form-control" placeholder="{Localisation::getTranslation('ff_your_city')}" />
                                        {/if}
                                    </div>
                                    <div class="mb-3">
                                        <label for="country" class="form-label fw-semibold text-dark-mariam">{Localisation::getTranslation('ff_country')}</label>
                                        <select name="country" id="country" class="form-select country">
                                            {if !empty($userPersonalInfo)}
                                            <option value="{$userPersonalInfo->getCountry()|escape:'html':'UTF-8'}" selected="selected">{$userPersonalInfo->getCountry()|escape:'html':'UTF-8'}</option>
                                            {/if}
                                            {foreach $countries as $country}
                                                {if $country->getCode() != 'LATN' && $country->getCode() != 'CYRL' && $country->getCode() != '419' && $country->getCode() != 'HANS' && $country->getCode() != 'HANT' && $country->getCode() != 'ARAB' && $country->getCode() != 'BENG' && $country->getCode() != 'ROHG'}
                                                <option value="{$country->getName()|escape:'html':'UTF-8'}" data-id="{$country->getCode()}">{$country->getName()|escape:'html':'UTF-8'}</option>
                                                {/if}
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            {if !empty($userPersonalInfo)}
                                            <input class="form-check-input" type="checkbox" name="receiveCredit" id="receiveCredit" value="1" {if $userPersonalInfo->getReceiveCredit()}checked="checked"{/if} />
                                            {else}
                                            <input class="form-check-input" type="checkbox" name="receiveCredit" id="receiveCredit" value="1" />
                                            {/if}
                                            <label class="form-check-label text-dark-mariam" for="receiveCredit">{Localisation::getTranslation('ff_make_my_info')}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="biography" class="form-label fw-semibold text-dark-mariam">About me</label>
                                        <textarea name="biography" id="biography" class="form-control" rows="8">{$user->getBiography()|escape:'html':'UTF-8'}</textarea>
                                    </div>
                                    {foreach from=$url_list key=name item=url}
                                        <div class="mb-3">
                                            <label for="{$name}" class="form-label fw-semibold text-dark-mariam"><strong>{$url['desc']}:</strong></label>
                                            <input type="text" value="{$url['state']|escape:'html':'UTF-8'}" class="form-control" name="{$name}" id="{$name}" />
                                        </div>
                                    {/foreach}
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="communications_consent" id="communications_consent" value="1" {if $communications_consent}checked="checked"{/if} />
                                            <label class="form-check-label text-dark-mariam" for="communications_consent">
                                                {Localisation::getTranslation('ff_subscribe_news')}
                                                <br /><small><i>{Localisation::getTranslation('ff_you_can_un')}</i></small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <button type="button" onclick="deleteUser(); return false;" class="btn btn-danger" id="deleteBtn">
                                    <i class="fa-solid fa-fire"></i> {Localisation::getTranslation('ff_delete_user')}
                                </button>
                                <button type="button" class="btn btnPrimary nexttab">
                                    {Localisation::getTranslation('ff_next_page')} <i class="fa-solid fa-chevron-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tab 2: Languages & Skills -->
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="mt-3">

                                {if $user_task_limitation_current_user['limit_profile_changes'] != 0}
                                    <input type="hidden" name="nativeLanguageSelect" id="nativeLanguageSelect" value="{$nativeLanguageSelectCode}" />
                                    <input type="hidden" name="nativeCountrySelect" id="nativeCountrySelect" value="{$nativeCountrySelectCode}" />
                                {else}
                                <div class="row g-3 mb-3">
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold required text-dark-mariam">
                                            <strong>{Localisation::getTranslation('ff_native_lang')}</strong>
                                            <i class="fa-solid fa-circle-question ms-1" id="tool5" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{Localisation::getTranslation('ff_please_coose_native')}"></i>
                                        </label>
                                        <select name="nativeLanguageSelect" class="form-select nativeLanguageSelect" id="nativeLanguageSelect">
                                            {if $nativeLanguageSelectCode != '999999999'}
                                                <option value="{$nativeLanguageSelectCode}" selected="selected">{$nativeLanguageSelectName}</option>
                                            {/if}
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold required text-dark-mariam">
                                            <strong>{Localisation::getTranslation('ff_variant')}</strong>
                                            <i class="fa-solid fa-circle-question ms-1" id="tool4" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{Localisation::getTranslation('ff_please_choose_variant')}"></i>
                                        </label>
                                        <select name="nativeCountrySelect" id="nativeCountrySelect" class="form-select variant">
                                            <option value="">{Localisation::getTranslation('ff_select_dash')}</option>
                                            {foreach $countries as $country}
                                                {if $country->getCode() != 'LATN' && $country->getCode() != 'CYRL' && $country->getCode() != '419' && $country->getCode() != 'HANS' && $country->getCode() != 'HANT' && $country->getCode() != 'ARAB' && $country->getCode() != 'BENG' && $country->getCode() != 'ROHG'}
                                                <option value="{$country->getCode()}" {if $country->getCode() == $nativeCountrySelectCode}selected="selected"{/if}>{$country->getName()}</option>
                                                {/if}
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                                {/if}

                                {if $user_task_limitation_current_user['limit_profile_changes'] == 0}
                                <div id="buildyourform" class="mb-3">
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-5">
                                            <label class="form-label fw-semibold required text-dark-mariam">
                                                <strong>{Localisation::getTranslation('ff_I_can_trans')}</strong>
                                                <i class="fa-solid fa-circle-question ms-1" id="tool3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{Localisation::getTranslation('ff_please_choose_lang')}"></i>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold required text-dark-mariam">
                                                <strong>{Localisation::getTranslation('ff_to_lang')}</strong>
                                                <i class="fa-solid fa-circle-question ms-1" id="tool2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{Localisation::getTranslation('ff_we_emcourage')}"></i>
                                            </label>
                                        </div>
                                        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold required text-dark-mariam">
                                                <strong>Qualification Level</strong>
                                                <i class="fa-solid fa-circle-question ms-1" id="tool2b" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="--"></i>
                                            </label>
                                        </div>
                                        {/if}
                                        <span id="btnclick" class="d-none"></span>
                                    </div>
                                </div>
                                {/if}

                                {if $user_task_limitation_current_user['limit_profile_changes'] == 0}
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        {if !($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                                            {if !(isset($intervalId))}
                                                {assign var="intervalId" value={NotificationIntervalEnum::DAILY}}
                                            {/if}
                                        {/if}
                                        <label class="form-label fw-semibold text-dark-mariam">
                                            <strong>{Localisation::getTranslation('ff_how_often_tasks')}</strong>
                                            <i class="fa-solid fa-circle-question ms-1" id="tool1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{Localisation::getTranslation('ff_let_us_how_often_tasks')}"></i>
                                        </label>
                                        <select name="interval" class="form-select interval">
                                            <option value="0"
                                                {if !isset($intervalId)}selected="true"{/if}>
                                                {Localisation::getTranslation('ff_never')}
                                            </option>
                                            <option value="{NotificationIntervalEnum::DAILY}"
                                                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::DAILY}selected="true"{/if}>
                                                {Localisation::getTranslation('ff_daily')}
                                            </option>
                                            <option value="{NotificationIntervalEnum::WEEKLY}"
                                                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::WEEKLY}selected="true"{/if}>
                                                {Localisation::getTranslation('ff_weekly')}
                                            </option>
                                            <option value="{NotificationIntervalEnum::MONTHLY}"
                                                {if isset($intervalId) && $intervalId == NotificationIntervalEnum::MONTHLY}selected="true"{/if}>
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

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold required text-dark-mariam"><strong>{Localisation::getTranslation('ff_services_i')}</strong></label>
                                        <div class="text-danger small mb-1" id="ch1"></div>
                                        {assign var="i" value=0}
                                        {foreach from=$capability_list key=name item=capability}
                                            <div class="form-check">
                                                <input class="form-check-input capabilities" type="checkbox" {if $capability['state']}checked="checked"{/if} name="{$name}" id="capability{$i}" />
                                                <label class="form-check-label text-dark-mariam" for="capability{$i}">{$capability['desc']|escape:'html':'UTF-8'}</label>
                                            </div>
                                            {assign var="i" value=$i+1}
                                        {/foreach}
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold required text-dark-mariam"><strong>{Localisation::getTranslation('ff_fields_exp')}</strong></label>
                                        <div class="text-danger small mb-1" id="ch"></div>
                                        <div class="row g-1">
                                        {assign var="i" value=0}
                                        {foreach from=$expertise_list key=name item=expertise}
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input expertise" type="checkbox" {if $expertise['state']}checked="checked"{/if} name="{$name}" id="expertise{$i}" />
                                                    <label class="form-check-label text-dark-mariam" for="expertise{$i}">{$expertise['desc']|escape:'html':'UTF-8'}</label>
                                                </div>
                                            </div>
                                            {assign var="i" value=$i+1}
                                        {/foreach}
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <button type="button" class="btn btn-secondary" onclick="showTab('home-tab')">
                                        <i class="fa-solid fa-chevron-left me-1"></i>{Localisation::getTranslation('ff_prev_page')}
                                    </button>
                                    <button type="button" class="btn btnPrimary nexttab1">
                                        {Localisation::getTranslation('ff_next_page')} <i class="fa-solid fa-chevron-right ms-1"></i>
                                    </button>
                                </div>

                            </div>
                        </div>

                        <!-- Tab 3: Verifications -->
                        <div class="tab-pane fade" id="verifications" role="tabpanel" aria-labelledby="verifications-tab">
                            <div class="mt-3">
                                <p class="text-dark-mariam">{Localisation::getTranslation('ff_this_step')}<br /><br />
                                {Localisation::getTranslation('ff_if_you_have_cert')}<br /><br />
                                {Localisation::getTranslation('ff_if_you_dont_have_cert')}<br /><br />
                                {Localisation::getTranslation('ff_after_coml')}<br /><br />
                                {Localisation::getTranslation('ff_any_quest')}</p>
                                <ul class="list-unstyled">
                                    {foreach from=$certification_list key=name item=certification}
                                        <li class="mb-2">
                                            {if $certification['state']}
                                                <span class="badge bg-success me-1">Already submitted{if $certification['reviewed'] == 1} and reviewed{/if}</span>
                                            {/if}
                                            <a href="{urlFor name="user-uploads" options="user_id.$user_id|cert_id.$name"}" target="_blank">{$certification['desc']|escape:'html':'UTF-8'}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                                <h4 class="fw-bold mt-4">{Localisation::getTranslation('ff_other_certs')}</h4>
                                <p class="text-dark-mariam">{Localisation::getTranslation('ff_certs_or_other')}</p>
                                <a href="{urlFor name="user-uploads" options="user_id.$user_id|cert_id.TRANSLATOR"}" target="_blank" class="btn btn-outline-primary mb-4">
                                    {Localisation::getTranslation('ff_upload_file')}
                                </a>

                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <button type="button" class="btn btn-secondary" onclick="showTab('profile-tab')">
                                        <i class="fa-solid fa-chevron-left me-1"></i>{Localisation::getTranslation('ff_prev_page')}
                                    </button>
                                    <button type="submit" class="btn btnPrimary" id="updateBtn">
                                        <i class="fa-solid fa-rotate me-1"></i>{Localisation::getTranslation('ff_complete')}
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <input type="hidden" name="sesskey" value="{$sesskey}" />
                    <div id="placeholder_for_errors_2"></div>

                </form>
            </div>
        </div>
    </div>
</div>

{include file="footer2.tpl"}
