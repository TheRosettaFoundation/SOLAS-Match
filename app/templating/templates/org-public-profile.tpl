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
    <div class='page-header'><h1>
    {if $org->getName() != ''}
        {$org->getName()}
    {else}
        Organisation Profile
    {/if}
    <small> An organisation on SOLAS Match </small>
    {assign var="org_id" value=$org->getId()}
    {if in_array($user->getUserId(), $org_members)}
        <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class='pull-right btn btn-primary'>Edit Profile</a>
    {else}
        <a href="{urlFor name="org-request-membership" options="org_id.$org_id"}" class='pull-right btn btn-primary'>Request Membership</a>
    {/if}
    </h1></div>
{/if}

<h3>Organisation Name</h3>
<p>{$org->getName()}</p>

<h3>Home Page</h3>
<p><a href='{$org->getHomePage()}'>{$org->getHomePage()}</a></p>

<h3>Biography</h3>
<p>{$org->getBiography()}</p>


{include file='footer.tpl'}
