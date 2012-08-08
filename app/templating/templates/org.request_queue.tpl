{include file="header.tpl"}

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

{include file="footer.tpl"}
