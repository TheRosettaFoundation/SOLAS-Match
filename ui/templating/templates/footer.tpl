            <br/><br/>
            <div class="well">
                <footer>	
                    <div style="text-align: center">
                        <p>
                            &copy; 2012-2013 University of Limerick &middot; <a href="http://www.therosettafoundation.org/">The Rosetta Foundation</a>
                        </p>
                        <p>
                            {mailto address={Settings::get("site.system_email_address")} encode='hex' text='Contact Us'} | <a href="#">Terms of Use</a> | <a href="{urlFor name='privacy'}">Privacy Statement</a> | <a href="http://forum.solas.uni.me/" target="_blank">Community Forum</a>
                        </p>
                        <p>                
                            Your <a href="https://docs.google.com/a/ul.ie/spreadsheet/viewform?formkey=dER4VFJZQVpNY0g2anpLb2dJSGJEbFE6MQ#gid=0">feedback</a> is appreciated.
                        </p>
                    </div>
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

