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

.twbheader{
    color:#e8991c !important;
}
.top-left {
  position: absolute;
  top: 8px;
  left: 16px;
  color:white;
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
</style>

<span class="d-none">
    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
</span>




{if !isset($user)}
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
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p style="font-size:15px;"><strong>{Localisation::getTranslation('common_warning')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
    </div>
{/if}

{if isset($flash['info'])}
    <div class="alert alert-info">
        <p style="font-size:15px;"><strong>{Localisation::getTranslation('common_note')} </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}</p>
    </div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p style="font-size:15px;"><strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}</p>
    </div>
{/if}

{if isset($flash['warning'])}
    <div class="alert alert-warning">
        <p style="font-size:15px;"><strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['warning'])}</strong></p>
    </div>
{/if}

        <div id="carouselExample" class="carousel slide">
            <div class="carousel-inner">

                <div class="carousel-item active">

                    <img src="{urlFor name='home'}ui/img/homepage/slider1_bk.jpg" alt="Slider_1_Img" class="d-block w-100">
                    <div class="top-left">
                        <h2>We provide growth opportunities for<br/> our community members</h2>
                        <br/>
                        <h3>Join to gain new experiences, learn relevant<br/> skills and increase your qualifications.</h3>
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
                        <div  class="span4 pull-right"><h4 >“Volunteering as a translator for TWB also helps to keep your eyes peeled and see things through a different perspective.”<br/> - Andrea Alvisi</h4></div>
                    </div>
                      <p></p>
                    </div>

                </div>

                
                <div class="carousel-item">

                    <img src="{urlFor name='home'}ui/img/homepage/slider22.jpg" alt="Slider_2_Img" class="d-block w-100">
                    
                    <div class="top-left"><h2>We connect community members<br/> and humanitarian organizations</h2><br/>
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
                                    <div  class="span4 pull-right">
                                        <h4 >“Volunteering allows me to appreciate the difficulties imposed by language barriers and the impact on the wellbeing of people who live in communities where they don’t understand the local language.”<br/> - Nabil Salibi</h4>
                                    </div>
                            </div>                      
                                <p></p>                           
                        </div>                  

                </div>

                            
                <div class="carousel-item">
                    <img src="{urlFor name='home'}ui/img/homepage/slider3.png"  alt="Slider_3_Img" class="w-100 d-block">
                    <div class="top-left"><h2>We offer references, certificates, <br/>and courses</h2><br/>
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
                            <div  class="span4 pull-right"><h4 >“Volunteering with TWB has impacted me very deeply on an emotional and intellectual level. People living in refugee camps face critical situations.”<br/> - Freddy Nkurunziza</h4></div>
                        </div>
                      
                        <p></p>
                    </div>                    

                </div>


                <div class="carousel-item">

                          <img src="{urlFor name='home'}ui/img/homepage/slider4.png" alt="Slider_4_Img" class="d-block w-100">

                        <div class="top-left">
                            <h2>TWB helps people get vital information, and <br/>be heard, whatever language they speak. </h2><br/>
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
                            <div  class="span4 pull-right">
                            <h4 >“When I speak my own language, I am free. When I hear someone else speaking Rohingya, I feel like I am home.”<br/> – Rohingya person talking to TWB</h4>
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


              
   <!-- <div class="row-fluid">

         <h2 class="twbheader" style="text-align: center">In the last 30 days</h2>
         <div style="text-align: center" class="span4">
           <h1 class="clearheader" id="value" >{$user_monthly_count}</h1>
           <span class="clearheader" >Active Users</span>
         </div>
         <div class="span4">
           <p></p>
         </div>
         <div class="span4">
          <p></p>
         </div>
  
   </div> -->

 <div class="row-fluid">
 <br/>
    <p class="btn-primary home_text">Translators without Borders is a community that brings together volunteers from all over the globe who offer their time, language skills and voices to support our mission - to help people get vital information and be heard, whatever language they speak. Through translation, subtitling and the power of their speech, our volunteers provide a wide range of information from medical content for farmers in Latin America to crisis relief information for people affected by earthquakes in Haiti, and resources for victims of gender-based violence and sexual abuse around the globe.
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
                <button class="btn btn-primary btn-home-slider" type="submit">
	                <a class="login" style="text-decoration:none;" href="{urlFor name="register"}">JOIN NOW</a>
	            </button>
                </div>
         </div>
         <div class="span4">
          <p></p>
         </div>
  
   </div>
<br/>


<div class="row-fluid d-flex flex-row justify-content-between" id="learnmore">
    <div >
    <div style="text-align: center"><img src="{urlFor name='home'}ui/img/homepage/connect.png" alt="Con"></div>
   
        <div>
            <div class="span3"><h2 class="clearheader" style="text-align: center">Connect</h2></div>
                <div>Meet other linguists from around the world in the TWB Community Forum.Ask and answer questions, exchange and chat with other volunteer linguist and TWB staff.<br/>
                Learn about the nonprofits that work with TWB and why they come to us.<br/>
                Attend one of our online initiatives like topical meetups, webinars and chat freely with other attendees 
                </div>   
        </div>
    
    </div>

    <div>
        <div style="text-align: center"><img src="{urlFor name='home'}ui/img/homepage/learn.png" alt="Con"></div>
        <div>
            <h2  class="clearheader" style="text-align: center">Learn</h2>
                <div>
                        Learn about translation in the humanitarian field by taking one of our courses and receive a certificate upon successful completion.<br/>
                        Get free access to Phrase TMS, our translation tool and hone your translation skills through practice.<br/>
                        Receive feedback on your translations from more senior linguists and grow in the process.
                </div>


        
        </div>

    </div>

    <div >
        <div style="text-align: center"><img src="{urlFor name='home'}ui/img/homepage/grow.png" alt="Con"></div>
        <div>

            <div class="span3"><h2 class="clearheader" style="text-align: center">Grow</h2></div>
             <div>Receive public acknowledgements of your contributions on the TWB platform. Request reference letters, translator feedback and skill endorsements on professional platforms and build out your resume.<br/>
            The most active linguists also get featured on TWB's blog and in the TWB Community Forum. </div>
        
        </div>
    </div>

    <div>
        <p style="text-align: center"><img src="{urlFor name='home'}ui/img/homepage/impact.png" alt="Con"></p>
        <div>
            <div class="span3"><h2 class="clearheader" style="text-align: center">Impact</h2></div>
              <div>
                Have real-world impact on communities by translating humanitarian and development content in the languages those communities speak.<br/>
                Contribute to the development of glossaries and chatbots, create voice recordings and subtitle videos. Make humanitarian responses more effective by making localized information available in different formats.<br/>
                Bridge the world's language gap and help respond more sensitively to the existing need.  
             </div>

        
        </div>

    </div>
</div>



 	{if ((Settings::get('banner.enabled') == 'y'))}
	    <div id="banner-container">
	    <a href = "{Settings::get('banner.link')}" target = "_blank">
	    	<div id="banner-container-blocks">
		    	<div id="banner-left">
            <img src="{urlFor name='home'}ui/img/banner/banner-left-en2.png" alt="{Settings::get('banner.info')}">
		    	</div>
		    	<div id="banner-mid">
            <img src="{urlFor name='home'}ui/img/banner/banner-mid-en2.png" alt="{Settings::get('banner.info')}">
		    	</div>
		    	<div id="banner-right">
            <img src="{urlFor name='home'}ui/img/banner/banner-right-en2.png" alt="{Settings::get('banner.info')}">
		    	</div>
	    	</div>
	    </a>
	    </div>
	{/if}    
{/if}

{if isset($user)}

{if isset($flash['error'])}
    <br>
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>{Localisation::getTranslation('common_warning')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
    </div>
{/if}

{if isset($flash['info'])}
    <div class="alert alert-info">
        <p><strong>{Localisation::getTranslation('common_note')} </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}</p>
    </div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}</p>
    </div>
{/if}

{if isset($flash['warning'])}
    <div class="alert alert-warning">
        <p><strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['warning'])}</strong></p>
    </div>
{/if}
<div class ="container-fluid">
<div class="d-flex row justify-content-between ">

    <div class="col-3 p-2">
     {if isset($user)}
            <h3>{Localisation::getTranslation('index_filter_available_tasks')}
            
            </h3>
            <div>You can only filter for languages that you have chosen as your language pairs in your user profile.</div>
            <h3 class="mt-5 mb-2"> Industry </h3>
            <form method="post" action="{urlFor name="home"}">
	           
	                    <div>
                            <div class="mb-3">
                                 
                                    <select name="taskTypes" id="taskTypes" class="form-select">
	                                    <option value="0" {if ($selectedTaskType === 0)}selected="selected"{/if}>{Localisation::getTranslation('index_any_task_type')}</option>
                                      <!-- <option value="1" {if ($selectedTaskType === 1)}selected="selected"{/if}>{Localisation::getTranslation('common_segmentation')}</option> -->
	                                    <option value="2" {if ($selectedTaskType === 2)}selected="selected"{/if}>{Localisation::getTranslation('common_translation')}</option>
	                                    <option value="3" {if ($selectedTaskType === 3)}selected="selected"{/if}>{Localisation::getTranslation('common_proofreading')}</option>
                                      <!-- <option value="4" {if ($selectedTaskType === 4)}selected="selected"{/if}>{Localisation::getTranslation('common_desegmentation')}</option> -->
                                      <option value="6" {if ($selectedTaskType === 6)}selected="selected"{/if}>Proofreading and Approval</option>
	                                 </select>

                            </div>

                            <div class="mb-3">
                                   
                                            <select name="sourceLanguage" ID="sourceLanguage" class="form-select">
                                                <option value="0" {if ($selectedSourceLanguageCode === 0)}selected="selected"{/if}>{Localisation::getTranslation("index_any_source_language")}</option>
                                                {foreach $activeSourceLanguages as $lang}
                                                    <option value="{$lang->getCode()}" {if ($selectedSourceLanguageCode === $lang->getCode())}selected="selected"{/if}>{$lang->getName()}</option>
                                                {/foreach}
                                            </select>	                            

                            </div>

                            <div class"mb-3">
                                    
                                        <select name="targetLanguage" ID="targetLanguage" class="form-select">
	                                    <option value="0" {if ($selectedTargetLanguageCode === 0)}selected="selected"{/if}>{Localisation::getTranslation("index_any_target_language")}</option>
	                                    {foreach $activeTargetLanguages as $lang}
	                                        <option value="{$lang->getCode()}" {if ($selectedTargetLanguageCode === $lang->getCode())}selected="selected"{/if}>{$lang->getName()}</option>
	                                    {/foreach}
	                                </select>
                            </div>
	                        
	                    </div>
	          
                <div class=" d-grid mt-3 mb-5  ">
                    <button class="btn btn-primary " type="submit">
                        <img src="{urlFor name='home'}ui/img/setting-5.svg" alt="Con" class="me-1">
                        <span class=""> Apply filters</span>
                    </button>                
                </div>

            </form>
        {/if}

          {include file="tag/tags.top-list.flex.inc.tpl"}
          {include file="tag/tags.user-flex.inc.tpl"}
            
   
    </div>

    <div class="col-9">


                    {if isset($topTasks) && count($topTasks) > 0}
                    
            <div>
                {for $count=0 to $itemsPerScrollPage-1}
                    {assign var="task" value=$topTasks[$count]}
                    <div class="d-flex justify-content-between mb-4 bg-body-tertiary p-3 rounded-3"  >
                       <div class=" w-100">
                        {assign var="task_id" value=$task->getId()}
                        {assign var="type_id" value=$task->getTaskType()}
                        {assign var="task_title" value=$task->getTitle()}
                        {if $taskImages[$task_id]}
                        <div  id="task_{$task_id}">
                        {else}
                        <div  id="task_{$task_id}">
                        {/if}
                            
                            <div class="fs-4 fw-bold">
                                <a id="task-{$task_id}" href="{$siteLocation}task/{$task_id}/view" class="text-primary">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task_title)}</a>
                            </div>
                            <div class="d-flex mt-4 mb-1 fs-6">
                              <button class="rounded-5 btn btn-tag-green">  <span class="">{TaskTypeEnum::$enum_to_UI[$type_id]['type_text']} </span> </button>
                                {if $task->getWordCount()}
                                   <button type="button" class=" ms-1 rounded-5 btn bg-secondary "><span class="fs-6,"> {Localisation::getTranslation('common_word_count')}: <strong>{$task->getWordCount()}</strong> </span> </button>
                                {/if}

                            </div>
                         
                            {if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}
                            <div class="mb-1 mt-3 text-muted">
                            <span class=" ">
                                Languages: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getSourceLocale())} - </strong>
                            </span>
                            {/if}
                            <span>
                              <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())}</strong>
                            </span>
                            <div class="process_deadline_utc" style="visibility: hidden">{$deadline_timestamps[$task_id]}</div>
                           
                            
                            </div>
                                       

                            {if $task->getProjectId() > Settings::get("discourse.pre_discourse") && !preg_match('/^Test.{4}$/', $task_title)}
                            <div class ="d-flex justify-content-between align-items-center">
                            <div> Translation Project for Translations without Borders</div>
                            
                            </div>
                            {/if}
                            <div class="d-flex justify-content-end">
                            <a class="btn btn-secondary fs-6  href="" target="_blank">view Task</a>
                            </div>
                           
                        </div>

                        </div>
                        {if $taskImages[$task_id]}
                            <div id="img_{$task_id}"  >
                                <img src="{$taskImages[$task_id]}" class="w-50">
                            </div>
                        {else}
                            <div id="img_{$task_id}" class="" ></div>
                        {/if}
                    </div>
                {/for}
            </div>
        </div>

            <ul class="pagination d-flex justify-content-center">

            {assign var="url_nam" value="home-paged"}
            {if $page_count> 6}
                {assign var="count" value= 6}
            {else}
                {assign var="count" value= $page_count}
            {/if}    
               <li class="page-item" id="page-item">
                     <a class="page-link" href="{urlFor name="$url_nam" options="page_no.$paged|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode "}">previous</a></li>
 
            {for $page=1 to $count}

                    <li class="page-item" id="page-item">
                     <a class="page-link" href="{urlFor name="$url_nam" options="page_no.$paged|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode "}">{$page}</a></li>
            {/for}
               <li class="page-item" id="page-item">
                     <a class="page-link" href="{urlFor name="$url_nam" options="page_no.$paged|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode "}">next</a></li> 
            </ul>

  


             {* pagination begins here *}
            

            {assign var="url_name" value="home-paged"}
            <ul class="pager pull-left">
                <div class="pagination-centered" id="ias-pagination">
                    {if $currentScrollPage > 1}
                        <li>
                            <a href="{urlFor name="$url_name" options="page_no.1|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}" title="First">&lt;&lt;</a>
                        </li>
                        <li class="ts-previous">
                            {assign var="previous" value=($currentScrollPage - 1)}
                            <a href="{urlFor name="$url_name" options="page_no.$previous|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}" title="Previous">&lt;</a>
                        </li>
                    {/if}
                    <li>
                        <a href="">{sprintf(Localisation::getTranslation('pagination_page_of'), {$currentScrollPage}, {$lastScrollPage})}</a>
                    </li>
                    {if $currentScrollPage < $lastScrollPage}
                        <li class="ts-next">
                            {assign var="next" value=($currentScrollPage + 1)}
                            <a href="{urlFor name="$url_name" options="page_no.$next|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}" title="Next" >&gt;</a>
                        </li>
                        <li>
                            <a class="page-link" href="{urlFor name="$url_name" options="page_no.$lastScrollPage|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}" title="Last">&gt;&gt;</a>
                        </li>
                    {/if}
                </div>
            </ul>
        {else}
            <p>
                {if !$org_admin}
                There are currently no tasks available for your language combinations. However, some may come up soon, so don't forget to <a href="https://community.translatorswb.org/t/signing-up-for-kato-platform-email-notifications/121" target="_blank">set up email alerts</a> <br/> to be notified of new available tasks. Meanwhile, you can:
                <ol>
                <li>
                    <a href="https://translatorswithoutborders.org/blog/" target="_blank">Learn more</a> about the work we do
                </li>
                <li>
                    <a href="https://community.translatorswb.org/t/creating-and-personalizing-your-kato-community-profile/3048" target="_blank">Register</a> and browse our forum
                </li>
                <li>
                    New to TWB? Have a look at our <a href="https://community.translatorswb.org/t/welcome-pack-for-kato-translators/3138" target="_blank">Translator's Toolkit</a> to find out how to get started with us.
                </li>
                </ol>
                    <p>
                    For any questions or comments, please email <a href="mailto:translators@translatorswithoutborders.org" target="_blank">translators@translatorswithoutborders.org</a>
                    </p>
                {else}
                    Since you are not a translator, there are no tasks here. Click on <a href="https://twbplatform.org/org/dashboard/">your organization's Dashboard</a>
                {/if}
            </p>
        {/if}
        <br />

        {if !isset($user)}
            <div class="alert pull-left" style="width: 100%; margin-top: 10px;">
                <p>{Localisation::getTranslation('index_6')}</p>
                <p>{sprintf(Localisation::getTranslation('index_register_now'), {urlFor name='register'})}</p>
            </div>
        {/if}
    </div>
</div>

 
<h1 class="end of container "></h1>

    </div>

</div>


</div>
{/if}

       
{include file="footer2.tpl"}
