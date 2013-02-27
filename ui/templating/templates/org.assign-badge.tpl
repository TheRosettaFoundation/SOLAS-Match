{include file="header.tpl"}

<h1 class="page-header">
    Assign a Badge
    <small>Assign an organisation badge to a user</small>
</h1>

<h3>Assign Organisation Badge To a User</h3>
<p>Users are identified by their email address</p>

{assign var="badge_id" value=$badge->getId()}
badge id: {$badge_id}<br/>
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

{include file="footer.tpl"}
