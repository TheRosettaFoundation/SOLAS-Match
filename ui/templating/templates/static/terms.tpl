{include file="header.tpl"}

<div class="page-header" style="text-align: center">
    <h1>{Localisation::getTranslation(Strings::TERMS_TERMS_AND_CONDITIONS)}</h1>
</div>  

<div>
    <p><h3>{Localisation::getTranslation(Strings::TERMS_TERMS)}</h3></p>
    <p>{Localisation::getTranslation(Strings::TERMS_0)}</p>

    <p><h3>{sprintf(Localisation::getTranslation(Strings::TERMS_ORGANISATION_USE_OF), Settings::get("site.name"))}</h3></p>
    <p>{Localisation::getTranslation(Strings::TERMS_3)}</p>

    <p><h4>{Localisation::getTranslation(Strings::TERMS_ORGANISATION_USE_LICENSE)}</h4></p>
    <p>
        <ol type="a">
            <li>
                {Localisation::getTranslation(Strings::TERMS_4)}
                <ol type="i">
                    <li>{sprintf(Localisation::getTranslation(Strings::TERMS_5), Settings::get("site.name"))}</li>
                    <li>{sprintf(Localisation::getTranslation(Strings::TERMS_6), Settings::get("site.name"))}</li>
                    <li>{Localisation::getTranslation(Strings::TERMS_7)}</li>
               </ol>
            </li>
            <li>
                {Localisation::getTranslation(Strings::TERMS_8)}
                <ol>
                    <li>
                       {Localisation::getTranslation(Strings::TERMS_9)}
                   </li>    
                   <li>{Localisation::getTranslation(Strings::TERMS_13)}</li>
                   <li>{sprintf(Localisation::getTranslation(Strings::TERMS_14), Settings::get("site.name"))}</li>
               </ol>
           </li>
           <li>
               {sprintf(Localisation::getTranslation(Strings::TERMS_15), Settings::get("site.name"))}
           </li>
        </ol>
    </p>       
       
    <p><h4>{Localisation::getTranslation(Strings::TERMS_OFFENSIVE_CONTENT)}</h4></p>
    <p>{sprintf(Localisation::getTranslation(Strings::TERMS_17), Settings::get("site.name"))}</p>
    <p>
        <ol>
           <li>{Localisation::getTranslation(Strings::TERMS_CONSTITUTES_CHILD_PORNOGRAPHY)}</li>
           <li>{Localisation::getTranslation(Strings::TERMS_CONSTITUTES_PORNOGRAPHY)}</li>
           <li>{Localisation::getTranslation(Strings::TERMS_18)}</li>
           <li>{Localisation::getTranslation(Strings::TERMS_19)}</li>
           <li>{Localisation::getTranslation(Strings::TERMS_20)}</li>
           <li>{Localisation::getTranslation(Strings::TERMS_21)}</li>
           <li>{Localisation::getTranslation(Strings::TERMS_22)}</li>
           <li>{Localisation::getTranslation(Strings::TERMS_23)}</li>
           <li>{Localisation::getTranslation(Strings::TERMS_24)}</li>
           <li>{Localisation::getTranslation(Strings::TERMS_25)}</li>
           <li>{sprintf(Localisation::getTranslation(Strings::TERMS_26), Settings::get("site.name"))}</li>
           <li>{sprintf(Localisation::getTranslation(Strings::TERMS_27), Settings::get("site.name"))}</li>
        </ol>
    </p>
    
    <p><h3>{Localisation::getTranslation(Strings::TERMS_VOLUNTEER_USE_LICENSE)}</h3></p>
    <p>
        <ol type="a">
           <li>
               {sprintf(Localisation::getTranslation(Strings::TERMS_28), Settings::get("site.name"))}
               <ol type="i">
                   <li>{Localisation::getTranslation(Strings::TERMS_30)}</li>
                   <li>{sprintf(Localisation::getTranslation(Strings::TERMS_31), Settings::get("site.name"))}</li>
                   <li>{Localisation::getTranslation(Strings::TERMS_32)}</li>
                   <li>{Localisation::getTranslation(Strings::TERMS_33)}</li>
               </ol>
           </li>
           <li>
               {sprintf(Localisation::getTranslation(Strings::TERMS_34), Settings::get("site.name"))}
           </li>
           <li>
               {sprintf(Localisation::getTranslation(Strings::TERMS_36), "http://creativecommons.org/licenses/by/3.0/ie/", "Creative Commons Attribution 3.0 Ireland License")} 
           </li>
        </ol>
    </p>
       
    <p><h3>{Localisation::getTranslation(Strings::TERMS_DISCLAIMER)}</h3></p>
    <p>
       {sprintf(Localisation::getTranslation(Strings::TERMS_39), {Settings::get("site.name")|upper})}
    </p>

    <p><h3>{Localisation::getTranslation(Strings::TERMS_LIMITATIONS)}</h3></p>
    <p>
       {Localisation::getTranslation(Strings::TERMS_42)}
    </p>

    <p><h3>{Localisation::getTranslation(Strings::TERMS_REVISIONS_AND_ERRATA)}</h3></p>
    <p>
       {sprintf(Localisation::getTranslation(Strings::TERMS_44), Settings::get("site.name"), Settings::get("site.name"), Settings::get("site.name"), Settings::get("site.name"))}
    </p>

    <p><h3>{Localisation::getTranslation(Strings::TERMS_LINKS)}</h3></p>
    <p>
       {sprintf(Localisation::getTranslation(Strings::TERMS_47), Settings::get("site.name"), Settings::get("site.name"))}
    </p>

    <p><h3>{Localisation::getTranslation(Strings::TERMS_49)}</h3></p>
    <p>
       {Localisation::getTranslation(Strings::TERMS_50)}
    </p>

    <p><h3>{Localisation::getTranslation(Strings::TERMS_GOVERNING_LAW)}</h3></p>
    <p>
       {Localisation::getTranslation(Strings::TERMS_52)}
    </p>
    <p>
       {Localisation::getTranslation(Strings::TERMS_53)}
    </p>

    <p><h3>{Localisation::getTranslation(Strings::TERMS_PRIVACY_POLICY)}</h3></p>
    <p>
        {sprintf(Localisation::getTranslation(Strings::TERMS_54), {urlFor name='privacy'})}
    </p>
    <p>
       {Localisation::getTranslation(Strings::TERMS_56)}
    </p>        
</div>
{include file="footer.tpl"}