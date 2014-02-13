{include file="header.tpl"}
<div class="page-header" style="text-align: center">
    <h1>{Localisation::getTranslation('privacy_privacy_policy')}</h1>
</div>  
<div>
    <p>{sprintf(Localisation::getTranslation('privacy_0'), {urlFor name="home"}, Settings::get("site.name"))}</p>

    <p><h4>{Localisation::getTranslation('privacy_personal_identification_information')}</h4></p>
    <p>{Localisation::getTranslation('privacy_2')}</p>

    <p><h4>{Localisation::getTranslation('privacy_nonpersonal_identification_information')}</h4></p>
    <p>{Localisation::getTranslation('privacy_5')}</p>

    <p><h4>{Localisation::getTranslation('privacy_web_browser_cookies')}</h4></p>
    <p>{Localisation::getTranslation('privacy_7')}</p>

    <p><h4>{Localisation::getTranslation('privacy_10')}</h4></p>
    <p>{Localisation::getTranslation('privacy_11')}</p>
    <ul>
        <li>
            <p><i>{Localisation::getTranslation('privacy_to_personalize_user_experience')}</i><p>
            <p>{Localisation::getTranslation('privacy_12')}</p>
        </li>
        <li>
            <p><i>{Localisation::getTranslation('privacy_to_improve_our_site')}</i></p>
            <p>{Localisation::getTranslation('privacy_13')}</p>
        </li>
        <li>
            <p><i>{Localisation::getTranslation('privacy_to_send_periodic_emails')}</i></p>
            <p>{Localisation::getTranslation('privacy_14')}</p>
        </li>
    </ul>
    
    <p><h4>{Localisation::getTranslation('privacy_17')}</h4></p>
    <p>{Localisation::getTranslation('privacy_18')}</p>

    <p><h4>{Localisation::getTranslation('privacy_sharing_your_personal_information')}</h4></p>
    <p>{Localisation::getTranslation('privacy_19')}</p>

    <p><h4>{Localisation::getTranslation('privacy_21')}</h4></p>
    <p>{Localisation::getTranslation('privacy_22')}</p>

    <p><h4>{Localisation::getTranslation('privacy_26')}</h4></p>
    <p>{Localisation::getTranslation('privacy_27')}</p>
    
    <p><h4>{Localisation::getTranslation('privacy_contacting_us')}</h4></p>
    <p>{sprintf(Localisation::getTranslation('privacy_30'), {mailto address={Settings::get("site.system_email_address")} encode='hex' text={Settings::get("site.system_email_address")}})}</p>

    <p>{Localisation::getTranslation('privacy_31')}</p>

</div>
{include file="footer.tpl"}