            <br/><br/>
            
            <div class="well pull-left" style="display:none;">
                <footer>
                    <table>
                        <tr>
                            <td width="31%" style="text-align: center">
                                <a rel="license" href="http://creativecommons.org/licenses/by/3.0/ie/">
                                    <img alt="Creative Commons Licence" style="border-width:0" src="{urlFor name='home'}ui/img/88x31.png" />
                                </a>
                                <br />
                                {sprintf(Localisation::getTranslation('footer_0'), "http://creativecommons.org/licenses/by/3.0/ie/", {urlFor name='terms'})}
                            </td>
                            <td width="38%" style="text-align: center">
                                {sprintf(Localisation::getTranslation('footer_maintained_by'), "https://translatorswithoutborders.org", "Translators without Borders")}
                            </td>
                            <td width="31%" style="text-align: center">
                            	<a href="http://github.com/TheRosettaFoundation/SOLAS-Match" target="_blank">
                                    <img alt="Solas Logo" style="border-width:0" src="{urlFor name='home'}ui/img/logo.png" height="48px" />
                                </a>
                                <br />
                                {sprintf(Localisation::getTranslation('footer_powered_by'), "https://github.com/TheRosettaFoundation/SOLAS-Match", "Solas")}
                            </td>
                        </tr>
                    </table>
                    <div id="footer-menu">
                        {mailto address={Settings::get("site.system_email_address")} encode='hex' text={Localisation::getTranslation("footer_contact_us")}} |
                        <a href="{urlFor name='terms'}">
                            {Localisation::getTranslation('footer_terms_and_conditons')}
                        </a> |
                        <a href="https://kato.translatorswb.org/static/privacy/">
                            {Localisation::getTranslation('footer_privacy_policy')}
                        </a>
                        {if Settings::get('site.forum_enabled') == 'y'}
                            | <a href="{Settings::get('site.forum_link')}" target="_blank">
                                {Localisation::getTranslation('common_forum')}
                            </a>
                        {/if}
                    </div>
                    
                </footer>    
            </div>
        </div>
         <!-- Container -->
        <div class="row-fluid">
        <div class="span4" style="text-align: center;margin-left:-890px;">
         <a href="https://facebook.com/translatorswithoutborders" target="_blank" class="fa fa-facebook"></a>
         <a href="https://www.instagram.com/translatorswb/?hl=en" target="_blank" class="fa fa-instagram"></a>
         <a href="https://linkedin.com/company/translators-without-borders" target="_blank" class="fa fa-linkedin"></a>
         <a href="https://twitter.com/TranslatorsWB" target="_blank" class="fa fa-twitter" style="background: #55ACEE;color: white;"></a>
         <a href="https://www.youtube.com/user/TranslatorsWB" target="_blank" class="fa fa-youtube"></a>
         <br/>
         <a href="https://share.hsforms.com/1ctab13J6RHWkhWHLjzk3wQ4fck2?__hstc=84675846.0317038ad40c7930bed861f0514d9b6b.1634021927382.1634021927382.1634309162966.2&__hssc=84675846.1.1634309162966&__hsfp=2187346942" target="_blank" class="btn btn-primary">Subscribe to TWB Newsletter</a>

           
        </div>
        <div class="span4" style="text-align: center;margin-left:-430px;margin-top:15px;">
        <br />
        <br />
        {sprintf(Localisation::getTranslation('footer_maintained_by'), "https://translatorswithoutborders.org", "Translators without Borders")}
        </div>
        <div class="span4" style="text-align: center;margin-left:895px;margin-top:-25px;">
                                	<a href="http://github.com/TheRosettaFoundation/SOLAS-Match" target="_blank">
                                    <img alt="Solas Logo" style="border-width:0;" src="{urlFor name='home'}ui/img/logo.png" height="48px" />
                                </a>
                                <br />
                                {sprintf(Localisation::getTranslation('footer_powered_by'), "https://github.com/TheRosettaFoundation/SOLAS-Match", "Solas")}
        </div>
        </div>
    
<br/>
             <div id="footer-menu">
                        {mailto address={Settings::get("site.system_email_address")} encode='hex' text={Localisation::getTranslation("footer_contact_us")}} |
                        <a href="{urlFor name='terms'}">
                            {Localisation::getTranslation('footer_terms_and_conditons')}
                        </a> |
                        <a href="https://kato.translatorswb.org/static/privacy/">
                            {Localisation::getTranslation('footer_privacy_policy')}
                        </a>
                        {if Settings::get('site.forum_enabled') == 'y'}
                            | <a href="{Settings::get('site.forum_link')}" target="_blank">
                                {Localisation::getTranslation('common_forum')}
                            </a>
                        {/if}
                        <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/us/" target="_blank"><img class="wp-image-2357 pull-right" src="https://translatorswithoutborders.org/wp-content/uploads/2016/04/image001-150x150.png" alt="image001" width="50" height="50"></a>
             </div>

                
                    <br/>
                    <br/>
    </body>  
</html> 
