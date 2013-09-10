
{include file='header.tpl'}

{if isset($this_user)}
    <div class="page-header">
        <h1>
        <table>
            <tr>
                <td>                    
                    <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($this_user->getEmail())))}?s=80{urlencode("&")}r=g" alt="" />
                    {assign var="user_id" value=$this_user->getId()}
                    {if $this_user->getDisplayName() != ''}
                        {$this_user->getDisplayName()}
                    {else}
                        {Localisation::getTranslation(Strings::COMMON_USER_PROFILE)}
                    {/if}
                    <small>{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_0)}</small>   
                    
                </td>
                <td>                    
                    <div class="pull-right">
                        {if isset($private_access) && isset($org_creation)}
                            {if $org_creation == 'y'}
                                <a href="{urlFor name="create-org"}" class="btn btn-success"
                                   onclick="return confirm('{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_1)}')">
                                    <i class="icon-star icon-white"></i> {Localisation::getTranslation(Strings::COMMON_CREATE_ORGANISATION)}
                                </a>
                            {else if $org_creation == 'h'}
                            {/if}
                        {/if} 
                        {if isset($private_access) || $isSiteAdmin}
                            <a href='{urlFor name="user-private-profile" options="user_id.$user_id"}' class='btn btn-primary'>
                                <i class="icon-wrench icon-white"></i> {Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_EDIT_PROFILE_DETAILS)}
                            </a>
                        {/if}
                    </div>
                </td>
            </tr>
        </table>
        </h1>
    </div>
            
{else}
    <div class='page-header'><h1>{Localisation::getTranslation(Strings::COMMON_USER_PROFILE)} <small>{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_2)}</small></h1></div>
{/if}

<table border="0">
    <tr valign="top">
        <td style="{if isset($userPersonalInfo) && (isset($private_access)|| $isSiteAdmin)} width: 48%  {else} width: 100% {/if}">
            <div>
                <table border="0" width="40%" style="overflow-wrap: break-word; word-break:break-all;">
                    <thead>                
                    <th align="left"><h3>{Localisation::getTranslation(Strings::COMMON_DISPLAY_NAME)}</h3></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {$this_user->getDisplayName()}
                            </td>
                        </tr>
                        {if isset($private_access) || $isSiteAdmin}
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr> 
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation(Strings::COMMON_EMAIL)}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {mailto address={$this_user->getEMail()} encode='hex' text={$this_user->getEMail()}}
                                </td>
                            </tr>
                        {/if}
                        <tr>
                            <td style="padding-bottom: 10px"/>
                        </tr> 
                        <tr>
                            <td>
                                <h3>{Localisation::getTranslation(Strings::COMMON_NATIVE_LANGUAGE)}</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                {if $this_user->getNativeLocale() != null}
                                    {TemplateHelper::getLanguageAndCountry($this_user->getNativeLocale())}
                                {else}
                                    <i>{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_3)}</i>
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px"/>
                        </tr>
                        {if isset($secondaryLanguages)}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation(Strings::COMMON_SECONDARY_LANGUAGES)}</h3>
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
                        {assign var=bio value={TemplateHelper::uiCleanseNewlineAndTabs($this_user->getBiography())}}
                        {if isset($bio)}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation(Strings::COMMON_BIOGRAPHY)}</h3>
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
                                <td ><h3>{Localisation::getTranslation(Strings::COMMON_FIRST_NAME)}</h3></td>
                            </tr>
                            <tr>
                                 <td>
                                     {$userPersonalInfo->getFirstName()}
                                 </td>
                             </tr>
                             <tr>
                                 <td style="padding-bottom: 10px"/>
                             </tr>
                        {/if}
                        {if $userPersonalInfo->getLastName() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation(Strings::COMMON_LAST_NAME)}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {$userPersonalInfo->getLastName()}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        {/if}
                        {if $userPersonalInfo->getMobileNumber() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation(Strings::COMMON_MOBILE_NUMBER)}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {$userPersonalInfo->getMobileNumber()}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                         {/if}
                         {if $userPersonalInfo->getBusinessNumber() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation(Strings::COMMON_BUSINESS_NUMBER)}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {$userPersonalInfo->getBusinessNumber()}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        {/if}
                        {if $userPersonalInfo->getSip() != null}
                            <tr>
                                <td>
                                    <h3>Session Initiation Protocol (SIP):</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {$userPersonalInfo->getSip()}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        {/if}
                        {if $userPersonalInfo->getJobTitle() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation(Strings::COMMON_JOB_TITLE)}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {$userPersonalInfo->getJobTitle()}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        {/if}
                        {if $userPersonalInfo->getAddress() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation(Strings::COMMON_ADDRESS)}</h3>
                                </td>
                            </tr>  
                            <tr>
                                <td>
                                    {if $userPersonalInfo->getAddress() != null}
                                        {TemplateHelper::uiCleanseNewlineAndTabs($userPersonalInfo->getAddress())}
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
                                    <h3>{Localisation::getTranslation(Strings::COMMON_CITY)}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {$userPersonalInfo->getCity()}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        {/if}
                        {if $userPersonalInfo->getCountry() != null}
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation(Strings::COMMON_COUNTRY)}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {$userPersonalInfo->getCountry()}
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
            {Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_REFERENCE_EMAIL)} 
            <small>{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_16)}</small>
            <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}" class="pull-right"> 
                <i class="icon-list-alt icon-white" style="position:relative; right:-30px; top:12px;"></i>
                <input type="submit" class="btn btn-primary" name="referenceRequest" 
                    value="    {Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_REQUEST_REFERENCE)}" />
            </form>
        </h1>            
    </div>
    <p>{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_15)}</p>
    {if isset($requestSuccess)}
        <p class="alert alert-success">{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_REFERENCE_REQUEST_SUCCESS)}</p>
    {/if}
    <p style="margin-bottom:50px;"/>
{/if}

{if isset($badges)}
    {if count($badges) > 0}
        <div class='page-header'>
            <h1>{Localisation::getTranslation(Strings::COMMON_BADGES)}<small> {Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_4)}</small>
                <a href='{urlFor name="badge-list"}' class='pull-right btn btn-primary'>
                    <i class="icon-list icon-white"></i> {Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_LIST_ALL_BADGES)}
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
                            <input type="submit" class='btn btn-inverse' name="revokeBadge" value="    {Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_REMOVE_BADGE)}" 
                           onclick="return confirm('{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_5)}')"/>
                        </form>   
                    {/if}
                {assign var="org_id" value=$badge->getOwnerId()}
                <h3>
                    <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                        {$orgList[$org_id]->getName()}</a> - {$badge->getTitle()}           
                </h3>
                <p>{$badge->getDescription()}</p>    
            {else}
                <h3>{Settings::get('site.name')} - {$badge->getTitle()}</h3>            
                <p>{$badge->getDescription()}</p>                
            {/if}
            <p style="margin-bottom:20px;"/>
        {/foreach}
        
        <p style="margin-bottom:50px;"/>
    {/if}
{/if}

{if isset($private_access)}
    <div class="page-header">
        <h1>{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_TASK_STREAM_NOTIFICATIONS)} <small>{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_6)}</small>
            <a href="{urlFor name="stream-notification-edit" options="user_id.$user_id"}" class="pull-right btn btn-primary">
                <i class="icon-wrench icon-white"></i> {Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_EDIT_NOTIFICATIONS)}
            </a>
        </h1>
    </div>
    <p>
        {if isset($interval)}
            <p>
                {Localisation::getTranslation(Strings::COMMON_WHAT_TYPE_OF_EMAILS)}
                {if $strict}
                    <strong>{Localisation::getTranslation(Strings::COMMON_STRICT)}</strong>
                {/if}            
            </p>
            <p>
                {Localisation::getTranslation(Strings::COMMON_HOW_OFTEN_RECEIVING_EMAILS)}
                <strong>{$interval}</strong>
            </p>
            <p>
                {if $lastSent != null}
                    {sprintf(Localisation::getTranslation(Strings::COMMON_THE_LAST_EMAIL_WAS_SENT_ON), {$lastSent})}
                {else}
                    {Localisation::getTranslation(Strings::COMMON_NO_EMAILS_HAVE_BEEN_SENT_YET)}
                {/if}
            </p>
        {else}
            {Localisation::getTranslation(Strings::COMMON_YOU_ARE_NOT_CURRENTLY_RECEIVING_TASK_STREAM_NOTIFICATION_EMAILS)}
        {/if}
    </p>
    <p style="margin-bottom:50px;"/>
{/if}

<div class="page-header">
    <h1>{Localisation::getTranslation(Strings::COMMON_TAGS)}<small> {Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_8)}</small>
        <a href='{urlFor name='tags-list'}' class="pull-right btn btn-primary">
            <i class="icon-search icon-white"></i> {Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_SEARCH_FOR_TAGS)}
        </a>
    </h1>
</div>

{if isset($user_tags) && count($user_tags) > 0}
    {foreach $user_tags as $tag}
        <p>
            {assign var="tag_label" value=$tag->getLabel()}
            {assign var="tagId" value=$tag->getId()}
            <a class="tag" href="{urlFor name="tag-details" options="id.$tagId"}">
                <span class="label">{$tag_label}</span>
            </a>
        </p>
    {/foreach}
{else}
    <p class="alert alert-info">
        {Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_9)}
    </p>
{/if}
<p style="margin-bottom:50px;"/>

{if isset($user_orgs)}
    {if count($user_orgs) > 0}
        <div class='page-header'>
            <h1>
                {Localisation::getTranslation(Strings::COMMON_ORGANISATIONS)} <small>{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_10)}</small>
                <a href="{urlFor name='org-search'}" class="pull-right btn btn-primary">
                    <i class="icon-search icon-white"></i> {Localisation::getTranslation(Strings::COMMON_SEARCH_FOR_ORGANISATIONS)}
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
                            <input type="submit" class='btn btn-inverse' name="revoke" value="    {Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_LEAVE_ORGANISATION)}" 
                                   onclick="return confirm('{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_11)}')"/>
                        {/if}                      
                    </form>
                </div>
                <div class="span8">
                    <p>
                        <strong>{Localisation::getTranslation(Strings::COMMON_BIOGRAPHY)}</strong><br/>
                        
                        {if $org->getBiography() == ''}
                            {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_NO_BIOGRAPHY_LISTED)}
                        {else}                            
                            {TemplateHelper::uiCleanseNewlineAndTabs($org->getBiography())}
                        {/if}
                    </p>
                    <p>
                    <strong>{Localisation::getTranslation(Strings::COMMON_HOME_PAGE)}</strong><br/>
                    {if $org->getHomePage() != "http://"}
                        <a target="_blank" href="{$org->getHomePage()}">{$org->getHomePage()}</a>
                    {else}
                        {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_NO_HOME_PAGE_LISTED)}
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
            <h1>{Localisation::getTranslation(Strings::COMMON_ARCHIVED_TASKS)} <small>{Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_14)}</small>
                {if isset($private_access)}
                    <a href='{urlFor name="archived-tasks" options="page_no.1"}' class='pull-right btn btn-primary'>
                        <i class="icon-list icon-white"></i> {Localisation::getTranslation(Strings::USER_PUBLIC_PROFILE_LIST_ALL_ARCHIVED_TASKS)}
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

