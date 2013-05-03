            <br/><br/>
            <div class="well">
                <footer>
                    <table>
                        <tr>
                            <td width="31%" style="text-align: center">
                                    <a rel="license" href="http://creativecommons.org/licenses/by/3.0/ie/"><img alt="Creative Commons Licence" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/ie/88x31.png" /></a><br />Unless otherwise stated, all works contributed are licensed under the <a rel="license" href="http://creativecommons.org/licenses/by/3.0/ie/">Creative Commons Attribution 3.0 Ireland License </a> in accordance with the <a href="{urlFor name='terms'}">terms and conditons</a> of trommons.org
                            </td>
                            <td width="38%" style="text-align: center">
                                
                                    <p>
                                        &copy; 2012-2013 University of Limerick &middot; <a href="http://www.therosettafoundation.org/">The Rosetta Foundation</a>
                                    </p>
                                    <p>
                                        {mailto address={Settings::get("site.system_email_address")} encode='hex' text='Contact Us'} | <a href="{urlFor name='terms'}">Terms and Conditons</a> | <a href="{urlFor name='privacy'}">Privacy Policy</a> | <a href="http://forum.solas.uni.me/" target="_blank">Community Forum</a>
                                    </p>
                                    <p>                
                                        Your <a href="https://docs.google.com/a/ul.ie/spreadsheet/viewform?formkey=dER4VFJZQVpNY0g2anpLb2dJSGJEbFE6MQ#gid=0">feedback</a> is appreciated.
                                    </p>
                                
                             <td>
                             <td width="31%" style="text-align: center">
                                 <img alt="Creative Commons Licence" style="border-width:0" src="{urlFor name='home'}ui/img/logo.png" height="48px" /><br /> Powered by <b>Solas Match</b>
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

