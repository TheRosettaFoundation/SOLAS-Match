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
    <label for='title'><strong>Badge Title:</strong></label>
    <input type='text' name='title' id='title' />

    <label for="description"><strong>Description:</strong></label>
    <textarea name='description' cols='40' rows='5'></textarea>

    <p>
        <button type='submit' class='btn btn-success' name='submit'>
            <i class="icon-star icon-white"></i> Create Badge
        </button>
    </p>
</form>

{include file="footer.tpl"}
