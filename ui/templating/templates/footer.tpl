            <br/><br/>
            <div class="well">
                <footer>
                    <table>
                        <tr>
                            <td width="31%" style="text-align: center">
                                    <a rel="license" href="http://creativecommons.org/licenses/by/3.0/ie/"><img alt="Creative Commons Licence" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/ie/88x31.png" /></a><br />{Localisation::getTranslation(Strings::FOOTER_0)} <a rel="license" href="http://creativecommons.org/licenses/by/3.0/ie/">Creative Commons Attribution 3.0 Ireland License </a> {Localisation::getTranslation(Strings::FOOTER_IN_ACCORDANCE_WITH_THE)} <a href="{urlFor name='terms'}">{Localisation::getTranslation(Strings::FOOTER_TERMS_AND_CONDITONS)}</a> - trommons.org
                            </td>
                            <td width="38%" style="text-align: center">
                                
                                    <p>
                                        <a href="http://www.therosettafoundation.org/">The Rosetta Foundation</a>
                                    </p>
                                    <p>
                                        {mailto address={Settings::get("site.system_email_address")} encode='hex' text='Contact Us'} | <a href="{urlFor name='terms'}">{Localisation::getTranslation(Strings::FOOTER_TERMS_AND_CONDITONS)}</a> | <a href="{urlFor name='privacy'}">{Localisation::getTranslation(Strings::FOOTER_PRIVACY_POLICY)}</a> | <a href="http://forum.solas.uni.me/" target="_blank">{Localisation::getTranslation(Strings::FOOTER_COMMUNITY_FORUM)}</a>
                                    </p>
                                    <p>                
                                        {Localisation::getTranslation(Strings::COMMON_YOUR)} <a href="https://docs.google.com/a/ul.ie/spreadsheet/viewform?formkey=dER4VFJZQVpNY0g2anpLb2dJSGJEbFE6MQ#gid=0">{Localisation::getTranslation(Strings::FOOTER_FEEDBACK)}</a> {Localisation::getTranslation(Strings::FOOTER_IS_APPRECIATED)}.
                                    </p>
                                
                             <td>
                             <td width="31%" style="text-align: center">
                                 <img alt="Creative Commons Licence" style="border-width:0" src="{urlFor name='home'}ui/img/logo.png" height="48px" /><br /> {Localisation::getTranslation(Strings::FOOTER_POWERED_BY)} <a href="https://github.com/TheRosettaFoundation/SOLAS-Match">Solas</a>
                             </td>
                        <tr>
                    </table>
                </footer>    
            </div>
            {if isset($openid)&& ($openid==='y'||$openid==='h' )}
                <script type="text/javascript">
                    $(window).load(function() {
                            openid.init('openid_identifier');
                            openid.setDemoMode(false); //Stops form submission for client javascript-only test purposes
                    });
                </script>
            {/if}
        </div> <!-- Container -->
    </body>  
</html> 

