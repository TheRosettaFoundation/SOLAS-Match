{include file="header.tpl"}

<h1 class="page-header">
    Create Organisation Badge
    <small>Create a badge for your organisation</small>
</h1>

{if isset($flash['error'])}
    <div class="alert alert-error">
        <p><strong>Warning! </strong>{$flash['error']}</p>
    </div>
{/if}

<form method="post" action="{urlFor name="org-create-badge" options="org_id.$org_id"}" class="well">
    <label for='title'>Badge Title</label>
    <input type='text' name='title' id='title' />

    <label for="description">Description</label>
    <textarea name='description' cols='40' rows='5'></textarea>

    <p>
        <button type='submit' class='btn btn-primary' name='submit'>Create</button>
    </p>
</form>

{include file="footer.tpl"}
