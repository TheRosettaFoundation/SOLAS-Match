{include file="header.tpl"}

    <h1 class="page-header">
        Manage Badge {$badge->getTitle()}
        <small>Assign/Remove an organisation badge to/from a user</small>
    </h1>

    {if isset($flash['success'])}
        <div class="alert alert-success">
            <strong>Notice:</strong> {$flash['success']}
        </div>
    {/if}

    <h3>Assign Organisation Badge To a User</h3>
    <p>Users are identified by their email address</p>

    {assign var="badge_id" value=$badge->getId()}
    <form method="post" action="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class="well">
        <label for="email">Enter a user's email address here to assign the badge to them</label>
        <input type='text' name='email' id='email' />

        {if isset($flash['error'])}
            <div class="alert alert-error">
                <strong>Warning!</strong> {$flash['error']}
            </div>
        {/if}
        <p>
            <button type='submit' class='btn btn-primary' name='submit'>
                <i class="icon-plus-sign icon-white"></i> Assign
            </button>
        </p>
    </form>

    <h3>Users with this badge</h3>
    <p>A list of users who were assigned this badge by an organisation member:</p>
    {if isset($user_list) && count($user_list) > 0}
        <ul class="unstyled">
            {foreach $user_list as $user}
                <div class="row">
                    {if $user->getDisplayName() != ''}
                        {assign var="displayName" value=$user->getDisplayName()}
                    {else}
                        {assign var="displayName" value=$user->getEmail()}
                    {/if}
                    {assign var="user_id" value=$user->getId()}
                    <li>
                        <div class="span8">
                            <h4>Display Name:</h4>
                            <a  href="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                {$displayName}
                            </a>
                        </div>
                        <div class="right">
                            <form method="post" action="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class="">
                                <input type="hidden" name="user_id" value="{$user->getId()}" />
                                <input type="hidden" value="Remove" onClick="return confirmPost()" />
                                <a href="#" onclick="this.parentNode.submit()" class="pull-right btn btn-inverse">
                                    <i class="icon-fire icon-white"></i> Remove Badge
                                </a>
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