{include file="header.tpl"}

<h1 class="page-header">
    Create an Organisation
    <small>
        Create your own organisation
    </small>
</h1>

{if isset($flash['error'])}
    <div class="alert alert-error">
        <p><strong>Warning!</strong> {$flash['error']}</p>
    </div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">
        <p>{$flash['success']}</p>
    </div>
{/if}


<form method="post" action="{urlFor name="create-org"}" class="well">
    <label for="name">Organisation Name:</label>
    <input type="text" name="name" />

    <label for="home_page">Home Page:</label>
    <input type="text" name="home_page" value="http://" />

    <label for="bio">Biography:</label>
    <textarea name="bio" cols="40" rows="5" placeholder="Explain what the purpose of your organisation is">
    </textarea>

    <p>
        <button type="submit" class="btn btn-primary">Create</button>
    </p>
</form>

{include file="footer.tpl"}
