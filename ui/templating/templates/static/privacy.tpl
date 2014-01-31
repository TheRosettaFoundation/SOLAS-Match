{include file="header.tpl"}
<div class="page-header" style="text-align: center">
    <h1>{Localisation::getTranslation(Strings::PRIVACY_PRIVACY_POLICY)}</h1>
</div>  
<div>
    <p>{sprintf(Localisation::getTranslation(Strings::PRIVACY_0), {urlFor name="home"}, Settings::get("site.name"))}</p>

    <p><h4>{Localisation::getTranslation(Strings::PRIVACY_PERSONAL_IDENTIFICATION_INFORMATION)}</h4></p>
    <p>{Localisation::getTranslation(Strings::PRIVACY_2)}</p>

    <p><h4>{Localisation::getTranslation(Strings::PRIVACY_NONPERSONAL_IDENTIFICATION_INFORMATION)}</h4></p>
    <p>{Localisation::getTranslation(Strings::PRIVACY_5)}</p>

    <p><h4>{Localisation::getTranslation(Strings::PRIVACY_WEB_BROWSER_COOKIES)}</h4></p>
    <p>{Localisation::getTranslation(Strings::PRIVACY_7)}</p>

    <p><h4>{Localisation::getTranslation(Strings::PRIVACY_10)}</h4></p>
    <p>{Localisation::getTranslation(Strings::PRIVACY_11)}</p>
    <ul>
        <li>
            <p><i>{Localisation::getTranslation(Strings::PRIVACY_TO_PERSONALIZE_USER_EXPERIENCE)}</i><p>
            <p>{Localisation::getTranslation(Strings::PRIVACY_12)}</p>
        </li>
        <li>
            <p><i>{Localisation::getTranslation(Strings::PRIVACY_TO_IMPROVE_OUR_SITE)}</i></p>
            <p>{Localisation::getTranslation(Strings::PRIVACY_13)}</p>
        </li>
        <li>
            <p><i>{Localisation::getTranslation(Strings::PRIVACY_TO_SEND_PERIODIC_EMAILS)}</i></p>
            <p>{Localisation::getTranslation(Strings::PRIVACY_14)}</p>
        </li>
    </ul>
    
    <p><h4>{Localisation::getTranslation(Strings::PRIVACY_17)}</h4></p>
    <p>{Localisation::getTranslation(Strings::PRIVACY_18)}</p>

    <p><h4>{Localisation::getTranslation(Strings::PRIVACY_SHARING_YOUR_PERSONAL_INFORMATION)}</h4></p>
    <p>{Localisation::getTranslation(Strings::PRIVACY_19)}</p>

    <p><h4>{Localisation::getTranslation(Strings::PRIVACY_21)}</h4></p>
    <p>{Localisation::getTranslation(Strings::PRIVACY_22)}</p>

    <p><h4>{Localisation::getTranslation(Strings::PRIVACY_26)}</h4></p>
    <p>{Localisation::getTranslation(Strings::PRIVACY_27)}</p>
    
    <p><h4>{Localisation::getTranslation(Strings::PRIVACY_CONTACTING_US)}</h4></p>
    <p>{sprintf(Localisation::getTranslation(Strings::PRIVACY_30), {mailto address={Settings::get("site.system_email_address")} encode='hex' text={Settings::get("site.system_email_address")}})}</p>

    <p>{Localisation::getTranslation(Strings::PRIVACY_31)}</p>

</div>
{include file="footer.tpl"}