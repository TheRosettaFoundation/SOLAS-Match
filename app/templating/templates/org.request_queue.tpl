{include file="header.tpl"}

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
        <p>
            <a href="{urlFor name="org-process-request" options="org_id.$org_id|user_id.$user_id|accept.true"}" class="btn btn-primary">
                Accept Request
            </a>
            <a href="{urlFor name="org-process-request" options="org_id.$org_id|user_id.$user_id|accept.false"}" class="btn btn-primary">
                Refuse Request
            </a>
        </p>

    {/foreach}
{else}
    <div class="alert alert-info">There are no current membership requests for this organisation</div>
{/if}

<h3>Add a User as an Organisation Member</h3>
<p>Enter the User's email to add them as a member of this organisation</p>

{if isset($flash['error'])}
    <div class="alert alert-error">{$flash['error']}</div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">{$flash['success']}</div>
{/if}
    
<form class="well" method="post" action="{urlFor name="org-request-queue" options="org_id.$org_id"}">
    <label for="email">User's email address:</label>
    <input type="text" name="email" />

    <p>
        <input type="submit" value="Add User" class="btn btn-primary" />
    </p>
</form>

{include file="footer.tpl"}
