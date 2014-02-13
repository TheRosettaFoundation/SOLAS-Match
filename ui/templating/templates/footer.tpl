            <br/><br/>
            <div class="well">
                <footer>
                    <table>
                        <tr>
                            <td width="31%" style="text-align: center">
                                    <a rel="license" href="http://creativecommons.org/licenses/by/3.0/ie/"><img alt="Creative Commons Licence" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/ie/88x31.png" /></a><br />{sprintf(Localisation::getTranslation('footer_0'), "http://creativecommons.org/licenses/by/3.0/ie/", {urlFor name='terms'})}
                            </td>
                            <td width="38%" style="text-align: center">
                                
                                    <p>
                                        <a href="http://www.therosettafoundation.org/">The Rosetta Foundation</a>
                                    </p>
                                    <p>
                                        {mailto address={Settings::get("site.system_email_address")} encode='hex' text='Contact Us'} | <a href="{urlFor name='terms'}">{Localisation::getTranslation('footer_terms_and_conditons')}</a> | <a href="{urlFor name='privacy'}">{Localisation::getTranslation('footer_privacy_policy')}</a> <!--| <a href="http://forum.solas.uni.me/" target="_blank">{Localisation::getTranslation('footer_community_forum')}</a>-->
                                    </p>
                                    <p>                
                                        {sprintf(Localisation::getTranslation('footer_feedback'), "https://docs.google.com/a/ul.ie/spreadsheet/viewform?formkey=dER4VFJZQVpNY0g2anpLb2dJSGJEbFE6MQ#gid=0")}
                                    </p>
                                
                             </td>
                             <td width="31%" style="text-align: center">
                                 <img alt="Creative Commons Licence" style="border-width:0" src="{urlFor name='home'}ui/img/logo.png" height="48px" /><br /> {sprintf(Localisation::getTranslation('footer_powered_by'), "https://github.com/TheRosettaFoundation/SOLAS-Match", "Solas")}
                             </td>
                        </tr>
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

