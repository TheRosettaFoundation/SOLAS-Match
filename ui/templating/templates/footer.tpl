            <br/><br/>
            <style>
        .sponsor:hover{
            color:#FFFFFF;
            background-color:#e8991c;
            text-decoration:none;
        }
        
        
        </style> 
            
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
                        <a href="https://twbplatform.org/static/privacy/">
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
        <br/>
               <div class="navbar" style="color:#000000;">
           <div class="navbar-inner1" style="margin-left: 15px;">
                <div class="container">
                        <div class="span4" style="text-align: center;margin-left:-25px;">
         <a href="https://facebook.com/translatorswithoutborders" target="_blank" class="fa fa-facebook"></a>
         <a href="https://www.instagram.com/translatorswb/?hl=en" target="_blank" class="fa fa-instagram"></a>
         <a href="https://linkedin.com/company/translators-without-borders" target="_blank" class="fa fa-linkedin"></a>
         <a href="https://twitter.com/TranslatorsWB" target="_blank" class="fa fa-twitter" style="background: #55ACEE;color: white;"></a>
         <a href="https://www.youtube.com/user/TranslatorsWB" target="_blank" class="fa fa-youtube"></a>
         <br/>
         <a href="https://share.hsforms.com/1ctab13J6RHWkhWHLjzk3wQ4fck2?__hstc=84675846.0317038ad40c7930bed861f0514d9b6b.1634021927382.1634021927382.1634309162966.2&__hssc=84675846.1.1634309162966&__hsfp=2187346942" target="_blank" class="btn btn-primary">Subscribe to TWB Newsletter</a>

           
        </div>  
        
                    <ul class="nav main_nav" style="margin-left: 240px;margin-top: 45px;">
                    Maintained by <a class="sponsor" href="https://translatorswithoutborders.org" target="_blank">Translators without Borders</a>
                    </ul>
                    <ul class=" pull-right main_nav_right" style="max-height: 38px">
                            <a class="s_logo"  href="http://github.com/TheRosettaFoundation/SOLAS-Match" target="_blank">
                                    <img   alt="Solas Logo" style="border-width:0;margin-left:35px;" src="{urlFor name='home'}ui/img/logo.png" height="48px" />
                                </a>
                                <br />
                                TWB Platform is powered by <a class="sponsor" href="https://github.com/TheRosettaFoundation/SOLAS-Match" target="_blank">Solas</a>
                            <br/>

                           
                        
                    </ul>
                </div>
            </div>
        </div>
        
    
         <div class="navbar" style="color:#000000;">
           <div class="navbar-inner1" >
                <div class="container" style="margin-top:-15px;">
                        <div class="span4" style="text-align: center">
  
           
        </div>  
        
                    <ul class="nav main_nav" style="margin-left: 460px;margin-top: 45px;">
                     {mailto address={Settings::get("site.system_email_address")} encode='hex' text={Localisation::getTranslation("footer_contact_us")}} |
                        <a href="{urlFor name='terms'}">
                            {Localisation::getTranslation('footer_terms_and_conditons')}
                        </a> |
                        <a href="https://twbplatform.org/static/privacy/">
                            {Localisation::getTranslation('footer_privacy_policy')}
                        </a>
                        {if Settings::get('site.forum_enabled') == 'y'}
                            | <a href="{Settings::get('site.forum_link')}" target="_blank">
                                {Localisation::getTranslation('common_forum')}
                            </a>
                        {/if}
                    </ul>
                    <ul class=" pull-right main_nav_right" style="max-height: 38px">
                               <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/us/" target="_blank"><img class="wp-image-2357 pull-right" src="https://translatorswithoutborders.org/wp-content/uploads/2016/04/image001-150x150.png" alt="image001" width="50" height="50"></a>

                           
                        
                    </ul>
                </div>
            </div>
        </div>
         <!-- Container -->
       <!-- <div class="row-fluid">
        <div class="span4" style="text-align: center">
         <a href="https://facebook.com/translatorswithoutborders" target="_blank" class="fa fa-facebook"></a>
         <a href="https://www.instagram.com/translatorswb/?hl=en" target="_blank" class="fa fa-instagram"></a>
         <a href="https://linkedin.com/company/translators-without-borders" target="_blank" class="fa fa-linkedin"></a>
         <a href="https://twitter.com/TranslatorsWB" target="_blank" class="fa fa-twitter" style="background: #55ACEE;color: white;"></a>
         <a href="https://www.youtube.com/user/TranslatorsWB" target="_blank" class="fa fa-youtube"></a>
         <br/>
         <a href="https://share.hsforms.com/1ctab13J6RHWkhWHLjzk3wQ4fck2?__hstc=84675846.0317038ad40c7930bed861f0514d9b6b.1634021927382.1634021927382.1634309162966.2&__hssc=84675846.1.1634309162966&__hsfp=2187346942" target="_blank" class="btn btn-primary">Subscribe to TWB Newsletter</a>

           
        </div>
        <div class="span4" style="text-align: center">
        <br />
        <br />
        {sprintf(Localisation::getTranslation('footer_maintained_by'), "https://translatorswithoutborders.org", "Translators without Borders")}
        </div>
        <div class="span4" style="text-align: center">
                                	<a href="http://github.com/TheRosettaFoundation/SOLAS-Match" target="_blank">
                                    <img alt="Solas Logo" style="border-width:0;" src="{urlFor name='home'}ui/img/logo.png" height="48px" />
                                </a>
                                <br />
                                {sprintf(Localisation::getTranslation('footer_powered_by'), "https://github.com/TheRosettaFoundation/SOLAS-Match", "Solas")}
        </div>
        </div> -->
    

                     <!--   <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/us/" target="_blank"><img class="wp-image-2357 pull-right" src="https://translatorswithoutborders.org/wp-content/uploads/2016/04/image001-150x150.png" alt="image001" width="50" height="50"></a> -->
             </div>

                
                    <br/>
                    <br/>
                    <script>

                     
                    const quill = new Quill('#editor', {
                      theme: 'snow'
                    });
                    
                    let textarea = document.getElementById("project_description");
                                    
                    let htmlText = textarea.value ;
                    
                     var delta = quill.clipboard.convert(htmlText)                   

                     quill.root.innerHTML = htmlText;
                
                    quill.on('text-change', function(delta, oldDelta, source){
                       if(source =='user'){
                           updateFormattedText() ;
                       }

                     
                    } )

                    function updateFormattedText(){

                      let htmlContent = quill.root.innerHTML;
                      let delta = quill.getContents();
                      textarea.value = htmlContent;

                    
                    }


             
                  </script>

    </body>  
</html> 