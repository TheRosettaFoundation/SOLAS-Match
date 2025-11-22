{include file="new_header.tpl" body_id="home"}
<!-- Editor Hint: ¿áéíóú -->

<style>
a:hover{
    text-decoration:none !important;
}
.carousel {
    position: relative;
    margin-bottom: 20px;
    line-height: 1;
}
.clearheader{
    color:#143878 !important;
}
h3{
    font-size:12px;
}
@media (min-width: 400px ){
    h3{
        font-size: 16px;
    } 
}
@media (min-width: 900px ){
    h3{
        font-size: 24px;
    } 
}
.car{
    display:none ;
}
@media (min-width: 700px ){
    .car{
      display:block;
    } 
}
.twbheader{
    color:#e8991c !important;
}
.top-left {
  position: absolute;
  top: 8px;
  left: 23px;
  color:white;
  z-index:10;
}
.btn-block {
    width: 120px !important;
}
.btn-home-slider{
    display: inline-block;
    height: 30px;
}
#globe{
    color:white;
}
.login{
    color:white !important;
    font-weight:bold;
    text-decoration:underline;
}
.button {
  background-color: #143878; /* Green */
  border: none;
  color: white;
  padding: 5px 10px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 14px;
  margin: 4px 2px;
  transition-duration: 0.4s;
  cursor: pointer;
}
.button a{
    color:#FFFFFF;
    font-weight:bold;
}
.button_join {
  background-color: #f89406; 
  border: none;
  color: white;
  padding: 5px 35px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 14px;
  margin: 4px 2px;
  transition-duration: 0.4s;
  cursor: pointer;
}
.button_join a{
    color:#FFFFFF;
    font-weight:bold;
}
.button1 {
  background-color: #f89406; 
  color: #FFF; 
  border: 2px solid #f89406;
}
.button1:hover {
  background-color: #cb7500;
  color: white;
}
.button2 {
  background-color: #143878; 
  color: #FFFFFF; 
  border: 2px solid #143878;
}
.button2:hover {
  background-color: #0e2754;
  color: white;
}
a:hover{
    text-decoration:underline;
}
</style>

        <div class="container">

<span class="d-none">
    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
</span>

    <div class="hero-unit">
       <!-- <h1>{Localisation::getTranslation('index_translation_commons')}</h1>
        <p>Vital information, in the right language, at the right time. </p>
        <p>
            <a class="btn btn-primary btn-large" href="{urlFor name="register"}">
                <i class="icon-star icon-white"></i> {Localisation::getTranslation('common_register')}
            </a>
            <a class="btn btn-primary btn-large" href="{urlFor name="login"}">
                <i class="icon-share icon-white"></i> {Localisation::getTranslation('common_log_in')}
            </a>
            <a href="https://community.translatorswb.org/t/how-to-solve-your-login-issues/4385">Click here if you are having problems logging in</a>
        </p> -->
        
{if isset($flash['error'])}
    <br>

    <div class="alert alert-danger alert-dismissible fade show mt-4">
       
        <p><strong>{Localisation::getTranslation('common_warning')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}

{if isset($flash['info'])}
    <div class="alert alert-info alert-dismissible fade show mt-4">
        <p ><strong>{Localisation::getTranslation('common_note')} </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}</p>
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}

{if isset($flash['success'])}
     <div class="alert alert-success alert-dismissible fade show mt-4">
            <img src="{urlFor name='home'}ui/img/success.svg" alt="translator" class="mx-1 " />
            <strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}</p>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}

{if isset($flash['warning'])}
    <div class="alert alert-warning alert-dismissible fade show mt-4">
        <p ><strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['warning'])}</strong></p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}

        <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">

                <div class="carousel-item active" data-bs-interval="5000">

                    <img src="{urlFor name='home'}ui/img/homepage/slider1_bk.jpg" alt="Slider_1_Img" class="d-block w-100">
                    <div class="top-left py-2 mb-2">
                        <h3 >We provide growth opportunities for<br/> our community members</h3>
                        <br/>
                        <h3>Join to gain new experiences, learn relevant<br/> skills and increase your qualifications.</h3>
                        <br/>                   
                        <button class="button_join button1"><a href="{urlFor name='register'}"> JOIN</a></button>
                        <button class="button button2"><a style="color:white;" href="#learnmore"> LEARN MORE</a></button>                
                        <br/> 
                        <br/>  
                        <p>Already registered?<a class="login" href="{urlFor name='login'}"> Log In</a></p>
                    </div>
                    <div class="carousel-caption ">
                        <div class="row-fluid  d-flex justify-content-center flex-wrap">
                 
                        <div  class="car"><h3 >“Volunteering as a translator for TWB also helps to keep your eyes peeled and see things through a different perspective.”<br/> - Andrea Alvisi</h3></div>
                    </div>
                      <p></p>
                    </div>
                </div>

                <div class="carousel-item" data-bs-interval="5000">
                    <img src="{urlFor name='home'}ui/img/homepage/slider22.jpg" alt="Slider_2_Img" class="d-block w-100">
                    
                    <div class="top-left"><h3>We connect community members<br/> and humanitarian organizations</h3><br/>
                        <h3>Join to meet other humanitarian linguists and<br/>support nonprofits.</h3>
                        <br/>                   
                        <button class="button_join button1"><a href="{urlFor name='register'}"> JOIN</a></button>
                        <button class="button button2"><a style="color:white;" href="#learnmore"> LEARN MORE</a></button>               
                        <br/>
                        <br/> 
                        <p>Already registered?<a class="login" href="{urlFor name='login'}"> Log In</a></p>
                    </div>

                        <div class="carousel-caption">
                           <div class="row-fluid">
                                <div class="span4"></div>
                                <div class="span4"></div>
                                    <div  class="car">
                                        <h3>“Volunteering allows me to appreciate the difficulties imposed by language barriers and the impact on the wellbeing of people who live in communities where they don’t understand the local language.”<br/> - Nabil Salibi</h3>
                                    </div>
                            </div>                      
                                <p></p>                           
                        </div>                  
                </div>

                <div class="carousel-item" data-bs-interval="5000">
                    <img src="{urlFor name='home'}ui/img/homepage/slider3.png"  alt="Slider_3_Img" class="w-100 d-block">
                    <div class="top-left"><h3>We offer references, certificates, <br/>and courses</h3><br/>
                        <h3>Join to grow your professional profile and <br/>advance your career.</h3>
                        <br/>                   
                        <button class="button_join button1"><a href="{urlFor name='register'}"> JOIN</a></button>
                        <button class="button button2"><a style="color:white;" href="#learnmore"> LEARN MORE</a></button>               
                        <br/>
                        <br/> 
                        <p>Already registered?<a class="login" href="{urlFor name='login'}"> Log In</a></p>
                    </div>

                     <div class="carousel-caption">
                        <div class="row-fluid">
                            <div class="span4"></div>
                            <div class="span4"></div>
                            <div  class="car"><h3>“Volunteering with TWB has impacted me very deeply on an emotional and intellectual level. People living in refugee camps face critical situations.”<br/> - Freddy Nkurunziza</h3></div>
                        </div>

                        <p></p>
                    </div>                    
                </div>

                <div class="carousel-item" data-bs-interval="5000">
                          <img src="{urlFor name='home'}ui/img/homepage/slider4.png" alt="Slider_4_Img" class="d-block w-100">

                        <div class="top-left">
                            <h3>TWB helps people get vital information, and <br/>be heard, whatever language they speak. </h3><br/>
                            <h3>Join to have a real-world impact.</h3>
                            <br/>
                            <button class="button_join button1"><a href="{urlFor name='register'}"> JOIN</a></button>
                            <button class="button button2"><a style="color:white;" href="#learnmore"> LEARN MORE</a></button>                
                            <br/>
                            <br/> 
                            <p>Already registered?<a class="login" href="{urlFor name='login'}"> Log In</a></p>                                                    
                        </div>

                      <div class="carousel-caption">
                        <div class="row-fluid">
                            <div class="span4"></div>
                            <div class="span4"></div>
                            <div  class="car">
                            <h3>“When I speak my own language, I am free. When I hear someone else speaking Rohingya, I feel like I am home.”<br/> – Rohingya person talking to TWB</h3>
                            </div>
                        </div>                  
                        <p></p>
                    </div>
                </div>
        </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
    </div>

 <div class="row-fluid">
 <br/>
    <p class=" bg-primary p-4 text-white">Translators without Borders is a community that brings together volunteers from all over the globe who offer their time, language skills and voices to support our mission - to help people get vital information and be heard, whatever language they speak. Through translation, subtitling and the power of their speech, our volunteers provide a wide range of information from medical content for farmers in Latin America to crisis relief information for people affected by earthquakes in Haiti, and resources for victims of gender-based violence and sexual abuse around the globe.
    <br/>
    <br/>
    You can volunteer with Translators without Borders if you are fluent in at least one language other than your native language.
    <br/>
    </p>  
   </div>
   <br/>
   <br/>
   <br/>
    <div class="row-fluid">
         <div class="span4">
           <p></p>
           <p></p>
         </div>
         <div class="span4">
         <div id="globe" style="text-align: center">
                  <a class="btn btn-primary text-white font-bold" style="text-decoration:none;" href="{urlFor name="register"}">JOIN NOW</a>
                </div>
         </div>
         <div class="span4">
          <p></p>
         </div>
   </div>
<br/>

  <div class="container">  
    <div class="row ">

        <div class="col-12 col-md-6 col-lg-3 d-flex flex-column align-items-center " >
                <div>
                <img src="{urlFor name='home'}ui/img/homepage/connect.png" style="width:150px ; height:150px" alt="Con">
                </div>

                <h2 class="mt-2">Connect</h2>
       
            <div class="text-center mt-2">
                Meet other linguists from around the world in the TWB Community Forum.Ask and answer questions, exchange and chat with other volunteer linguist and TWB staff
                Learn about the nonprofits that work with TWB and why they come to us.
                Attend one of our online initiatives like topical meetups, webinars and chat freely with other attendees 
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3 d-flex flex-column align-items-center">
              <div>
                    <img src="{urlFor name='home'}ui/img/homepage/learn.png" style="width:150px ; height:150px" alt="Con">
              </div>

              <h2 class="mt-2">Learn</h2>

              <div class="text-center mt-2">
                        Learn about translation in the humanitarian field by taking one of our courses and receive a certificate upon successful completion.
                        Get free access to Phrase TMS, our translation tool and hone your translation skills through practice.
                        Receive feedback on your translations from more senior linguists and grow in the process.
            </div>
        </div>

        <div  class="col-12  col-md-6 col-lg-3 d-flex flex-column align-items-center" >
           <div>
                  <img src="{urlFor name='home'}ui/img/homepage/grow.png" style="width:150px ; height:150px" alt="Con">
           </div>

           <h2 class="mt-2">Grow</h2>    

            <div class="text-center mt-2">
             Receive public acknowledgements of your contributions on the TWB platform. Request reference letters, translator feedback and skill endorsements on professional platforms and build out your resume.
            The most active linguists also get featured on TWB's blog and in the TWB Community Forum. 
            </div>
         </div>

         <div class="col-12 col-md-6 col-lg-3 d-flex flex-column align-items-center" id="learnmore">
           <div>
                   <img src="{urlFor name='home'}ui/img/homepage/impact.png" style="width:150px ; height:150px" alt="Impact">
           </div>

           <h2 class="mt-2" >Impact</h2>

             <div class="text-center mt-2">
                Have real-world impact on communities by translating humanitarian and development content in the languages those communities speak.
                Contribute to the development of glossaries and chatbots, create voice recordings and subtitle videos. Make humanitarian responses more effective by making localized information available in different formats.
                Bridge the world's language gap and help respond more sensitively to the existing need.  
            </div>
        </div>

    </div>

  </div>

</div>

</div>

{include file="footer2.tpl"}
