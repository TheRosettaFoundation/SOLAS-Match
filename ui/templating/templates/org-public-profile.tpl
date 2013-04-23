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
                Organisation Profile
            {/if}
            <small>An organisation on SOLAS Match.</small>
            {assign var="org_id" value=$org->getId()}
            {if isset($user)}
                {if $isMember}
<!--                    do not add ||$adminAccess-->
                    <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class='pull-right btn btn-primary'>
                        <i class="icon-wrench icon-white"></i> Edit Organisation Details
                    </a>
                {else}
                    <a href="{urlFor name="org-request-membership" options="org_id.$org_id"}" class='pull-right btn btn-primary'>
                        <i class="icon-ok-circle icon-white"></i> Request Membership
                    </a>
                {/if}
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
                                <th align="left">Home Page:<hr/></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getHomePage() != 'http://'}
                                            <a href="{$org->getHomePage()}">{$org->getHomePage()}</a>
                                        {else}
                                            No home page listed.
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>Address:</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getAddress() != ''}
                                            {$org->getAddress()}
                                        {else}
                                            No address listed.
                                        {/if}
                                    </td>  
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>City:</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getCity() != ''}
                                            {$org->getCity()}
                                        {else}
                                            No city listed.
                                        {/if}
                                    </td>  
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>Country:</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getCountry() != ''}
                                            {$org->getCountry()}
                                        {else}
                                            No country listed.
                                        {/if}
                                    </td>  
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
                <td style="width: 4%"/>
                <td style="width: 48%">            
                    <div class="pull-right">
                        <table border="0" width="40%" style="overflow-wrap: break-word; word-break:break-all; table-layout: fixed;">
                            <thead>                
                                <th align="left" width="48%">E-Mail:<hr/></th>
                            </thead>
                            <tbody>
                                 <tr>
                                    <td style="font-style: italic">
                                        {if $org->getEmail() != ''}
                                            <a href="mailto:{$org->getEmail()}">{$org->getEmail()}</a>
                                        {else}
                                            No e-mail listed.
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>Biography:</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getBiography() != ''}
                                            {$org->getBiography()}
                                        {else}
                                            No biography listed.
                                        {/if}
                                    </td>  
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 40px"/>
                                </tr>
                                <tr valign="top">
                                    <td colspan="1" >
                                        <strong>Regional Focus:</strong><hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-style: italic">
                                        {if $org->getRegionalFocus() != ''}
                                            {$org->getRegionalFocus()}
                                        {else}
                                            No regional focus listed.
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
        Badges
        <small>Overview of badges created by this organisation.</small>

        {if isset($user)}

            {if $isMember||$adminAccess  }
                <a href="{urlFor name="org-create-badge" options="org_id.$org_id"}" class='pull-right btn btn-success'>
                    <i class="icon-star icon-white"></i> Create Badge
                </a>
            {/if}
        {/if}
    </h1>  
    <p style="margin-bottom: 40px" />   

{if $org_badges != NULL && count($org_badges) > 0}                
    <table class="table table-striped">
        <thead>            
            <th style="text-align: left">Name</th>
            <th>Description</th>

            {if $isMember||$adminAccess  }
                <th>Edit</th>
                <th>Assign</th>
                <th>Delete</th>
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
                {if ($isMember||$adminAccess  ) && isset($user)}
                    <td>
                        <a href="{urlFor name="org-edit-badge" options="org_id.$org_id|badge_id.$badge_id"}" class='btn'>
                            <i class="icon-wrench icon-black"></i> Edit Badge
                        </a>
                    </td>
                    <td>
                        <a href="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class='btn'>
                            <i class="icon-plus-sign icon-black"></i> Assign Badge
                        </a>
                    </td>
                    <td>                        
                        <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                            <input type="hidden" name="badge_id" value="{$badge_id}" />
                            <input type="submit" class='btn btn-inverse' name="deleteBadge" value="    Delete Badge"
                              onclick="return confirm('Are you sure you want to delete this badge?')" />                                 
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
        There are no badges associated with this organisation.
    </p>
    <p style="margin-bottom:20px;"></p>
{/if}
      

{if $isMember||$adminAccess  }               
     <p style="margin-bottom: 40px" />         
     <h1 class="page-header">
         Membership Requests
         <small>Overview of users who have requested membership.</small>

             <a href="{urlFor name="org-request-queue" options="org_id.$org_id"}" class='pull-right btn btn-success'>
                 <i class="icon-star icon-white"></i> Add User
             </a>
     </h1>                  
     <p style="margin-bottom: 40px" />               

     {if isset($user_list) && count($user_list) > 0}
         <table class="table table-striped">
             <thead>            
                 <th style="text-align: left"><strong>Name</strong></th>
                 <th><strong>Biography</strong></th>
                 <th>Accept</th>
                 <th>Deny</th>
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
                             No biography has been added.
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
             There are no membership requests associated with this organisation.
         </p>
     {/if}
     <p style="margin-bottom: 40px" />               
 {/if}


{if $isMember||$adminAccess}
    <h1 class="page-header">
        Organisation Members
        <small>A list of users that are a member of this organisation.</small>
    </h1>
    {if isset($orgMembers) && count($orgMembers) > 0}
        <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
            <table class="table table-striped">
                <thead>
                    <th style="width: 33%">Username</th>
                    <th style="width: 33%">Biography</th>
                    {if $adminAccess}
                        <th>Remove User</th>
                    {/if}
                </thead>
                <tbody>
                    {foreach $orgMembers as $member}
                        <tr>
                            <td>
                                <a href="{urlFor name="user-public-profile" options="user_id.{$member->getId()}"}">{$member->getDisplayName()}</a>
                            </td>
                            <td style="font-style: italic">
                                {if $member->getBiography() != ''}
                                    {$member->getBiography()}
                                {else}
                                    No biography listed.
                                {/if}
                            </td>
                        </td>
                        {if $adminAccess}
                            <td>
                                <button type="submit" name="revokeUser" value="{$member->getId()}" class="btn btn-inverse" 
                                        onclick="return confirm('Are you sure you want to revoke membership from this user?')">
                                    <i class="icon-fire icon-white"></i> Revoke Membership
                                </button>
                            </td>
                        {/if}
                    {/foreach}
                </tbody>
            </table>
        </form>
    {else}
        <p class="alert alert-info">This organisation does not have any members</p>
    {/if}
{/if}

{include file='footer.tpl'}
