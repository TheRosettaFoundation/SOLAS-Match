{include file="header.tpl"}

<h1 class="page-header">
    Manage Badge {$badge->getTitle()}
    <small>Assign/Remove an organisation badge to/from a user</small>
</h1>

{if isset($flash['success'])}
    <div class="alert alert-success">
        <b>Notice:</b> {$flash['success']}
    </div>
{/if}

<h3>Assign Organisation Badge To a User</h3>
<p>Users are identified by their email address</p>

{assign var="badge_id" value=$badge->getBadgeId()}
<form method="post" action="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class="well">
    <label for="email">Enter a user's email address here to assign the badge to them</label>
    <input type='text' name='email' id='email' />

    {if isset($flash['error'])}
        <div class="alert alert-error">
            <b>Warning!</b> {$flash['error']}
        </div>
    {/if}

    <p>
        <button type='submit' class='btn btn-primary' name='submit'>Assign</button>
    </p>
</form>

<h3>Users with this badge</h3>
<p>A list of users who were assigned this badge by an organisation member</p>
{if isset($user_list) && count($user_list) > 0}
    <ul class="unstyled">
        {foreach $user_list as $user}
            <div class="row">
                {if $user->getDisplayName() != ''}
                    {assign var="displayName" value=$user->getDisplayName()}
                {else}
                    {assign var="displayName" value=$user->getEmail()}
                {/if}
                {assign var="user_id" value=$user->getUserId()}
                <li>
                    <div class="span8">
                        <a  href="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                            {$displayName}
                        </a>
                        : {$user->getBiography()}
                    </div>
                    <div class="span4">
                        <form method="post" action="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class="">
                            <input type="hidden" name="user_id" value="{$user->getUserId()}" />
                            <input type="submit" class="btn btn-inverse pull-right" value="Remove" onClick="return confirmPost()" />
                        </form>
                    </div>
                </li>
            </div>
            <br />
        {/foreach}
    </ul>
{else}
    <p class="alert alert-info">
        No users have been assigned this badge yet. To assign a badge enter the email address
        of the user in the field above and click assign.
    </p>
{/if}


{include file="footer.tpl"}
