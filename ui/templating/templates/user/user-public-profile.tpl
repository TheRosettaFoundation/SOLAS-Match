
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
                        User Profile
                    {/if}
                    <small>Overview of your account details.</small>   
                    
                </td>
                <td>                    
                    <div class="pull-right">
                        {if isset($private_access) && isset($org_creation)}
                            {if $org_creation == 'y'}
                                <a href="{urlFor name="create-org"}" class="btn btn-success"
                                   onclick="return confirm('By creating an organisation, you confirm that it's activities are not in conflict with the general terms and conditions for the use of this site.')">
                                    <i class="icon-star icon-white"></i> Create Organisation
                                </a>
                            {else if $org_creation == 'h'}
                            {/if}
                        {/if} 
                        {if isset($private_access) || $isSiteAdmin}
                            <a  href='{urlFor name="user-private-profile" options="user_id.$user_id"}' class='btn btn-primary'>
                                <i class="icon-wrench icon-white"></i> Edit Profile Details
                            </a>
                        {/if}
                    </div>
                </td>
            </tr>
        </table>
        </h1>
    </div>
            
{else}
    <div class='page-header'><h1>User Profile <small>Overview of your account details.</small></h1></div>
{/if}

<table border="0">
    <tr valign="top">
        <td style="{if isset($userPersonalInfo) && (isset($private_access)|| $isSiteAdmin)} width: 48%  {else} width: 100% {/if}">
            <div>
                <table border="0" width="40%" style="overflow-wrap: break-word; word-break:break-all;">
                    <thead>                
                    <th align="left"><h3>Public Display Name:</h3></th>
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
                                    <h3>E-Mail:</h3>
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
                                <h3>Native Language:</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                {if $this_user->getNativeLocale() != null}
                                    {TemplateHelper::getLanguageAndCountry($this_user->getNativeLocale())}
                                {else}
                                    <i>Please select a native language!</i>
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px"/>
                        </tr>
                        {if isset($secondaryLanguages)}
                            <tr>
                                <td>
                                    <h3>Secondary Language(s):</h3>
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
                        {assign var=bio value=$this_user->getBiography()}
                        {if isset($bio)}
                            <tr>
                                <td>
                                    <h3>Biography:</h3>
                                </td>
                            </tr>
                            <tr>
                                <td> 
                                    {$this_user->getBiography()}
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
                        {if $userPersonalInfo->getFirstName() != null}
                            <thead>                
                                <th align="left" width="48%"><h3>First Name:</h3></th>
                            </thead>
                            <tbody>
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
                                    <h3>Last Name:</h3>
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
                                    <h3>Mobile Number:</h3>
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
                                    <h3>Business Number:</h3>
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
                                    <h3>Job Title:</h3>
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
                                    <h3>Address:</h3>
                                </td>
                            </tr>  
                            <tr>
                                <td>
                                    {if $userPersonalInfo->getAddress() != null}
                                        {$userPersonalInfo->getAddress()}
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
                                    <h3>City:</h3>
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
                                    <h3>Country:</h3>
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
{if isset($badges)}
    {if count($badges) > 0}
        <div class='page-header'>
            <h1>Badges<small> A list of badges you have earned.</small>
                <a href='{urlFor name="badge-list"}' class='pull-right btn btn-primary'>
                    <i class="icon-list icon-white"></i> List All Badges
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
                            <input type="submit" class='btn btn-inverse' name="revokeBadge" value="    Remove Badge" 
                           onclick="return confirm('Are you sure you want to remove this badge?')"/>
                        </form>   
                    {/if}
                {assign var="org_id" value=$badge->getOwnerId()}
                <h3>
                    <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                        {$orgList[$org_id]->getName()}</a>: {$badge->getTitle()}           
                </h3>
                <p>{$badge->getDescription()}</p>    
            {else}
                <h3>SOLAS Badge: {$badge->getTitle()}</h3>            
                <p>{$badge->getDescription()}</p>                
            {/if}
            <p style="margin-bottom:20px;"/>
        {/foreach}
        
        <p style="margin-bottom:50px;"/>
    {/if}
{/if}

{if isset($private_access)}
    <div class="page-header">
        <h1>Task Stream Notifications <small>How often you recieve task stream e-mail updates.</small>
            <a href="{urlFor name="stream-notification-edit" options="user_id.$user_id"}" class="pull-right btn btn-primary">
                <i class="icon-wrench icon-white"></i> Edit Notifications
            </a>
        </h1>
    </div>
    <p>
        {if isset($interval)}
            You are currently receiving 
            {if $strict}
                <strong>strict</strong>
            {/if}
            <strong>{$interval}</strong> emails.
            {if $lastSent != null}
                The last email was sent on {$lastSent}.
            {else}
                No emails have been sent yet.
            {/if}
        {else}
            You are not currently receiving task stream notification emails.
        {/if}
    </p>
    <p style="margin-bottom:50px;"/>
{/if}

<div class="page-header">
    <h1>Tags<small> A list of tags you have subscribed to.</small>
        <a href='{urlFor name='tags-list'}' class="pull-right btn btn-primary">
            <i class="icon-search icon-white"></i> Search For Tags
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
        You are not subscribed to any Tags on the system at the moment.
    </p>
{/if}
<p style="margin-bottom:50px;"/>

{if isset($user_orgs)}
    {if count($user_orgs) > 0}
        <div class='page-header'>
            <h1>
                Organisations <small>A list of organisations you belong to.</small>
                <a href="{urlFor name='org-search'}" class="pull-right btn btn-primary">
                    <i class="icon-search icon-white"></i> Search for Organisations
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
                            <input type="submit" class='btn btn-inverse' name="revoke" value="    Leave Organisation" 
                                   onclick="return confirm('Are you sure you want to leave the organisation?')"/>
                        {/if}                      
                    </form>
                </div>
                <div class="span8">
                    <p>
                        <strong>Biography:</strong><br/>
                        
                        {if $org->getBiography() == ''}
                            This organisation does not have a biography listed.
                        {else}                            
                            {$org->getBiography()}
                        {/if}
                    </p>
                    <p>
                    <strong>Home Page:</strong><br/>
                    {if $org->getHomePage() != "http://"}
                        <a target="_blank" href="{$org->getHomePage()}">{$org->getHomePage()}</a>
                    {else}
                        This organisation does not have a web site listed.
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
            <h1>Archived Tasks <small>A list of tasks you have worked on in the past.</small>
                {if isset($private_access)}
                    <a href='{urlFor name="archived-tasks" options="page_no.1"}' class='pull-right btn btn-primary'>
                        <i class="icon-list icon-white"></i> List All Archived Tasks
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

