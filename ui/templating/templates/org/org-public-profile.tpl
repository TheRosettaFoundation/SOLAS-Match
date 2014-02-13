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
                {Localisation::getTranslation('common_organisation_profile')}
            {/if}
            <small>{sprintf(Localisation::getTranslation('org_public_profile_an_organisation_of'), Settings::get("site.name"))}</small>
            {assign var="org_id" value=$org->getId()}
            {if isset($user)}
                <div class="pull-right">
                    {if $isMember || $adminAccess}
                            <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class='btn btn-primary'>
                            <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('org_public_profile_edit_organisation_details')}
                        </a>
                    {/if}
                    {if !$isMember}
                        <a href="{urlFor name="org-request-membership" options="org_id.$org_id"}" class='btn btn-primary'>
                            <i class="icon-ok-circle icon-white"></i> {Localisation::getTranslation('org_public_profile_request_membership')}
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
                                <th align="left">{Localisation::getTranslation('common_home_page')}<hr/></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getHomePage() != 'http://' && $org->getHomePage() != ''}
                                            <a href="{$org->getHomePage()}">{$org->getHomePage()}</a>
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_home_page_listed')}
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('common_address')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getAddress() != ''}
                                            {TemplateHelper::uiCleanseNewlineAndTabs($org->getAddress())}
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_address_listed')}
                                        {/if}
                                    </td>  
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('common_city')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getCity() != ''}
                                            {$org->getCity()}
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_city_listed')}
                                        {/if}
                                    </td>  
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('common_country')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getCountry() != ''}
                                            {$org->getCountry()}
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_country_listed')}
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
                                <th align="left" width="48%">{Localisation::getTranslation('common_email')}<hr/></th>
                            </thead>
                            <tbody>
                                 <tr>
                                    <td style="font-style: italic">
                                        {if $org->getEmail() != ''}
                                            <a href="mailto:{$org->getEmail()}">{$org->getEmail()}</a>
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_email_listed')}
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('common_biography')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getBiography() != ''}
                                            {TemplateHelper::uiCleanseNewlineAndTabs($org->getBiography())}
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_biography_listed')}
                                        {/if}
                                    </td>  
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>{Localisation::getTranslation('common_regional_focus')}</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getRegionalFocus() != ''}
                                            {$org->getRegionalFocus()}
                                        {else}
                                            {Localisation::getTranslation('org_public_profile_no_regional_focus_listed')}
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
        {Localisation::getTranslation('common_badges')}
        <small>{Localisation::getTranslation('org_public_profile_0')}</small>

        {if isset($user)}

            {if $isMember || $adminAccess}
                <a href="{urlFor name="org-create-badge" options="org_id.$org_id"}" class='pull-right btn btn-success'>
                    <i class="icon-star icon-white"></i> {Localisation::getTranslation('org_public_profile_create_badge')}
                </a>
            {/if}
        {/if}
    </h1>  
    <p style="margin-bottom: 40px" />   

{if $org_badges != NULL && count($org_badges) > 0}                
    <table class="table table-striped">
        <thead>            
            <th style="text-align: left">{Localisation::getTranslation('common_name')}</th>
            <th>{Localisation::getTranslation('common_description')}</th>

            {if $isMember || $adminAccess}
                <th>{Localisation::getTranslation('common_edit')}</th>
                <th>{Localisation::getTranslation('common_assign')}</th>
                <th>{Localisation::getTranslation('common_delete')}</th>
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
                            <i class="icon-wrench icon-black"></i> {Localisation::getTranslation('org_public_profile_edit_badge')}
                        </a>
                    </td>
                    <td>
                        <a href="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class='btn'>
                            <i class="icon-plus-sign icon-black"></i> {Localisation::getTranslation('common_assign_badge')}
                        </a>
                    </td>
                    <td>                        
                        <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                            <input type="hidden" name="badge_id" value="{$badge_id}" />
                            <input type="submit" class='btn btn-inverse' name="deleteBadge" value="    {Localisation::getTranslation('org_public_profile_delete_badge')}"
                              onclick="return confirm('{Localisation::getTranslation('org_public_profile_1')}')" />                                 
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
        {Localisation::getTranslation('org_public_profile_2')}
    </p>
    <p style="margin-bottom:20px;"></p>
{/if}
      

{if $isMember || $adminAccess}               
     <p style="margin-bottom: 40px" />         
     <h1 class="page-header">
         {Localisation::getTranslation('org_public_profile_membership_requests')}
         <small>{Localisation::getTranslation('org_public_profile_3')}</small>

             <a href="{urlFor name="org-request-queue" options="org_id.$org_id"}" class='pull-right btn btn-success'>
                 <i class="icon-star icon-white"></i> {Localisation::getTranslation('common_add_user')}
             </a>
     </h1>                  
     <p style="margin-bottom: 40px" />               

     {if isset($membershipRequestUsers) && count($membershipRequestUsers) > 0}
         <table class="table table-striped">
             <thead>            
                 <th style="text-align: left"><strong>{Localisation::getTranslation('common_name')}</strong></th>
                 <th><strong>{Localisation::getTranslation('common_biography')}</strong></th>
                 <th>{Localisation::getTranslation('org_public_profile_accept')}</th>
                 <th>{Localisation::getTranslation('org_public_profile_deny')}</th>
             </thead>
             <tbody>
             {foreach $membershipRequestUsers as $nonMember}
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
                             {TemplateHelper::uiCleanseNewlineAndTabs($nonMember->getBiography())}
                         {else}
                             {Localisation::getTranslation('org_public_profile_no_biography_listed')}
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
             {Localisation::getTranslation('org_public_profile_5')}
         </p>
     {/if}
     <p style="margin-bottom: 40px" />               
 {/if}


{if $isMember || $adminAccess}
    <h1 class="page-header">
        {Localisation::getTranslation('org_public_profile_organisation_members')}
        <small>{Localisation::getTranslation('org_public_profile_6')}</small>
    </h1>
    {if isset($orgMembers) && count($orgMembers) > 0}
        <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
            <table class="table table-striped">
                <thead>
                    <th>{Localisation::getTranslation('org_public_profile_member_type')}</th>
                    <th>{Localisation::getTranslation('org_public_profile_username')}</th>
                    {if $adminAccess}
                        <th>{Localisation::getTranslation('org_public_profile_remove_user')}</th>
                        <th>{Localisation::getTranslation('org_public_profile_alter_permissions')}</th>
                    {/if}
                </thead>
                <tbody>
                    {foreach $orgMembers as $member}
                        <tr>
                            <td>
                                {if $member['orgAdmin']}
                                    <span class="marker org-admin-marker">{Localisation::getTranslation('org_public_profile_administrator')}</span>
                                {else}
                                    <span class="marker org-member-marker">{Localisation::getTranslation('org_public_profile_member')}</span >
                                {/if}
                            </td>
                            <td>
                                <a href="{urlFor name="user-public-profile" options="user_id.{$member->getId()}"}">{$member->getDisplayName()}</a>
                            </td>
                        {if $adminAccess}
                            <td>
                                <button type="submit" name="revokeUser" value="{$member->getId()}" class="btn btn-inverse" 
                                        onclick="return confirm('{Localisation::getTranslation('org_public_profile_7')}')">
                                    <i class="icon-fire icon-white"></i> {Localisation::getTranslation('org_public_profile_revoke_membership')}
                                </button>
                            </td>
                            <td>
                                {if $member['orgAdmin']}
                                    <button type="submit" name="revokeOrgAdmin" value="{$member->getId()}" class="btn btn-inverse" 
                                            onclick="return confirm('{Localisation::getTranslation('org_public_profile_8')}')">
                                            <i class="icon-fire icon-white"></i> {Localisation::getTranslation('org_public_profile_revoke_administrator')}
                                    </button>
                                {else}
                                    <button type="submit" name="makeOrgAdmin" value="{$member->getId()}" class="btn btn-success" 
                                            onclick="return confirm('{Localisation::getTranslation('org_public_profile_10')}')"> 
                                            <i class="icon-star icon-white"></i> {Localisation::getTranslation('common_create_administrator')}
                                    </button>
                                {/if}
                            </td>
                        {/if}
                      </tr>
                    {/foreach}
                </tbody>
            </table>
        </form>
    {else}
        <p class="alert alert-info">{Localisation::getTranslation('org_public_profile_9')}</p>
    {/if}
{/if}

{include file='footer.tpl'}
