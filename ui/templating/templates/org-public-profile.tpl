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
    <small> An organisation on SOLAS Match </small>
    {assign var="org_id" value=$org->getId()}
    {if isset($user)}
        {if in_array($user->getUserId(), $org_members)}
            <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class='pull-right btn btn-primary'>
                <i class="icon-wrench icon-white"></i> Edit Organisation Profile
            </a>
        {else}
            <a href="{urlFor name="org-request-membership" options="org_id.$org_id"}" class='pull-right btn btn-primary'>
                <i class="icon-ok-circle icon-white"></i> Request Membership
            </a>
        {/if}
    {/if}
    </h1></div>
{/if}

{if $org->getName() != ''}
    <h3>Organisation Name</h3>
    <p>{$org->getName()}</p>
    <p style="margin-bottom:20px;"></p>
{/if}

{if $org->getHomePage() != '' && $org->getHomePage() != 'http://'}
    <h3>Home Page</h3>
    <p><a target="_blank" href='{$org->getHomePage()}'>{$org->getHomePage()}</a></p>
    <p style="margin-bottom:20px;"></p>
{/if}

{if $org->getBiography() != ''}
    <h3>Biography</h3>
    <p>{$org->getBiography()}</p>
    <p style="margin-bottom:20px;"></p>
{/if}

<h3>
    Organisation Badges
    
    {if isset($user)}
        {if in_array($user->getUserId(), $org_members)}
            <a href="{urlFor name="org-create-badge" options="org_id.$org_id"}" class='pull-right btn btn-success'>
                <i class="icon-star icon-white"></i> Create Badge
            </a>
        {/if} 
        <p style="margin-bottom:20px;"></p>
    {/if}
</h3>
{if $org_badges != NULL && count($org_badges) > 0}
    {foreach $org_badges as $badge}
        <p>
            {if isset($user)}
                {if in_array($user->getUserId(), $org_members)}
                    {assign var="badge_id" value=$badge->getId()}
                    {assign var="org_id" value=$org->getId()}
                    
                    <div class="pull-right">
                     <i class="icon-fire icon-white" style="position:relative; right:-190px; top:1px;"></i>   
                     <form method="post" class="pull-right" action="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                        {* {if isset($private_access)} *}
                            <input type="hidden" name="badge_id" value="{$badge_id}" />
                            <input type="submit" class='btn btn-inverse' name="deleteBadge" value="    Delete"
                              onclick="return confirm('Are you sure you want to delete this badge?')" />                                 
                        {* {/if} *}
                    </form> 
                    
                    <a href="{urlFor name="org-edit-badge" options="org_id.$org_id|badge_id.$badge_id"}" class='btn'>
                        <i class="icon-wrench icon-black"></i> Edit
                    </a>&nbsp;&nbsp;
                    <a href="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class='btn'>
                        <i class="icon-plus-sign icon-black"></i> Assign
                    </a>&nbsp;&nbsp;
                    </div>
                {/if}
            {/if}
            <p>
                <b>Name:</b><br/>
                {$badge->getTitle()}
            </p>
            <p><b>Description:</b><br/>
                {$badge->getDescription()}
            </p>
        </p>
        <hr>
    {/foreach}
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
        <a href="{urlFor name="org-request-queue" options="org_id.$org_id"}" class="btn btn-primary">
            <i class="icon-list icon-white"></i> View Membership Requests
        </a>
    {/if}
{/if}

{include file='footer.tpl'}
