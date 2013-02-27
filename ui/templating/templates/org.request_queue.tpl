{include file="header.tpl"}

    {assign var="org_id" value=$org->getId()}
    <h1 class="page-header">{$org->getName()}<small> Add a user to your organisation.</small></h1>

    <h3>Add a User as an Organisation Member</h3>
    <p>Enter the User's email to add them as a member of this organisation</p>

    {if isset($flash['error'])}
        <div class="alert alert-error">{$flash['error']}</div>
    {/if}

    {if isset($flash['success'])}
        <div class="alert alert-success">{$flash['success']}</div>
    {/if}

    <form class="well" method="post" action="{urlFor name="org-request-queue" options="org_id.$org_id"}">
        <label for="email"><strong>User's email address:</strong></label>
        <input type="text" name="email" />
        <p>
            <input type="submit" value="    Add User" class="btn btn-primary" />
            <i class="icon-plus-sign icon-white" style="position:relative; right:88px; top:2px;"></i>
        </p>
    </form>

{include file="footer.tpl"}
