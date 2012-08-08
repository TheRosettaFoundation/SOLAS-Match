{include file="header.tpl"}

<h1 class="page-header">{$org->getName()}<small> A list of membership requests</small></h1>
{if isset($user_list) && count($user_list) > 0}
    {foreach $user_list as $user}
        {if $user->getDisplayName() != ''}
            <h3>{$user->getDisplayName()}</h3>
        {else}
            <h3>User {$user->getUserId()}</h3>
        {/if}
        <p>{$user->getBiography()}</p>
        {assign var="user_id" value=$user->getUserId()}
        <p>View their <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">profile</a></p>
    {/foreach}
{/if}

{include file="footer.tpl"}
