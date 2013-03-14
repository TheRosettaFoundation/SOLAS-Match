{include file="header.tpl"}

<h1 class="page-header">
    Create an Organisation
    <small>
        Create your own organisation
    </small>
</h1>

<form method="post" action="{urlFor name="org-dashboard"}" class="well">
    <label for="name"><strong>Organisation Name:</strong></label>
    <input type="text" name="name" />

    <label for="home_page"><strong>Home Page:</strong></label>
    <input type="text" name="home_page" value="http://" />

    <label for="bio"><strong>Biography:</strong></label>
    <textarea name="bio" cols="40" rows="5" placeholder="Explain what the purpose of your organisation is."></textarea>

    <p>
        <button type="submit" name="submit" value="createOrg" class="btn btn-success">
            <i class="icon-star icon-white"></i> Create Organisation
        </button>            
    </p>
</form>

{include file="footer.tpl"}