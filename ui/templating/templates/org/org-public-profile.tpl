{include file='header.tpl'}

{if isset($org)}
    {if isset($flash['error'])}
        <div class="alert alert-error">
            {$flash['error']}
        </div>
    {/if}
    {if isset($flash['success'])}
        <div class="alert alert-success">
            {$flash['success']}
        </div>
    {/if}
    {if isset($flash['info'])}
        <div class="alert alert-info">
            {$flash['info']}
        </div>
    {/if}
    <div class='page-header'>
        <h1>
            {if $org->getName() != ''}
                {$org->getName()}
            {else}
                {Localisation::getTranslation(Strings::COMMON_ORGANISATION_PROFILE)}
            {/if}
            <small>{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_AN_ORGANISATION_OF)} {$siteName}.</small>
            {assign var="org_id" value=$org->getId()}
            {if isset($user)}
                <div class="pull-right">
                    {if $isMember || $adminAccess}
                            <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class='btn btn-primary'>
                            <i class="icon-wrench icon-white"></i> {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_EDIT_ORGANISATION_DETAILS)}
                        </a>
                    {/if}
                    {if !$isMember}
                        <a href="{urlFor name="org-request-membership" options="org_id.$org_id"}" class='btn btn-primary'>
                            <i class="icon-ok-circle icon-white"></i> {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_REQUEST_MEMBERSHIP)}
                        </a>
                    {/if}
                </div>
            {/if}
        </h1>
    </div>
{/if}

    <div class="well">
        <table>
            <tr valign="top">
                <td  style="width: 48%">
                    <div>
                        <table border="0" width="40%" style="overflow-wrap: break-word; word-break:break-all;">
                            <thead>                
                                <th align="left">{Localisation::getTranslation(Strings::COMMON_HOME_PAGE)}:<hr/></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getHomePage() != 'http://'}
                                            <a href="{$org->getHomePage()}">{$org->getHomePage()}</a>
                                        {else}
                                            {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_NO_HOME_PAGE_LISTED)}.
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation(Strings::COMMON_ADDRESS)}:</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getAddress() != ''}
                                            {$org->getAddress()}
                                        {else}
                                            {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_NO_ADDRESS_LISTED)}.
                                        {/if}
                                    </td>  
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation(Strings::COMMON_CITY)}:</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getCity() != ''}
                                            {$org->getCity()}
                                        {else}
                                            {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_NO_CITY_LISTED)}.
                                        {/if}
                                    </td>  
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation(Strings::COMMON_COUNTRY)}:</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getCountry() != ''}
                                            {$org->getCountry()}
                                        {else}
                                            {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_NO_COUNTRY_LISTED)}.
                                        {/if}
                                    </td>  
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
                <td style="width: 4%"/>
                <td style="width: 48%">            
                    <div>
                        <table border="0" width="40%" style="overflow-wrap: break-word; word-break:break-all;">
                            <thead>                
                                <th align="left" width="48%">{Localisation::getTranslation(Strings::COMMON_EMAIL)}:<hr/></th>
                            </thead>
                            <tbody>
                                 <tr>
                                    <td style="font-style: italic">
                                        {if $org->getEmail() != ''}
                                            <a href="mailto:{$org->getEmail()}">{$org->getEmail()}</a>
                                        {else}
                                            {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_NO_EMAIL_LISTED)}.
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation(Strings::COMMON_BIOGRAPHY)}:</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getBiography() != ''}
                                            {$org->getBiography()}
                                        {else}
                                            {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_NO_BIOGRAPHY_LISTED)}.
                                        {/if}
                                    </td>  
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation(Strings::COMMON_REGIONAL_FOCUS)}:</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getRegionalFocus() != ''}
                                            {$org->getRegionalFocus()}
                                        {else}
                                            {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_NO_REGIONAL_FOCUS_LISTED)}.
                                        {/if}
                                    </td>  
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    
                        
                
    <p style="margin-bottom: 60px" />         
    <h1 class="page-header">
        {Localisation::getTranslation(Strings::COMMON_BADGES)}
        <small>{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_0)}.</small>

        {if isset($user)}

            {if $isMember || $adminAccess}
                <a href="{urlFor name="org-create-badge" options="org_id.$org_id"}" class='pull-right btn btn-success'>
                    <i class="icon-star icon-white"></i> {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_CREATE_BADGE)}
                </a>
            {/if}
        {/if}
    </h1>  
    <p style="margin-bottom: 40px" />   

{if $org_badges != NULL && count($org_badges) > 0}                
    <table class="table table-striped">
        <thead>            
            <th style="text-align: left">{Localisation::getTranslation(Strings::COMMON_NAME)}</th>
            <th>{Localisation::getTranslation(Strings::COMMON_DESCRIPTION)}</th>

            {if $isMember || $adminAccess}
                <th>{Localisation::getTranslation(Strings::COMMON_EDIT)}</th>
                <th>{Localisation::getTranslation(Strings::COMMON_ASSIGN)}</th>
                <th>{Localisation::getTranslation(Strings::COMMON_DELETE)}</th>
            {/if}
        </thead>
        <tbody>
        {foreach $org_badges as $badge}
            {assign var="badge_id" value=$badge->getId()}
            {assign var="org_id" value=$org->getId()}
            <tr>
                <td style="text-align: left" width="20%">
                    <strong>{$badge->getTitle()}</strong>
                </td>
                <td width="35%">
                    {$badge->getDescription()}
                </td>
                {if ($isMember || $adminAccess) && isset($user)}
                    <td>
                        <a href="{urlFor name="org-edit-badge" options="org_id.$org_id|badge_id.$badge_id"}" class='btn'>
                            <i class="icon-wrench icon-black"></i> {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_EDIT_BADGE)}
                        </a>
                    </td>
                    <td>
                        <a href="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class='btn'>
                            <i class="icon-plus-sign icon-black"></i> {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_ASSIGN_BADGE)}
                        </a>
                    </td>
                    <td>                        
                        <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                            <input type="hidden" name="badge_id" value="{$badge_id}" />
                            <input type="submit" class='btn btn-inverse' name="deleteBadge" value="    {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_DELETE_BADGE)}"
                              onclick="return confirm('{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_1)}')" />                                 
                        </form> 
                        <i class="icon-fire icon-white" style="position:relative; right:44px; top:-40px;"></i> 
                    </td>  
                {/if}
            </tr>
        {/foreach}
        </tbody>
    </table>
<br />
{else}
    <p class="alert alert-info">
        {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_2)}.
    </p>
    <p style="margin-bottom:20px;"></p>
{/if}
      

{if $isMember || $adminAccess}               
     <p style="margin-bottom: 40px" />         
     <h1 class="page-header">
         {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_MEMBERSHIP_REQUESTS)}
         <small>{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_3)}.</small>

             <a href="{urlFor name="org-request-queue" options="org_id.$org_id"}" class='pull-right btn btn-success'>
                 <i class="icon-star icon-white"></i> {Localisation::getTranslation(Strings::COMMON_ADD_USER)}
             </a>
     </h1>                  
     <p style="margin-bottom: 40px" />               

     {if isset($user_list) && count($user_list) > 0}
         <table class="table table-striped">
             <thead>            
                 <th style="text-align: left"><strong>{Localisation::getTranslation(Strings::COMMON_NAME)}</strong></th>
                 <th><strong>{Localisation::getTranslation(Strings::COMMON_BIOGRAPHY)}</strong></th>
                 <th>{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_ACCEPT)}</th>
                 <th>{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_DENY)}</th>
             </thead>
             <tbody>
             {foreach $user_list as $nonMember}
                 <tr>
                     {assign var="user_id" value=$nonMember->getId()}                        
                     {if $nonMember->getDisplayName() != ''}
                         <td style="text-align: left">
                             <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">{$nonMember->getDisplayName()}</a>
                         </td>
                     {/if}
                     <td width="50%">
                         <i>
                         {if $nonMember->getBiography() != ''}
                             {$nonMember->getBiography()}
                         {else}
                             {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_NO_BIOGRAPHY_LISTED)}.
                         {/if}
                         </i>
                     </td>
                     <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                         <input type="hidden" name="user_id" value="{$nonMember->getId()}" />
                         <td>
                             <input type="submit" name="accept" value="    Accept Request" class="btn btn-primary" />
                             <i class="icon-ok-circle icon-white" style="position:relative; right:126px; top:2px;"></i>
                         </td>
                         <td>
                             <input type="submit" name="refuse" value="    Refuse Request" class="btn btn-inverse" />
                             <i class="icon-remove-circle icon-white" style="position:relative; right:126px; top:2px;"></i>
                         </td>
                     </form>
                 </tr>
             {/foreach}
             </tbody>
         </table>   
     {else}
         <p class="alert alert-info">
             {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_5)}.
         </p>
     {/if}
     <p style="margin-bottom: 40px" />               
 {/if}


{if $isMember || $adminAccess}
    <h1 class="page-header">
        {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_ORGANISATION_MEMBERS)}
        <small>{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_6)}.</small>
    </h1>
    {if isset($orgMembers) && count($orgMembers) > 0}
        <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
            <table class="table table-striped">
                <thead>
                    <th>{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_MEMBER_TYPE)}</th>
                    <th>{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_USERNAME)}</th>
                    {if $adminAccess}
                        <th>{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_REMOVE_USER)}</th>
                        <th>{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_ALTER_PERMISSIONS)}</th>
                    {/if}
                </thead>
                <tbody>
                    {foreach $orgMembers as $member}
                        <tr>
                            <td>
                                {if $member['orgAdmin']}
                                    <span class="marker org-admin-marker">{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_ADMINISTRATOR)}</span>
                                {else}
                                    <span class="marker org-member-marker">{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_MEMBER)}</span >
                                {/if}
                            </td>
                            <td>
                                <a href="{urlFor name="user-public-profile" options="user_id.{$member->getId()}"}">{$member->getDisplayName()}</a>
                            </td>
                        </td>
                        {if $adminAccess}
                            <td>
                                <button type="submit" name="revokeUser" value="{$member->getId()}" class="btn btn-inverse" 
                                        onclick="return confirm('{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_7)}')">
                                    <i class="icon-fire icon-white"></i> {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_REVOKE_MEMBERSHIP)}
                                </button>
                            </td>
                            <td>
                                {if $member['orgAdmin']}
                                    <button type="submit" name="revokeOrgAdmin" value="{$member->getId()}" class="btn btn-inverse" 
                                            onclick="return confirm('{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_8)}')">
                                            <i class="icon-fire icon-white"></i> {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_REVOKE_ADMINISTRATOR)}
                                    </button>
                                {else}
                                    <button type="submit" name="makeOrgAdmin" value="{$member->getId()}" class="btn btn-success" 
                                            onclick="return confirm('{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_10)}')"> 
                                            <i class="icon-star icon-white"></i> {Localisation::getTranslation(Strings::COMMON_CREATE_ADMINISTRATOR)}
                                    </button>
                                {/if}
                            </td>
                        {/if}
                    {/foreach}
                </tbody>
            </table>
        </form>
    {else}
        <p class="alert alert-info">{Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_9)}</p>
    {/if}
{/if}

{include file='footer.tpl'}
