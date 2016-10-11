
{include file='header.tpl'}

{if isset($this_user)}
    <div class="page-header">
        <h1>
        <table>
            <tr>
                <td>                    
                    <img src="https://www.gravatar.com/avatar/{md5( strtolower( trim($this_user->getEmail())))}?s=80{urlencode("&")}r=g" alt="" />
                    {assign var="user_id" value=$this_user->getId()}
                    {if $this_user->getDisplayName() != ''}
                        {TemplateHelper::uiCleanseHTML($this_user->getDisplayName())}
                    {else}
                        {Localisation::getTranslation('common_user_profile')}
                    {/if}
                    <small>{Localisation::getTranslation('user_public_profile_0')}</small>   
                    
                </td>
                <td>                    
                    <div class="pull-right">
                        {if isset($private_access) && isset($org_creation)}
                            {if $org_creation == 'y'}
                                <a href="{urlFor name="create-org"}" class="btn btn-success"
                                   onclick="return confirm('{Localisation::getTranslation('user_public_profile_1')}')">
                                    <i class="icon-star icon-white"></i> {Localisation::getTranslation('common_create_organisation')}
                                </a>
                            {else if $org_creation == 'h'}
                            {/if}
                        {/if} 
                        {if isset($private_access) || $isSiteAdmin}
                            <a href='{urlFor name="user-private-profile" options="user_id.$user_id"}' class='btn btn-primary'>
                                <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('user_public_profile_edit_profile_details')}
                            </a>
                        {/if}
                    </div>
                </td>
            </tr>
        </table>
        </h1>
    </div>
            
{else}
    <div class='page-header'><h1>{Localisation::getTranslation('common_user_profile')} <small>{Localisation::getTranslation('user_public_profile_2')}</small></h1></div>
{/if}

<table border="0">
    <tr valign="top">
        <td style="{if isset($userPersonalInfo) && (isset($private_access)|| $isSiteAdmin)} width: 48%  {else} width: 100% {/if}">
            <div>
                <table border="0" width="40%" style="overflow-wrap: break-word; word-break:break-all;">
                    <thead>                
                    <th align="left"><h3>{Localisation::getTranslation('common_display_name')}</h3></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {TemplateHelper::uiCleanseHTML($this_user->getDisplayName())}
                            </td>
                        </tr>
                        {if isset($private_access) || $isSiteAdmin}
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr> 
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation('common_email')}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {mailto address={$this_user->getEmail()} encode='hex' text={$this_user->getEmail()}}
                                    {if $isSiteAdmin}
                                        <a href='{urlFor name="change-email" options="user_id.$user_id"}' class='pull-right btn btn-primary'>
                                            <i class="icon-list icon-white"></i> {Localisation::getTranslation('common_change_email')}
                                        </a>
                                    {/if}
                                </td>
                            </tr>
                        {/if}
                        <tr>
                            <td style="padding-bottom: 10px"/>
                        </tr> 
                        <tr>
                            <td>
                                <h3>{Localisation::getTranslation('common_native_language')}</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                {if $this_user->getNativeLocale() != null}
                                    {TemplateHelper::getLanguageAndCountry($this_user->getNativeLocale())}
                                {else}
                                    <i>{Localisation::getTranslation('user_public_profile_3')}</i>
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px"/>
                        </tr>
                        {if isset($secondaryLanguages)}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation('common_secondary_languages')}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {foreach from=$secondaryLanguages item=language}
                                        <p>{TemplateHelper::getLanguageAndCountry($language)}</p>
                                    {/foreach}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        {/if}
                        {assign var=bio value={TemplateHelper::uiCleanseHTMLNewlineAndTabs($this_user->getBiography())}}
                        {if isset($bio)}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation('common_biography')}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td> 
                                    {$bio}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 20px"/>
                            </tr>
                        {/if}
                        {if isset($userPersonalInfo) && $userPersonalInfo->getLanguagePreference() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation('common_language_preference')}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {$langPrefName}
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </td>
        
        {if isset($userPersonalInfo) && (isset($private_access)  || $isSiteAdmin)}
            <td style="width: 4%"/>
            <td style="width: 48%">            
                <div>
                    <table border="0" width="40%" style="overflow-wrap: break-word; word-break:break-all;">
                        <tbody align="left" width="48%">
                        {if $userPersonalInfo->getFirstName() != null}
                            <tr>                                  
                                <td ><h3>{Localisation::getTranslation('common_first_name')}</h3></td>
                            </tr>
                            <tr>
                                 <td>
                                     {TemplateHelper::uiCleanseHTML($userPersonalInfo->getFirstName())}
                                 </td>
                             </tr>
                             <tr>
                                 <td style="padding-bottom: 10px"/>
                             </tr>
                        {/if}
                        {if $userPersonalInfo->getLastName() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation('common_last_name')}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {TemplateHelper::uiCleanseHTML($userPersonalInfo->getLastName())}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        {/if}
                        {if $userPersonalInfo->getMobileNumber() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation('common_mobile_number')}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {TemplateHelper::uiCleanseHTML($userPersonalInfo->getMobileNumber())}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                         {/if}
                         {if $userPersonalInfo->getBusinessNumber() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation('common_business_number')}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {TemplateHelper::uiCleanseHTML($userPersonalInfo->getBusinessNumber())}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        {/if}
                        {if $userPersonalInfo->getJobTitle() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation('common_job_title')}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {TemplateHelper::uiCleanseHTML($userPersonalInfo->getJobTitle())}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        {/if}
                        {if $userPersonalInfo->getAddress() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation('common_address')}</h3>
                                </td>
                            </tr>  
                            <tr>
                                <td>
                                    {if $userPersonalInfo->getAddress() != null}
                                        {TemplateHelper::uiCleanseHTMLNewlineAndTabs($userPersonalInfo->getAddress())}
                                    {/if}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        {/if}
                        {if $userPersonalInfo->getCity() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation('common_city')}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {TemplateHelper::uiCleanseHTML($userPersonalInfo->getCity())}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        {/if}
                        {if $userPersonalInfo->getCountry() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation('common_country')}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {TemplateHelper::uiCleanseHTML($userPersonalInfo->getCountry())}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        {/if}
                        </tbody>
                    </table>
                </div>
            </td>
        {/if}
    </tr>
</table>
<p style="margin-bottom:50px;"/>
{if $this_user->getId() == UserSession::getCurrentUserID()}
    <div class="page-header">
        <h1>
            {Localisation::getTranslation('user_public_profile_reference_email')} 
            <small>{Localisation::getTranslation('user_public_profile_16')}</small>
            <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}" class="pull-right"> 
                <i class="icon-list-alt icon-white" style="position:relative; right:-30px; top:12px;"></i>
                <input type="submit" class="btn btn-primary" name="referenceRequest" 
                    value="    {Localisation::getTranslation('user_public_profile_request_reference')}" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
        </h1>            
    </div>
    <p>{Localisation::getTranslation('user_public_profile_15')}</p>
    {if isset($requestSuccess)}
        <p class="alert alert-success">{Localisation::getTranslation('user_public_profile_reference_request_success')}</p>
    {/if}
    </p>{Localisation::getTranslation('user_public_profile_certificate')}</p>
    <p style="margin-bottom:50px;"/>
{/if}

{if isset($badges)}
    {if count($badges) > 0}
        <div class='page-header'>
            <h1>{Localisation::getTranslation('common_badges')}<small> {Localisation::getTranslation('user_public_profile_4')}</small>
                <a href='{urlFor name="badge-list"}' class='pull-right btn btn-primary'>
                    <i class="icon-list icon-white"></i> {Localisation::getTranslation('user_public_profile_list_all_badges')}
                </a>
            </h1>
        </div>

        {foreach $badges as $badge}
            {if !is_null($badge->getOwnerId())}
                {assign var="user_id" value=$this_user->getId()} 
                    {if isset($private_access)}
                        <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}" class="pull-right">
                            <i class="icon-fire icon-white" style="position:relative; right:-25px; top:1px;"></i>
                            <input type="hidden" name="badge_id" value="{$badge->getId()}" />
                            <input type="submit" class='btn btn-inverse' name="revokeBadge" value="    {Localisation::getTranslation('user_public_profile_remove_badge')}" 
                           onclick="return confirm('{Localisation::getTranslation('user_public_profile_5')}')"/>
                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                        </form>   
                    {/if}
                {assign var="org_id" value=$badge->getOwnerId()}
                {assign var="org" value=$orgList[$org_id]}
                <h3>
                    <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                        {$org->getName()}</a> - {TemplateHelper::uiCleanseHTML($badge->getTitle())}
                </h3>
                <p>{TemplateHelper::uiCleanseHTML($badge->getDescription())}</p>
            {else}
                <h3>{Settings::get('site.name')} - {TemplateHelper::uiCleanseHTML(Localisation::getTranslation($badge->getTitle()))}</h3>
                <p>{TemplateHelper::uiCleanseHTML(Localisation::getTranslation($badge->getDescription()))}</p>
            {/if}
            <p style="margin-bottom:20px;"/>
        {/foreach}
        
        <p style="margin-bottom:50px;"/>
    {/if}
{/if}

{if isset($private_access)}
    <div class="page-header">
        <h1>{Localisation::getTranslation('user_public_profile_task_stream_notifications')} <small>{Localisation::getTranslation('user_public_profile_6')}</small>
            <a href="{urlFor name="stream-notification-edit" options="user_id.$user_id"}" class="pull-right btn btn-primary">
                <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('user_public_profile_edit_notifications')}
            </a>
        </h1>
    </div>
    <p>
        {if isset($interval)}
            <p>
                {Localisation::getTranslation('common_what_type_of_emails')}
                {if $strict}
                    <strong>{Localisation::getTranslation('common_strict')}</strong>
                {/if}            
            </p>
            <p>
                {Localisation::getTranslation('common_how_often_receiving_emails')}
                <strong>{$interval}</strong>
            </p>
            <p>
                {if $lastSent != null}
                    {sprintf(Localisation::getTranslation('common_the_last_email_was_sent_on'), {$lastSent})}
                {else}
                    {Localisation::getTranslation('common_no_emails_have_been_sent_yet')}
                {/if}
            </p>
        {else}
            {Localisation::getTranslation('common_you_are_not_currently_receiving_task_stream_notification_emails')}
        {/if}
    </p>
    <p style="margin-bottom:50px;"/>
{/if}

<div class="page-header">
    <h1>{Localisation::getTranslation('common_tags')}<small> {Localisation::getTranslation('user_public_profile_8')}</small>
        <a href='{urlFor name='tags-list'}' class="pull-right btn btn-primary">
            <i class="icon-search icon-white"></i> {Localisation::getTranslation('user_public_profile_search_for_tags')}
        </a>
    </h1>
</div>

{if isset($user_tags) && count($user_tags) > 0}
    {foreach $user_tags as $tag}
        <p>
            {assign var="tag_label" value=TemplateHelper::uiCleanseHTML($tag->getLabel())}
            {assign var="tagId" value=$tag->getId()}
            <a class="tag" href="{urlFor name="tag-details" options="id.$tagId"}">
                <span class="label">{$tag_label}</span>
            </a>
        </p>
    {/foreach}
{else}
    <p class="alert alert-info">
        {Localisation::getTranslation('user_public_profile_9')}
    </p>
{/if}
<p style="margin-bottom:50px;"/>

{if isset($user_orgs)}
    {if count($user_orgs) > 0}
        <div class='page-header'>
            <h1>
                {Localisation::getTranslation('common_organisations')} <small>{Localisation::getTranslation('user_public_profile_10')}</small>
                <a href="{urlFor name='org-search'}" class="pull-right btn btn-primary">
                    <i class="icon-search icon-white"></i> {Localisation::getTranslation('common_search_for_organisations')}
                </a>
            </h1>
        </div>

        {foreach $user_orgs as $org}
            <div class="row">
                {assign var="org_id" value=$org->getId()}
                {assign var="user_id" value=$this_user->getId()}
                <div class="span8">
                    <h3>
                        <i class="icon-briefcase"></i>
                        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">{$org->getName()}</a>
                    </h3>
                </div>
                <div class="row">
                    <form method="post" class="pull-right" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                        {if isset($private_access)}
                            <i class="icon-fire icon-white" style="position:relative; right:-25px; top:1px;"></i>
                            <input type="hidden" name="org_id" value="{$org_id}" />
                            <input type="submit" class='btn btn-inverse' name="revoke" value="    {Localisation::getTranslation('user_public_profile_leave_organisation')}" 
                                   onclick="return confirm('{Localisation::getTranslation('user_public_profile_11')}')"/>
                        {/if}                      
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                </div>
                <div class="span8">
                    <p>
                        <strong>{Localisation::getTranslation('common_biography')}</strong><br/>
                        
                        {if $org->getBiography() == ''}
                            {Localisation::getTranslation('org_public_profile_no_biography_listed')}
                        {else}                            
                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org->getBiography())}
                        {/if}
                    </p>
                    <p>
                    <strong>{Localisation::getTranslation('common_home_page')}</strong><br/>
                    {if $org->getHomepage() != "https://"}
                        <a target="_blank" href="{$org->getHomepage()}">{$org->getHomepage()}</a>
                    {else}
                        {Localisation::getTranslation('org_public_profile_no_home_page_listed')}
                    {/if}
                    </p>
                </div>
            </div>
            <p style="margin-bottom:20px;"/>
            <hr>
        {/foreach}
        
        <p style="margin-bottom:50px;"/>
    {/if}
{/if}

{if isset($archivedJobs)}
    {if count($archivedJobs) > 0}
        <div class='page-header'>
            <h1>{Localisation::getTranslation('common_archived_tasks')} <small>{Localisation::getTranslation('user_public_profile_14')}</small>
                {if isset($private_access)}
                    <a href='{urlFor name="archived-tasks" options="page_no.1"}' class='pull-right btn btn-primary'>
                        <i class="icon-list icon-white"></i> {Localisation::getTranslation('user_public_profile_list_all_archived_tasks')}
                    </a>
                {/if}
            </h1>
        </div>

        {foreach $archivedJobs as $job}
            {include file="task/task.profile-display.tpl" task=$job}
        {/foreach}
        <p style="margin-bottom:50px;"/>
    {/if}
{/if}

{include file='footer.tpl'}

