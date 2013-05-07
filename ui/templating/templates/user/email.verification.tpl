{include file="header.tpl"}

<h1 class="page-header">
    Email Verification <small>You can complete registration here</small>
</h1>

<p>
    Welcome to SOLAS Match. Once you click the button below you will have access to
    the site. You can log in any time by going to the login page (link on the right
    of the status bar). Once logged in you can view tasks on the task stream. If
    there is a task you are interested in you can claim it to start working on it.
</p>

<p>
    If you are an organisation representative then you can request membership of your
    organisation by visiting the organisation's public profile. If your organisation
    does not yet exist on the system you can create it on you public profile.
</p>

<form method='post' action="{urlFor name="email-verification" options="uuid.$uuid"}">
    <input type="submit" name="verify" class="btn btn-success" value="Finish Registration" />
</form>

{include file="footer.tpl"}
