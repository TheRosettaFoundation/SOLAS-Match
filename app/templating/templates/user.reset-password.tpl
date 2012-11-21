{include file="header.tpl"}

{include file="handle-flash-messages.tpl"}

<div class="page-header">
    <h1>Reset User Password</h1>
</div>

<form class="well" action="{urlFor name="password-reset-request"}" method="post">
    <p>
        To reset your password enter the email address you registered with below.
        When you click the button below you will receive an email to that email
        address with a link to a page where you can reset your password. 
    </p>
    <label for="email">
        <h2>
            Email Address
        </h2>
    </label>
    <p><input type="text" name="email_address" id="email_address" /></p>
    <input type="submit" name="password_reset" value="    Send Request" class="btn btn-primary"/>
    <i class="icon-share-alt icon-white" style="position:relative; right:116px; top:2px;"></i>     
</form>

{include file="footer.tpl"}
