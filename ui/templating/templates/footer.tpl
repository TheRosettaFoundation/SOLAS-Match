           <!--  <br/><br/>
            <div class="well pull-left">
 -->

 
                      <footer>
                        <p>&copy; Company 2015</p>
                      </footer>


               <!--  <footer>
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
                                <a href="http://www.therosettafoundation.org" target="_blank">
                                    <img alt="The Rosetta Foundation" style="border-width:0" src="{urlFor name='home'}ui/img/TheRosettaFoundationLogo.png" height="48px" />
                                </a>
                                <br />
                                {sprintf(Localisation::getTranslation('footer_maintained_by'), "http://www.therosettafoundation.org/", "The Rosetta Foundation")}
                                <br/>
                                {sprintf(Localisation::getTranslation('footer_feedback'), "https://docs.google.com/a/ul.ie/spreadsheet/viewform?formkey=dER4VFJZQVpNY0g2anpLb2dJSGJEbFE6MQ#gid=0")}
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
                        <a href="{urlFor name='privacy'}">
                            {Localisation::getTranslation('footer_privacy_policy')}
                        </a>
                        {if Settings::get('site.forum_enabled') == 'y'}
                            | <a href="{Settings::get('site.forum_link')}" target="_blank">
                                {Localisation::getTranslation('common_forum')}
                            </a>
                        {/if}
                    </div>
                </footer>     -->
            </div>
                 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
                 <script>window.jQuery || document.write('<script src="{urlFor name="home"}resources/public/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

                 <script src="{urlFor name="home"}resources/public/js/vendor/bootstrap.min.js"></script>

                 <script src="{urlFor name="home"}resources/public/js/main.js"></script>


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

