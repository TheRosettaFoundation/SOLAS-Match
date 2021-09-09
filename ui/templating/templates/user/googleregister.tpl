
{include file="header.tpl"}
<style>
 #gregisterform label {
  color:#143878 !important;
}
 #gregisterform .check {
  color:#333 !important;
}
#gregisterform label.error {
		
		width: auto;
	
	}
#gregisterform .error{
    color:#F00 !important;
}
 .required:after {
    content:" *";
    color: red;
  }
.center {
  margin: 0;
  position: absolute;
  top: 50%;
  left: 50%;
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
}

.register_header1{
  text-align:right;
  color:#143878 !important;
}

.register_header2{
  text-align:left;
  color:#e8991c !important;
}

.span{
  text-align:center;
}
	
</style>
<div class="container-fluid">
  <div class="row-fluid">
  <br/>
    <div class="span4">
      
    </div>
    <div class="span">
    
    
<h1><span class="register_header2">Welcome {$firstname}</span></h1>
  

</div>
    </div>
 

{include file="handle-flash-messages.tpl"}
{if isset($error)}
    <div class="alert alert-error">
        <strong>{Localisation::getTranslation('common_error')}:</strong> {$error}
    </div>
{/if}

{if isset($warning)}
    <div class="alert">
        <strong>{Localisation::getTranslation("common_warning")}:</strong> {$warning}
    </div>
{/if}

<div class="container-fluid">
  <div class="row-fluid">
    <div class="span4">
      
    </div>
    <div class="span4">
    
    <form method="post" id="gregisterform" action="{urlFor name="googleregister"}" class="well" accept-charset="utf-8">
            <label for="username" class="required "><strong>Username</strong></label>
            <input type="text" name="username" id="username" placeholder="Username" value="{$username}" required/>
            <label for="first_name" class="required "><strong>First name</strong></label>
            <input type="text" name="first_name" id="first_name" placeholder="First name" value="{$firstname}" required/>
            <label for="last_name" class="required"><strong>Last name</strong></label>
            <input type="text" name="last_name" id="last_name" placeholder="Last name" value="{$lastname}" required/>
            <label for="email" class="required"><strong>Email</strong></label>
            <input type="text" name="email" id="email" placeholder="Your email" value="{$email}"/>
            <input type="hidden" name="user_id" id="user_id"  value="{$user_id}"/>
           
            <label class="checkbox required check">
            <input name="age_consent" id="age_consent" type="checkbox"> I confirm I am over the age of 18 <i class="icon-question-sign" id="tool" data-toggle="tooltip" title="If you are under 18 years of age, you can't work with us. Our child protection policy prevents it"></i>
            </label>
           
            <label class="checkbox required check">
            <input name="conduct_consent" id="conduct_consent" type="checkbox"> I agree to the <a href="https://www.translatorswithoutborders.org/volunteer/volunteer-translators/translators-code-of-conduct/" target="_blank">TWB Code of Conduct for translators</a> and the <a href="https://translatorswithoutborders.org/privacy-policy/?__hstc=6552685.50947dd5d22eb95562a1c48227dc4cde.1624948951679.1624948951679.1624948951679.1&__hssc=6552685.1.1624948951679&__hsfp=1528584403" target="_blank">TWB Privacy policy</a>
            </label>
             <label class="checkbox check">
            <input name="newsletter_consent" id="newsletter_consent"  type="checkbox"> Subscribe to the TWB email newsletter.
            <small>You can unsubscribe at any time</small>
            </label>
            <p class="reg_btn ">
                <button type="submit" onclick="return compareEmails();" class="btn btn-primary " name="submit">
                     Create Account
                </button>
            </p>
    </form>
     
  


      
    </div>
  </div>
</div>

      


{include file="footer.tpl"}
