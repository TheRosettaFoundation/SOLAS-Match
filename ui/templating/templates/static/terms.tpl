{include file="header.tpl"}

<div class="page-header" style="text-align: center">
    <h1>{Localisation::getTranslation('terms_terms_and_conditions')}</h1>
</div>  

<div>
    <p><h3>{Localisation::getTranslation('terms_terms')}</h3></p>
    <p>{Localisation::getTranslation('terms_0')}</p>

    <p><h3>{sprintf(Localisation::getTranslation('terms_organisation_use_of'), Settings::get("site.name"))}</h3></p>
    <p>{Localisation::getTranslation('terms_3')}</p>

    <p><h4>{Localisation::getTranslation('terms_organisation_use_license')}</h4></p>
    <p>
        <ol type="a">
            <li>
                {Localisation::getTranslation('terms_4')}
                <ol type="i">
                    <li>{sprintf(Localisation::getTranslation('terms_5'), Settings::get("site.name"))}</li>
                    <li>{sprintf(Localisation::getTranslation('terms_6'), Settings::get("site.name"))}</li>
                    <li>{Localisation::getTranslation('terms_7')}</li>
               </ol>
            </li>
            <li>
                {Localisation::getTranslation('terms_8')}
                <ol>
                    <li>
                       {Localisation::getTranslation('terms_9')}
                   </li>    
                   <li>{Localisation::getTranslation('terms_13')}</li>
                   <li>{sprintf(Localisation::getTranslation('terms_14'), Settings::get("site.name"))}</li>
               </ol>
           </li>
           <li>
               {sprintf(Localisation::getTranslation('terms_15'), Settings::get("site.name"))}
           </li>
        </ol>
    </p>       
       
    <p><h4>{Localisation::getTranslation('terms_offensive_content')}</h4></p>
    <p>{sprintf(Localisation::getTranslation('terms_17'), Settings::get("site.name"))}</p>
    <p>
        <ol>
           <li>{Localisation::getTranslation('terms_constitutes_child_pornography')}</li>
           <li>{Localisation::getTranslation('terms_constitutes_pornography')}</li>
           <li>{Localisation::getTranslation('terms_18')}</li>
           <li>{Localisation::getTranslation('terms_19')}</li>
           <li>{Localisation::getTranslation('terms_20')}</li>
           <li>{Localisation::getTranslation('terms_21')}</li>
           <li>{Localisation::getTranslation('terms_22')}</li>
           <li>{Localisation::getTranslation('terms_23')}</li>
           <li>{Localisation::getTranslation('terms_24')}</li>
           <li>{Localisation::getTranslation('terms_25')}</li>
           <li>{sprintf(Localisation::getTranslation('terms_26'), Settings::get("site.name"))}</li>
           <li>{sprintf(Localisation::getTranslation('terms_27'), Settings::get("site.name"))}</li>
        </ol>
    </p>
    
    <p><h3>{Localisation::getTranslation('terms_volunteer_use_license')}</h3></p>
    <p>
        <ol type="a">
           <li>
               {sprintf(Localisation::getTranslation('terms_28'), Settings::get("site.name"))}
               <ol type="i">
                   <li>{Localisation::getTranslation('terms_30')}</li>
                   <li>{sprintf(Localisation::getTranslation('terms_31'), Settings::get("site.name"))}</li>
                   <li>{Localisation::getTranslation('terms_32')}</li>
                   <li>{Localisation::getTranslation('terms_33')}</li>
               </ol>
           </li>
           <li>
               {sprintf(Localisation::getTranslation('terms_34'), Settings::get("site.name"))}
           </li>
           <li>
               {sprintf(Localisation::getTranslation('terms_36'), "http://creativecommons.org/licenses/by/3.0/ie/", "Creative Commons Attribution 3.0 Ireland License")} 
           </li>
        </ol>
    </p>
       
    <p><h3>{Localisation::getTranslation('terms_disclaimer')}</h3></p>
    <p>
       {sprintf(Localisation::getTranslation('terms_39'), {Settings::get("site.name")|upper})}
    </p>

    <p><h3>{Localisation::getTranslation('terms_limitations')}</h3></p>
    <p>
       {Localisation::getTranslation('terms_42')}
    </p>

    <p><h3>{Localisation::getTranslation('terms_revisions_and_errata')}</h3></p>
    <p>
       {sprintf(Localisation::getTranslation('terms_44'), Settings::get("site.name"), Settings::get("site.name"), Settings::get("site.name"), Settings::get("site.name"))}
    </p>

    <p><h3>{Localisation::getTranslation('terms_links')}</h3></p>
    <p>
       {sprintf(Localisation::getTranslation('terms_47'), Settings::get("site.name"), Settings::get("site.name"))}
    </p>

    <p><h3>{Localisation::getTranslation('terms_49')}</h3></p>
    <p>
       {Localisation::getTranslation('terms_50')}
    </p>

    <p><h3>{Localisation::getTranslation('terms_governing_law')}</h3></p>
    <p>
       {Localisation::getTranslation('terms_52')}
    </p>
    <p>
       {Localisation::getTranslation('terms_53')}
    </p>

    <p><h3>{Localisation::getTranslation('terms_privacy_policy')}</h3></p>
    <p>
        {sprintf(Localisation::getTranslation('terms_54'), {urlFor name='privacy'})}
    </p>
    <p>
       {Localisation::getTranslation('terms_56')}
    </p>        
</div>
{include file="footer.tpl"}