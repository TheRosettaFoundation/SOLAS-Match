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
    <div class='page-header'><h1>
    {if $org->getName() != ''}
        {$org->getName()}
    {else}
        Organisation Profile
    {/if}
    <small>An organisation on SOLAS Match.</small>
    {assign var="org_id" value=$org->getId()}
    {if isset($user)}
        {if in_array($user->getUserId(), $org_members)}
            <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class='pull-right btn btn-primary'>
                <i class="icon-wrench icon-white"></i> Edit Organisation Details
            </a>
        {else}
            <a href="{urlFor name="org-request-membership" options="org_id.$org_id"}" class='pull-right btn btn-primary'>
                <i class="icon-ok-circle icon-white"></i> Request Membership
            </a>
        {/if}
    {/if}
    </h1></div>
{/if}

<div class="well">
    <table border="0" width="100%">
        <thead>
        <th align="left" width="48%">Biography:<hr/></th>
        </thead>
        <tbody>
            <tr valign="top">
                <td>
                    <i>
                    {if $org->getBiography() != ''}
                        {$org->getBiography()}
                    {else}
                        This organisation has no biography listed.
                    {/if}
                    </i>
                </td>
            </tr>    
            <tr><td><hr /></td></tr>
            <tr>
                <td>
                    {if $org->getHomePage() != '' && $org->getHomePage() != 'http://'}
                        <b>Home Page:</b> <a target="_blank" href='{$org->getHomePage()}'>{$org->getHomePage()}</a>
                    {/if}
                </td>
            </tr>
        </tbody>
    </table>
</div>  
         


    
           
{*
{assign var="org_id" value=$org->getId()}
<h1 class="page-header">{$org->getName()}<small> A list of membership requests</small></h1>
{if isset($user_list) && count($user_list) > 0}
    {foreach $user_list as $user}
        {assign var="user_id" value=$user->getUserId()}
        {assign var="org_id" value=$org->getId()}
        {if $user->getDisplayName() != ''}
            <h3>{$user->getDisplayName()}</h3>
        {else}
            <h3>User {$user->getUserId()}</h3>
        {/if}
        <p>{$user->getBiography()}</p>
        <p>View their <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">profile</a></p>
        <form method="post" action="{urlFor name="org-request-queue" options="org_id.$org_id"}">
            <input type="hidden" name="user_id" value="{$user->getUserId()}" />
            <input type="submit" name="accept" value="    Accept Request" class="btn btn-primary" />
            <input type="submit" name="refuse" value="    Refuse Request" class="btn btn-inverse" />
            <i class="icon-ok-circle icon-white" style="position:relative; right:260px; top:2px;"></i>
            <i class="icon-remove-circle icon-white" style="position:relative; right:145px; top:2px;"></i>
        </form>

    {/foreach}
{else}
    <div class="alert alert-info">There are no current membership requests for this organisation</div>
{/if}
*}
                
                
<p style="margin-bottom: 60px" />         
<h1 class="page-header">
    Badges
    <small>Overview of badges created by this organisation.</small>
    
    {if isset($user)}
        {if in_array($user->getUserId(), $org_members)}
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
                <th style="text-align: left"><b>Name</b></th>
                <th><b>Description</b></th>
            </thead>
            <tbody>
            {foreach $org_badges as $badge}
                {assign var="badge_id" value=$badge->getId()}
                {assign var="org_id" value=$org->getId()}
                <tr>
                    <td style="text-align: left" width="20%">
                        <b>{$badge->getTitle()}</b>
                    </td>
                    <td width="35%">
                        {$badge->getDescription()}
                    </td>
                    {if isset($user)}
                        {if in_array($user->getUserId(), $org_members)}
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
                    {/if}
                </tr>
            {/foreach}
            </tbody>
        </table>
    <br />
{else}
    <br />
    <p class="alert alert-info">
        There are no badges associated with this organisation.
        {if isset($user)}
            {if in_array($user->getUserId(), $org_members)}
                Add organisation badges <a href="{urlFor name="org-create-badge" options="org_id.$org_id"}">here</a>.
            {/if}
        {/if}
    </p>
    <p style="margin-bottom:20px;"></p>
{/if}    


      
{if isset($user)}
   {if in_array($user->getUserId(), $org_members)}               
        <p style="margin-bottom: 40px" />         
        <h1 class="page-header">
            Membership Requests
            <small>Overview of users who have requested membership.</small>
        </h1>                  
        <p style="margin-bottom: 40px" />               
                
                
        <table class="table table-striped">
            <thead>            
                <th style="text-align: left"><b>Name</b></th>
                <th><b>Biography</b></th>
                <th/>
                <th/>
            </thead>
            <tbody>
                {if isset($user_list) && count($user_list) > 0}
                    {foreach $user_list as $user}
                        <tr>
                            {assign var="user_id" value=$user->getUserId()}                        
                            {if $user->getDisplayName() != ''}
                                <td style="text-align: left">
                                    <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">{$user->getDisplayName()}</a>
                                </td>
                            {/if}
                            <td width="50%">
                                <i>
                                {if $user->getBiography() != ''}
                                    {$user->getBiography()}
                                {else}
                                    No biography has been added.
                                {/if}
                                </i>
                            </td>
                            <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                                <input type="hidden" name="user_id" value="{$user->getUserId()}" />
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
                {else}
                    <tr>
                        <td colspan="2">
                            <i>There are no current membership requests for this organisation</i>
                        </td>
                    </tr>
                {/if}
            </tbody>
        </table>   
    {/if}
{/if}


{if isset($user)}
    {if in_array($user->getUserId(), $org_members)}
        <a href="{urlFor name="org-request-queue" options="org_id.$org_id"}" class="btn btn-primary">
            <i class="icon-list icon-white"></i> View Membership Requests
        </a>
    {/if}
{/if}

{include file='footer.tpl'}
