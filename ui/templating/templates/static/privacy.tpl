{include file="header.tpl"}
<div class="page-header" style="text-align: center">
    <h1>{Localisation::getTranslation(Strings::PRIVACY_PRIVACY_POLICY)}</h1>
</div>  
<div>
{Localisation::getTranslation(Strings::PRIVACY_0)} <a href="http://trommons.org">trommons.org</a> {Localisation::getTranslation(Strings::PRIVACY_1)}.<br><br>

<b>{Localisation::getTranslation(Strings::PRIVACY_PERSONAL_IDENTIFICATION_INFORMATION)}</b><br><br>

{Localisation::getTranslation(Strings::PRIVACY_2)}. {Localisation::getTranslation(Strings::PRIVACY_3)}. {Localisation::getTranslation(Strings::PRIVACY_4)},<br><br>

<b>{Localisation::getTranslation(Strings::PRIVACY_NONPERSONAL_IDENTIFICATION_INFORMATION)}</b><br><br>

{Localisation::getTranslation(Strings::PRIVACY_5)}. {Localisation::getTranslation(Strings::PRIVACY_6)}.<br><br>

<b>{Localisation::getTranslation(Strings::PRIVACY_WEB_BROWSER_COOKIES)}</b><br><br>

{Localisation::getTranslation(Strings::PRIVACY_7)}. {Localisation::getTranslation(Strings::PRIVACY_8)}. {Localisation::getTranslation(Strings::PRIVACY_9)}.<br><br>

<b>{Localisation::getTranslation(Strings::PRIVACY_10)}</b><br><br>

{Localisation::getTranslation(Strings::PRIVACY_11)}:<br>
<ul>
<li><i>- {Localisation::getTranslation(Strings::PRIVACY_TO_PERSONALIZE_USER_EXPERIENCE)}</i><br>
	{Localisation::getTranslation(Strings::PRIVACY_12)}.</li>
<li><i>- {Localisation::getTranslation(Strings::PRIVACY_TO_IMPROVE_OUR_SITE)}</i><br>
	{Localisation::getTranslation(Strings::PRIVACY_13)}.</li>
<li><i>- {Localisation::getTranslation(Strings::PRIVACY_TO_SEND_PERIODIC_EMAILS)}</i><br>
{Localisation::getTranslation(Strings::PRIVACY_14)}. {Localisation::getTranslation(Strings::PRIVACY_15)}. {Localisation::getTranslation(Strings::PRIVACY_16)}.</li>
</ul>
<b>{Localisation::getTranslation(Strings::PRIVACY_17)}</b><br><br>

{Localisation::getTranslation(Strings::PRIVACY_18)}.<br><br>

<b>{Localisation::getTranslation(Strings::PRIVACY_SHARING_YOUR_PERSONAL_INFORMATION)}</b><br><br>

{Localisation::getTranslation(Strings::PRIVACY_19)}. {Localisation::getTranslation(Strings::PRIVACY_20)}.<br><br>

<b>{Localisation::getTranslation(Strings::PRIVACY_21)}</b><br><br>

{Localisation::getTranslation(Strings::PRIVACY_22)}. {Localisation::getTranslation(Strings::PRIVACY_23)}. {Localisation::getTranslation(Strings::PRIVACY_24)}. {Localisation::getTranslation(Strings::PRIVACY_25)}.<br><br>

<b>{Localisation::getTranslation(Strings::PRIVACY_26)}</b><br><br>

{Localisation::getTranslation(Strings::PRIVACY_27)}. {Localisation::getTranslation(Strings::PRIVACY_28)}. {Localisation::getTranslation(Strings::PRIVACY_29)}.<br><br>

<b>{Localisation::getTranslation(Strings::PRIVACY_CONTACTING_US)}</b><br><br>

{Localisation::getTranslation(Strings::PRIVACY_30)}:<br>
{mailto address={Settings::get("site.system_email_address")} encode='hex' text={Settings::get("site.system_email_address")}}<br>
<br>
{Localisation::getTranslation(Strings::PRIVACY_31)} April 25, 2013<br><br>

</div>
{include file="footer.tpl"}