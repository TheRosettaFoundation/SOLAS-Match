{include file='new_header.tpl'}

{if isset($this_user)}
 <div class="container-fluid bg-light-subtle py-2">
   <div class="container px-4  py-4">

     <div class=" d-flex justify-content-between py-4 align-items-center px-2 flex-wrap">
     <div >

     <img class="rounded-circle" src="https://www.gravatar.com/avatar/{md5( strtolower( trim($this_user->getEmail())))}?s=80{urlencode("&")}r=g" alt="" />
                    {assign var="user_id" value=$this_user->getId()}
                    <span class="fw-bold me-4">
                    {if $this_user->getDisplayName() != ''}
                      {TemplateHelper::uiCleanseHTML($this_user->getDisplayName())}
                    {else}
                        {Localisation::getTranslation('common_user_profile')}
                    {/if}
                    {if !isset($no_header)}<small class="text-muted">{Localisation::getTranslation('user_public_profile_0')}</small>{/if}
                    </span>

     </div>


     <div class="d-flex align-items-center flex-wrap ">

                   {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                            <a href="{urlFor name="claimed-tasks" options="user_id.{$this_user->getId()}"}" class="btnPrimary me-2 text-white mt-2 mt-md-0">
                            <img src="{urlFor name='home'}ui/img/claimed.svg" class="me-2"> {Localisation::getTranslation('claimed_tasks_claimed_tasks')}
                            </a>
                        {/if}
                 
                        {if $private_access || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                          {if $admin_role&($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)}
                            <a href='{urlFor name="user-code-of-conduct" options="user_id.$user_id"}' class='btnPrimary me-2 text-white mt-2 mt-md-0'>
                                <i class="fa-solid fa-screwdriver-wrench me-2 "></i> {Localisation::getTranslation('user_public_profile_edit_profile_details')}
                            </a>
                          {else}
                            <a href='{urlFor name="user-private-profile" options="user_id.$user_id"}' class='btnPrimary me-2 text-white mt-2 mt-md-0 '>
                            <i class="fa-solid fa-screwdriver-wrench me-2  "></i>{Localisation::getTranslation('user_public_profile_edit_profile_details')}
                            </a>
                          {/if}
                        {/if}
                        {if false && ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)) && $howheard['reviewed'] == 0}
                            <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                <input type="submit" class="btnPrimary me-2 text-white mt-2 mt-md-0" name="mark_reviewed" value="Mark New User as Reviewed" />
                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                            </form>
                        {/if}
                        {if $show_create_memsource_user}
                            <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                <input type="submit" class="btnPrimary me-2 text-white mt-2 mt-md-0" name="mark_create_memsource_user" value="Create Matching Phrase TMS User" />
                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                            </form>
                        {/if}
    </div> 

     
     </div>
         {else} 
       <div class='fw-bold'><h1>{Localisation::getTranslation('common_user_profile')} </h1> <small class="text-muted">{Localisation::getTranslation('user_public_profile_2')}</small></div>
{/if}

{if isset($flash['error'])}
    <p class="alert alert-error" style="margin-bottom: 50px">
        {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
    </p>
{/if}
{if isset($flash['success'])}
    <p class="alert alert-success" style="margin-bottom: 50px">
        {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
    </p>
{/if}


    {if isset($this_user) && ($private_access || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)) || $receive_credit)}
  
        <div class="row g-4 ">    

        <div class="bg-body p-4 rounded-3 text-body col-12 col-md-7  >
   
       
        <span class="d-none">
        <div id="dialog_for_verification" title="Perform a translation test?" class="d-none">
        <p>Becoming verified will give you access to more tasks in your language pair. For more information please visit <a href="https://community.translatorswb.org/t/how-to-become-a-kato-verified-translator/262">this page</a>.</p>
        <p>By clicking “OK” below, a test will be created for you, and you will receive an email with instructions on how to complete the test.</p>
        <p>When you have completed the test, one of our Senior Translators will review it. When we have the results we will contact you by email. Please note, this can take 3-4 weeks.</p>
        <p>If you do not want to take the test, please click “Cancel”.</p>
        </div>
        </span>

                    {if isset($userPersonalInfo)}
                            <div class="mb-3 ">
                                    {if !empty($userPersonalInfo->getFirstName()) && !empty($userPersonalInfo->getLastName())}<h3 class="fw-bold mb-3">{TemplateHelper::uiCleanseHTML($userPersonalInfo->getFirstName())} {TemplateHelper::uiCleanseHTML($userPersonalInfo->getLastName())}</h3>{/if}

                                    {if $private_access || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                                        {if !empty($linguist_payment_information['linguist_name'])}<h4 class="mb-3">Official Name: {TemplateHelper::uiCleanseHTML($linguist_payment_information['linguist_name'])}</h4>{/if}
                                    {/if}

                                     <div> {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                                     {if $admin_role&$SITE_ADMIN}TWB ADMIN{if $admin_role&($PROJECT_OFFICER + $COMMUNITY_OFFICER)},{/if}{/if} {if $admin_role&$PROJECT_OFFICER}PROJECT OFFICER{if $admin_role&$COMMUNITY_OFFICER},{/if}{/if} {if $admin_role&$COMMUNITY_OFFICER}COMMUNITY OFFICER{/if}
                                     {if $admin_role&$NGO_ADMIN}NGO ADMIN{if $admin_role&$NGO_PROJECT_OFFICER},{/if}{/if} {if $admin_role&$NGO_PROJECT_OFFICER}NGO PROJECT OFFICER{/if}
                                     {if $admin_role&$NGO_LINGUIST}NGO LINGUIST{if !($admin_role&$LINGUIST)} (exclusive){/if}{/if}
                                     {/if}</div>
                                 
                             </div>
                    {/if}
                   
                    {if $private_access || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                    
                        <div class="d-flex justify-content-between flex-wrap mb-3">
                         <a href="mailto:{$this_user->getEmail()}" class=" text-body"> {$this_user->getEmail()}</a>
                         
                                    {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                                        <a href='{urlFor name="change-email" options="user_id.$user_id"}' class='bg-yellowish custom-link text-primary text-uppercase p-1 rounded-2 fs-5  mt-3 md:mt-0'>
                                            <i class="fa-solid fa-envelope ms-2 "></i> {Localisation::getTranslation('common_change_email')}
                                        </a>
                                    {/if}
                        </div>

                            
                                
                            
                        
                    {/if}

                    {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                    {if !empty($uuid)}
                    <div class="mt-3">
                    
                            <a href='{urlFor name="password-reset" options="uuid.$uuid"}' class=' bg-yellowish custom-link text-uppercase p-1 rounded-1 fs-5'>
                            <i class="fa-solid fa-link me-2"></i> Link emailed to User for Password Reset
                            </a>
                    
                    </div>

                 
                    {/if}


                        <div class="mb-3 mt-3">
                            
                                Joined: <strong> {substr($this_user->getCreatedTime(), 0, 10)}</strong>
                        </div>     
                    
                    {/if}

                    {if isset($userPersonalInfo)}
                    {if !empty($userPersonalInfo->getMobileNumber())}
                        <div class="mb-3"  >
                        
                                {TemplateHelper::uiCleanseHTML($userPersonalInfo->getMobileNumber())}
                        
                        </div>
                    {/if}
                    {if !empty($userPersonalInfo->getCity())}
                        <div class="mb-3" >
                    
                                {TemplateHelper::uiCleanseHTML($userPersonalInfo->getCity())}
                        
                        </div>
                    {/if}
                    {if !empty($userPersonalInfo->getCountry())}
                        <div class="mb-3" >
                        
                                {TemplateHelper::uiCleanseHTML($userPersonalInfo->getCountry())}
                        
                        </div>
                      
                    {/if}
                
                    {/if}
                    
                    <div class="d-flex ">
                        {foreach from=$url_list item=url}
                            {if $url['state']|strstr:"facebook"}                    
                                <a href="{$url['state']}" target="_blank"><img alt="Facebook icon" src="{urlFor name='home'}ui/img/urls/facebook.svg" class="me-2"/></a>
                            {elseif $url['state']|strstr:"twitter"}
                                <a href="{$url['state']}" target="_blank"><img alt="Twitter icon" src="{urlFor name='home'}ui/img/urls/x.svg" class="me-2"/></a>
                            {elseif $url['state']|strstr:"linkedin"}
                                <a href="{$url['state']}" target="_blank"><img alt="Linkedin icon" src="{urlFor name='home'}ui/img/urls/linkedin.svg" class="me-2"/></a>                             
                            {elseif $url['state']|strstr:"instagram"}
                                <a href="{$url['state']}" target="_blank"><img alt="Instagram icon" src="{urlFor name='home'}ui/img/urls/instagram.svg" class="me-2"/></a>
                            {elseif $url['state']|strstr:"youtube"}
                                <a href="{$url['state']}" target="_blank"><img alt="Youtube icon" src="{urlFor name='home'}ui/img/urls/youtube.svg"  class="me-2"/></a>
                            {elseif $url['state']|strstr:"proz"}
                                    <a href="{$url['state']}" target="_blank"><img alt="Proz icon" src="{urlFor name='home'}ui/img/urls/proz.svg"  class="me-2"/></a>    
                            {elseif  $url['state'] != ""}
                                <a href="{$url['state']}" target="_blank"><img alt="Url icon " src="{urlFor name='home'}ui/img/urls/globe.svg"  class="me-2"/></a>                        
                            {/if} 
                        {/foreach}
                    </div> 
                 
                    {assign var=bio value={TemplateHelper::uiCleanseHTMLNewlineAndTabs($this_user->getBiography())}}
                    {if !empty($bio)}
                
                        
                            <h4 class="mt-3 mb-3 fw-bold">About Me</h4>
                        
                
                            <div class="mb-3" >
                                
                                    {$bio}
                                
                            </div>
                    {/if}

                    {assign var="native_language_code" value=""}
                    {if $this_user->getNativeLocale() != null}
                    {assign var="native_language_code" value=$this_user->getNativeLocale()->getLanguageCode()}
                    <div class="mb-3">
                        
                            Native in <strong>{TemplateHelper::getLanguageAndCountry($this_user->getNativeLocale())}</strong>
                        
                    </div>

                    <hr class="bg-light-subtle"/>
                    {/if}
                  
                    {if !empty($userQualifiedPairs)}
                        <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <h4 class="mb-3 fw-bold w-50">{Localisation::getTranslation('common_secondary_languages')}</h4>
                                    {if $roles & ( $PROJECT_OFFICER + $SITE_ADMIN + $COMMUNITY_OFFICER)}
                                    <h4 class="mb-3 fw-bold">Eligible for Paid Task</h4>
                                    {/if}
                                </div>

                                {foreach from=$userQualifiedPairs item=userQualifiedPair}
                                    {assign var="pair" value="`$userQualifiedPair['language_code_source']`-`$userQualifiedPair['language_code_target']`"}
                                    {$button_count.$pair=0}
                                {/foreach}

                                {foreach from=$userQualifiedPairs item=userQualifiedPair}
                                    {assign var="pair" value="`$userQualifiedPair['language_code_source']`-`$userQualifiedPair['language_code_target']`"}
                                    {if $userQualifiedPair['qualification_level'] > 1}
                                        {$button_count.$pair=1}
                                    {/if}
                                {/foreach}

                                {foreach from=$userQualifiedPairs item=userQualifiedPair}
                                    <div class="d-flex justify-content-between align-items-center">
                                    <p class="w-50">
                                        {if $userQualifiedPair['country_source'] == 'ANY'}<span class="bg-light-subtle p-1 rounded-2">{$userQualifiedPair['language_source']}{else}{$userQualifiedPair['language_source']} - {$userQualifiedPair['country_source']}{/if} </span>  <img src="{urlFor name='home'}ui/img/lang_arr.svg" alt="arrow" class="mx-1"/> <span class="bg-light-subtle rounded-2 p-1 me-2 "> {if $userQualifiedPair['country_target'] == 'ANY'}{$userQualifiedPair['language_target']}{else}{$userQualifiedPair['language_target']} - {$userQualifiedPair['country_target']}{/if}</span>
                                        <strong>
                                        {if $userQualifiedPair['qualification_level'] == 1}({Localisation::getTranslation('user_qualification_level_1')}){/if}
                                        {if $userQualifiedPair['qualification_level'] == 2}({Localisation::getTranslation('user_qualification_level_2')}){/if}
                                        {if $userQualifiedPair['qualification_level'] == 3}({Localisation::getTranslation('user_qualification_level_3')}){/if}
                                        </strong>

                                        {assign var="pair" value="`$userQualifiedPair['language_code_source']`-`$userQualifiedPair['language_code_target']`"}
                                        {if false && $userQualifiedPair['qualification_level'] == 1 && in_array($pair, ['en-ar', 'en-fr', 'en-es', 'fr-en', 'es-en', 'en-pt', 'en-it']) && $native_language_code === $userQualifiedPair['language_code_target'] && ($private_access || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))) && $button_count.$pair == 0}
                                            {$button_count.$pair=1}
                                        <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                            <input type="hidden" name="source_language_country" value="{$userQualifiedPair['language_code_source']}-{$userQualifiedPair['country_code_source']}" />
                                            <input type="hidden" name="target_language_country" value="{$userQualifiedPair['language_code_target']}-{$userQualifiedPair['country_code_target']}" />
                                            {if empty($testing_center_projects_by_code[$pair]) || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                                                <input type="submit" class="add_click_handler btn btn-primary text-white" name="btnSubmit" value="Get Verified" />
                                            {else}
                                                <input type="submit" class="btn btn-primary text-white" name="btnSubmit" value="Get Verified" onclick="
    alert('You have already requested to take a test in order to become a TWB Verified Translator. If you would like to take a second test, please contact translators@translatorswithoutborders.org');
                                                return false;" />
                                            {/if}
                                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                        </form>
                                        {/if}
                                    </p>
                                    <p class="flex-grow-1" >
                                        <form class="d-flex flex-column align-items-center justify-content-center">
                                            {if isset($sesskey)}
                                            <span class="sesskey d-none">{$sesskey}</span>
                                            {/if}
                                            <span class="user d-none">{$user_id}</span>
                                            <span class="sl d-none">{$userQualifiedPair['language_id_source']}</span>
                                            <span class="sc d-none">{$userQualifiedPair['country_id_source']}</span>
                                            <span class="tl d-none">{$userQualifiedPair['language_id_target']}</span>
                                            <span class="tc d-none">{$userQualifiedPair['country_id_target']}</span>
                                            <span class="level d-none">{$userQualifiedPair['eligible_level']}</span>

                                            {if $roles & ($SITE_ADMIN + $COMMUNITY_OFFICER)}
                                            <select class="form-select border border-primary eligible text-primary"   aria-label="select eligibility">
                                                <option  {if $userQualifiedPair['eligible_level'] == null } selected  {/if} value="0">None</option>
                                                <option  {if $userQualifiedPair['eligible_level'] == '1' } selected  {/if} value="1">Translation</option>
                                                <option  {if $userQualifiedPair['eligible_level'] == '2' } selected  {/if} value="2">Translation and Revision</option>                                  
                                            </select>
                                            {elseif $roles & ($PROJECT_OFFICER )}                                     
                                                {if $userQualifiedPair['eligible_level'] == null }
                                                    <div class="bg-yellowish p-1 fs-5  text-primary text-uppercase rounded-2">None</div>
                                                {elseif $userQualifiedPair['eligible_level'] == '1'}
                                                    <div class="bg-yellowish p-1 fs-5  text-primary text-uppercase rounded-2">Translation</div>
                                                {elseif $userQualifiedPair['eligible_level'] == '2'}   
                                                    <div class="bg-yellowish p-1 fs-5  text-primary text-uppercase rounded-2">Translation and Revision</div>
                                                {/if}    
                                            {/if}  
                                        </form>
                                    </p>
                                    </div>
                                {/foreach}
                        </div>
                        <hr  class="bg-light-subtle"/>
                    {/if}
                  
                        {if !empty($user_rate_pairs) && ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                           

                            <div>
                            <div class="d-flex justify-content-between">
                                <h4 class="mb-3 fw-bold">Language Rate Pairs</h4>
                                {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                                    <div class="mb-3  ">
                                        
                                            <a href='{urlFor name="user_rate_pairs" options="user_id.$user_id"}' class='bg-yellowish p-1 fs-5 custom-link text-primary  text-decoration-none text-uppercase rounded-2'>
                                            <i class="fa-solid fa-edit me-1"></i> Edit Linguist Unit Rate Exceptions
                                            </a>
                                        
                                    </div>
                                    
                                 
                                {/if}

                            </div>
                             
                            <div class="mt-3">
                                
                                    {foreach from=$user_rate_pairs item=user_rate_pair}
                                        <p>

                                        <span class="bg-light-subtle rounded-2 p-1 me-2"> {$user_rate_pair['selection_source']} {* &nbsp;&nbsp;&nbsp;{Localisation::getTranslation('common_to')}&nbsp;&nbsp;&nbsp; *} </span>  <img src="{urlFor name='home'}ui/img/lang_arr.svg" alt="arrow" class="mx-1" > <span class="bg-light-subtle rounded-2 p-1 me-2"> {$user_rate_pair['selection_target']}</span>
                                            ({$user_rate_pair['task_type_text']}): <span class="fw-bold"> ${$user_rate_pair['unit_rate']} ({$user_rate_pair['pricing_and_recognition_unit_text_hours']})</span>
                                        </p>
                                    {/foreach}
                               
                            </div>
                            </div>

                            <hr class="bg-light-subtle" />
                            
                           
                        {/if}
                     
                     
                        
                        
                           
                            <h4 class="mb-3 fw-bold">Services</h4>
                            
                            <div>
                               
                                <ul>
                                {foreach from=$capability_list item=capability}
                                    {if $capability['state']}<li>{$capability['desc']|escape:'html':'UTF-8'}</li>{/if}
                                {/foreach}
                                </ul>
                                
                            </div>
                               <hr class="bg-light-subtle"/>
                           
                                    <h4 class="mb-3 fw-bold" >Experienced in</h4>
                            
                            <div>
                              
                                <ul>
                                {foreach from=$expertise_list item=expertise}
                                    {if $expertise['state']}<li>{$expertise['desc']|escape:'html':'UTF-8'}</li>{/if}
                                {/foreach}
                                </ul>
                              
                            </div>
                            <hr class="bg-light-subtle"/>

                            {if $private_access || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                            <div>
                                
                                    <h4 class="mb-3 fw-bold" >Share this link with anyone you wish to see your profile:</h4>
                                
                            </div>
                            <div class="d-flex">
                               
                                    <a class="btn btn-yellowish text-uppercase text-primary fs-7 me-2" id="linkcopy"  href="{urlFor name="shared_with_key" options="key.{$key}"}" target="_blank" > <img src="{urlFor name='home'}ui/img/copy_url" class="me-1" />  Preview</span></a>
                               
                                <button id="copy-button" class="btn btn-yellowish text-uppercase text-primary fs-7">  <img src="{urlFor name='home'}ui/img/copy_url" class="me-1" /> Copy</button>
                            </div>
                            {/if}
                            {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                           
                          
                                    <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}" class="mt-4 ">
                                        <input type="submit" class="btn btn-primary text-white" name="requestDocuments" value="Request Documents (paid projects linguist)" />
                                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                    </form>
                             {/if}
                             

  
        </div>
      
        <div class="bg-body p-4 rounded-3 text-body col-12  col-md-5">


            

                        <div class="bg-yellowish  text-dark d-flex justify-content-between rounded-3 mb-3  p-2 mt-2 md:mt-0">
                

                            <div class="d-flex flex-column">
                                <img src="{urlFor name='home'}ui/img/TWB_Logo.svg" class="mb-5 w-75" />
                                <h4 class="fw-bold mb-3">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_badge_name)}</h4>
                                <h2 class="mb-3 fw-bold"><span class="">{$user_badges['words_donated']}</span><br/> </h2>
                                <div class="opacity-75">WORDS DONATED</div>
                              {*<div class="d-flex "><img src="{urlFor name='home'}ui/img/TWB_Community_members_badge_BG-01.png" class="w-50 h-50" /></div>*}
                            </div>

                            <div class="">

                            <img src="{urlFor name='home'}ui/img/profile_badge"   />

                            </div>                           

                            
                        </div>


                        {if $private_access || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                       
                            <h4 class="mb-3 fw-bold mt-2">Use the link below to embed the above badge in another system:</h4>

                             <div class="d-flex align-items-center">
                              
                               <a class="btn btn-yellowish text-uppercase text-primary fs-7 me-2" id="badgecopy" href="{urlFor name="badge_shared_with_key" options="key.{$bkey}"}" target="_blank">  <img src="{urlFor name='home'}ui/img/copy_url" class="me-1" /> Preview</a>

                                <button id="badge-button" class="btn btn-yellowish text-uppercase text-primary fs-7">    <img src="{urlFor name='home'}ui/img/copy_url" class="me-1" /> Copy</button>
                            </div>
                        
                    
                            
                        
                        {/if}
               
                        {if !empty($user_badges['hours_donated'])}


                        
                            <div class="bg-yellowish  text-dark d-flex justify-content-between rounded-3 mb-3 mt-3  p-2">
                

                            <div class="d-flex flex-column">
                                <img src="{urlFor name='home'}ui/img/TWB_Logo.svg" class="mb-5 w-75" />
                                <h4 class="fw-bold mb-3">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_badge_name)}</h4>
                                <h2 class="mb-3 fw-bold"><span class="">{$user_badges['hours_donated']}</span><br/> </h2>
                                <div class="opacity-75">HOURS DONATED</div>
                              {*<div class="d-flex "><img src="{urlFor name='home'}ui/img/TWB_Community_members_badge_BG-01.png" class="w-50 h-50" /></div>*}
                            </div>

                            <div class="">

                            <img src="{urlFor name='home'}ui/img/profile_badge"  />

                            </div>                           

                            
                        </div>    

        

                        {if $private_access || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                 
                            <h4 class="fw-bold mt-2">Use the link below to embed the above badge in another system:</h4>

                            <div class="d-flex align-items-center">
                                   <a class="btn btn-yellowish text-uppercase text-primary fs-7 me-2" id="badgecopy_2" href="{urlFor name="badge_shared_with_key" options="key.{$hourkey}"}"   target="_blank"><img src="{urlFor name='home'}ui/img/copy_url" class="me-1" /> Preview </a>
                              
                                <button id="badge-button_2" class="btn btn-yellowish text-uppercase text-primary fs-7">    <img src="{urlFor name='home'}ui/img/copy_url" class="me-1" /> Copy</button>
                            </div>
                        
                       
                         
                       
                        {/if}
                        {/if}
                        <hr class="bg-light-subtle"/>
                 
                       
                                <h4 class="mb-3 mt-3 fw-bold">Supported Organizations</h4>
                       
                       
                            <ul>
                            {foreach from=$supported_ngos item=supported_ngo}
                                <li>{$supported_ngo['org_name']|escape:'html':'UTF-8'}</li>
                            {/foreach}
                            </ul>
                        

                        {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)) && !empty($supported_ngos_paid)}
                       
                                <h4 class="mb-3 fw-bold">NGOs supported with paid projects</h4>
                       
                      
                            <ul>
                            {foreach from=$supported_ngos_paid item=supported_ngo}
                                <li>{$supported_ngo['org_name']|escape:'html':'UTF-8'}</li>
                            {/foreach}
                            </ul>
                 
                        {/if}

                      
                                <h4 class="mb-3 fw-bold">Certificates and training courses</h4>
                       
                    
                            <ul>
                        {foreach from=$certifications item=certification}
                        <li>
                        {if $private_access || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
                            {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)) && $certification['reviewed'] == 0 && $certification['certification_key'] != 'TRANSLATOR' && $certification['certification_key'] != 'TWB'}
                            <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                <input type="submit" class="btn btn-primary text-white " name="mark_certification_reviewed" value="Mark Reviewed" />
                                <input type="hidden" name="certification_id" value="{$certification['id']}" />
                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                <a href="{urlFor name="user-download" class="custom-link" options="id.{$certification['id']}"}">{$certification['note']|escape:'html':'UTF-8'}</a>
                            </form>
                           {else}
                           <a href="{urlFor name="user-download" options="id.{$certification['id']}"}" class="custom-link">{$certification['note']|escape:'html':'UTF-8'}</a>{if $private_access && $certification['reviewed'] == 1} (reviewed){/if}
                           {/if}
                            {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                                <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}" class="mt-2">
                                    <input type="submit" class="btn btn-danger" name="mark_certification_delete" value="Delete" onclick="return confirm('Are you sure you want to permanently delete this certificate?')" />
                                    <input type="hidden" name="certification_id" value="{$certification['id']}" />
                                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                </form>
                           {/if}
                        {else}
                        {$certification['note']|escape:'html':'UTF-8'}
                        {/if}
                        </li>
                        {/foreach}
                            </ul>
                       

                        {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                     <a href="{urlFor name="user-uploads" options="user_id.$user_id|cert_id.TWB"}" target="_blank" class="bg-yellowish text-uppercase fs-5 p-1 rounded-2 custom-link text-decoration-none  align-middle">  <img src="{urlFor name='home'}ui/img/upload.svg" class="me-2" /> <span>Upload a new file for this user </span></a>
                     <hr  class="bg-light-subtle"/>  
                    {/if}

                    

                        {if $private_access || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}

                               <div class="d-flex justify-content-between mb-3">
                                    <div class="fw-bold"> Average scores in reviews out of 5 </div>
                                </div>


                                <div class="text-sm mb-4 fw-bold fs-7">This information is only visible to you</div>

                                <div class="d-flex  mt-2"> 
                                     <ul class="me-4">

                                        <li class="mb-2">Accuracy</li>
                                        <li class="mb-2">Fluency</li>
                                        <li class="mb-2">Terminology</li>
                                        <li class="mb-2">Style</li>
                                        <li class="mb-2">Design</li>

                                     
                                     
                                     </ul>
                                     <div class="ms-4">

                                         <div class="mb-2 fw-bold ">{$quality_score['accuracy']}</div>
                                          <div class="mb-2 fw-bold">{$quality_score['fluency']}</div>
                                           <div class="mb-2 fw-bold">{$quality_score['terminology']}</div>
                                            <div class="mb-2 fw-bold">{$quality_score['style']}</div>
                                             <div class="mb-2 fw-bold">{$quality_score['design']}</div>
                                       
                                     
                                     </div>
                                
                                </div>


                             

                                
                     
                        {/if}
              

            </div>
     
 
     
     </div>
     


     
    {if $private_access || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
    
        <div class="mt-3 p-4 rounded-2 bg-body fs-4" >

    <h3 class="fw-bold mb-3">Community Recognition Program <span class="text-muted fs-4">Contribute to our mission and obtain rewards</span></h3>

<p>We believe it is important to acknowledge the value and impact of the crucial support that our TWB Community members provide.
As part of our Community Recognition Program, you can receive rewards depending on your level of contribution.
Deliver tasks on the TWB platform to build up points.
Once you reach the point thresholds described in the chart below, please request the respective reward by sending an email at
<a  class="custom-link" href="mailto:recognition@translatorswithoutborders.org?subject=Request reward" target="_blank">recognition@translatorswithoutborders.org</a>.
Our staff will process your request and get back to you within 2 business days.
The points are cumulative and never reset to zero, so you keep accruing points even if you claim any rewards.</p>
<p>Please remember that the quality of the work we submit is of utmost importance,
as it can influence the lives of people affected by humanitarian crises.
Only work that meets our minimum quality standards (as described in <a  class="custom-link" href="https://translatorswithoutborders.org/wp-content/uploads/2022/03/Plain-language-Code-of-Conduct-for-Translators.pdf" target="_blank">TWB’s Code of Conduct</a>)
will qualify towards our Community Recognition Program.
If you work on a revision task or a proofreading/approval task and notice that the quality of the translation is not fit for purpose, please contact us at
<a  class="custom-link" href="mailto:recognition@translatorswithoutborders.org?subject=Feedback" target="_blank">recognition@translatorswithoutborders.org</a>.
</p>
 
<h2><span style="color: #9e6100;">Rewards offered</span></h2>
<div class="row">
<div class="col-xs-12 col-md-6">
<div class="border border-1 border-primaryDark rounded-3 w-100">

   <div class=" d-flex  border-bottom border-primaryDark  p-2  px-2 " >

      <div class="fw-bold me-4">Points</div>
      <div class="fw-bold flex-grow-1 text-end" >Reward</div>
    </div>

       <div class=" d-flex  border-bottom border-primaryDark  p-2" >

      <div class="  me-4 ">5,000</div>
      <div class=" flex-grow-1 text-end" >Certification of volunteer activity</div>
    </div>

      <div class=" d-flex  border-bottom border-primaryDark  p-2" >

      <div class=" me-4">15,000</div>
      <div class=" flex-grow-1 text-end" >Reference Letter</div>
    </div>


     <div class=" d-flex   p-2" >

      <div class="  me-4">30,000</div>
      <div class=" flex-grow-1 text-end " >Recommendation on professional platforms</div>
    </div>


</div>

<h2 class="mt-3"><span style="color: #9e6100;">How do I earn points?</span></h2>
<p>The points are calculated as follows:</p>

<div class=" border border-1 border-primaryDark rounded-3 mt-4 w-100 items-center ">

   <div class=" d-flex  border-bottom border-primaryDark  p-2 px-2" >

      <div class="fw-bold w-25 me-2">Type of task</div>
      <div class="fw-bold w-25 me-4 text-end" >Unit</div>
         <div class="fw-bold flex-grow-1 text-end" >Points accrued per unit </div>

    </div>

       <div class=" d-flex  border-bottom border-primaryDark p-2" >

         <div class=" w-25 me-2">Translation</div>
        <div class=" w-25 me-4 text-end" > 1 word</div>
         <div class="flex-grow-1 text-end" > 1 </div>

        </div>

      <div class=" d-flex  border-bottom border-primaryDark  p-2" >

        <div class="w-25 me-2">Revision</div>
        <div class="  w-25 me-4 text-end" > 1 word</div>
         <div class="flex-grow-1 text-end" > 0.5 </div>

     
     </div>


     <div class=" d-flex p-2 border-bottom border-primaryDark" >

       <div class=" w-25 me-2"> Proofreading/Approval</div>
        <div class="  w-25 me-4 text-end" > 1 word</div>
         <div class="flex-grow-1 text-end" > 0.25 </div>

    </div>

     <div class=" d-flex p-2 border-bottom border-primaryDark " >

       <div class=" w-25 me-2"> Transcription</div>
        <div class=" w-25 me-4 text-end" > 1 word</div>
         <div class="flex-grow-1 text-end" > 0.5 </div>

    </div>
      <div class=" d-flex p-2 border-bottom border-primaryDark " >

       <div class=" w-25 me-2"> Voice Recording</div>
        <div class="  w-25 me-4 text-end" > 1 word</div>
         <div class="flex-grow-1 text-end" > 1 </div>

    </div>

     <div class=" d-flex p-2 border-bottom border-primaryDark " >

       <div class=" w-25 me-2"> Translation of subtitles</div>
        <div class="  w-25 me-4 text-end" > 1 word</div>
         <div class="flex-grow-1 text-end" > 1 </div>

    </div>

       <div class=" d-flex p-2 border-bottom border-primaryDark" >

       <div class=" w-25 me-2"> Revision of subtitles</div>
        <div class=" w-25 me-4 text-end" > 1 word</div>
         <div class="flex-grow-1 text-end" > 0.5 </div>

    </div>

      <div class=" d-flex p-2 " >

       <div class=" w-25 me-2"> Terminology</div>
        <div class="  w-25 me-4 text-end" > 1 term</div>
         <div class="flex-grow-1 text-end" >10 </div>

    </div>


</div>
</div>



<div class="col-xs-12 col-md-6">

      <div class="bg-yellowish  text-dark d-flex justify-content-between rounded-3 w-100  p-2">
                

                           

                            {if empty($user_badges['strategic_points'])}
                                
                                <div class="d-flex flex-column">
                                <img src="{urlFor name='home'}ui/img/TWB_Logo.svg" class="mb-5 w-75" />

                                <h4 class="fw-bold mb-3 fs-3">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_badge_name)}</h4>
                                <h5 class="mb-3">
                                    <span class="fw-bold fs-3 mb-2">{$user_badges['recognition_points']}</span><br />
                                    <span >RECOGNITION POINTS</span>
                                </h5>
                                </div>
                            {else}
                                <div class="d-flex flex-column">
                                <img src="{urlFor name='home'}ui/img/TWB_Logo.svg" class="mb-5 w-75" />
                                <h4 class="fw-bold mb-3">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_badge_name)}</h4>
                                <p class=" mb-3">
                                    <span class="fw-bold fs-3">{$user_badges['recognition_points']}</span>
                                    <span >RECOGNITION POINTS</span>
                                    <span > of which
                                        <span class="fw-bold fs-3">{$user_badges['strategic_points']}</span>
                                        POINTS
                                    </span><br />
                                    <span >IN STRATEGIC LANGUAGES</span>
                                </p>
                                </div>
                                
                            {/if}

                         

                            <div class="">

                            <img src="{urlFor name='home'}ui/img/profile_badge"  />

                            </div>                           

                            
                        </div>

</div>

</div>

</div>

<div>

{if !empty($user_has_strategic_languages) || !empty($user_badges['strategic_points'])}
<div class="bg-body mt-2 rounded-3 p-4 fs-4">    

<p>Our Community Recognition Program also includes monetary rewards for some marginalized languages.
Speakers of marginalized languages often face high connectivity costs when offering their online support.
These monetary rewards aim to cover some of those expenses.
We hope that this will allow speakers of marginalized languages to volunteer more with us.</p>
<p>We also offer monetary rewards for languages that are crucial to supporting ongoing humanitarian responses.
When we advocate for language inclusion and collaborate with partner organizations on the ground, it generates more urgent and more frequent requests for relevant language communities.
We are committed to recognizing the work of our volunteers who step up to support their communities in times of crisis.
We hope that the rewards included in our Community Recognition Program may help community members who are personally affected.</p>
<p>This program is not a form of employment, and rewards do not constitute payment for services.</p>
<p>Currently, the languages for which we can offer monetary rewards are Amharic; Bengali; Bengali, India; Bura-Pabir; Burmese; Chadian Arabic; Chadian Arabic Latin; Chittagonian; Dari; Fulah; Haitian; Hausa; Kanuri; Kibaku; Kurdish Bahdini; Kurdish Kurmanji; Kurdish Sorani; Lingala; Ganda; Wandala (formerly Mandara); Marghi Central; Mongo; Nande; Ngombe; Oromo; Pushto; Pushto, Pakistan; Rohingya Bengali; Rohingya Latin; Romani; Shi; Somali; Somali, Ethiopia; Swahili; Swahili, Congo; Tigrinya; Ukrainian; Lamang (formerly Waha).</p>
<p>This list may change over time, depending on our strategic needs and budgetary constraints related to our crisis response work and international programs.
If a language is to be removed from this list, the community will be informed beforehand.</p>

<h2>
<span style="color: #9e6100;">Rewards offered for work in strategic languages</span>

</h2>

{if empty($user_has_strategic_languages) || $user_has_strategic_languages[0]['nigeria'] == 0}
<div class="row">
<div class="border border-1 border-primaryDark rounded-3 col-xs-12 col-md-8 fs-5">

   <div class=" d-flex  border-bottom border-primaryDark  justify-content-between p-2  px-2 " >

      <div class="fw-bold ">Points in strategic languages</div>
      <div class="fw-bold " >Status</div>
      <div class="fw-bold " >Recognition reward</div>
    </div>

       <div class="d-flex  border-bottom border-primaryDark  justify-content-between p-2  px-2" >

       <div class=" w-50"> 5,000</div>
       <div class="" > TWB New Community Member</div>
        <div class="flex-grow-1 text-end" > 10 USD phone top-up or online voucher </div>
    </div>
    
        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
    
        <div class="  w-50 "> 25,000</div>
        <div class="  " > TWB Traveler</div>
         <div class="flex-grow-1 text-end" > 100 USD bank transfer</div>
        </div>
    
       
        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
    
        <div class=" w-50"> 50,000</div>
        <div class=" " > TWB Pathfinder</div>
         <div class="flex-grow-1 text-end" > 150 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
    
        <div class="w-50"> 100,000</div>
        <div class="" > TWB Explorer</div>
         <div class="flex-grow-1 text-end" > 400 USD bank transfer</div>
        </div>
    

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
    
        <div class=" w-50"> 200,000</div>
        <div class=" " > TWB Navigator</div>
         <div class="flex-grow-1 text-end" > 750 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
    
        <div class=" w-50"> 300,000</div>
        <div class=" " > TWB Voyager</div>
         <div class="flex-grow-1 text-end" > 750 USD bank transfer</div>
        </div>
    


        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
    
        <div class="  w-50"> 400,000</div>
        <div class="" > TWB Trailblazer</div>
         <div class="flex-grow-1 text-end" > 750 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-50"> 500,000</div>
        <div class=" " > TWB Pioneer</div>
         <div class="flex-grow-1 text-end" > 750 USD bank transfer</div>
        </div>
        </div>
        

        {else}

        <div class="border border-1 border-primaryDark rounded-3 col-xs-12 col-md-6 fs-5">
        
        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 fw-bold"> Threshold</div>
        <div class=" me-4 fw-bold" > Status</div>
         <div class="flex-grow-1 fw-bold text-end" > Recognition reward</div>
        </div>
        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 fw-bold"> First task delivered</div>
        <div class=" me-4 fw-bold" > TWB Translator</div>
         <div class="flex-grow-1 fw-bold text-end" > 5 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 "> 2,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1 text-end" > 5 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 "> 5,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 10 USD bank transfer</div>
        </div>
        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  7,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 10 USD bank transfer</div>
        </div>


        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 fw-bold"> 10,000 Points</div>
        <div class=" me-4 fw-bold" > </div>
         <div class="flex-grow-1 fw-bold text-end" > 10 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  12,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 10 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  15,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 10 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  17,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 10 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  20,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 10 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  22,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 10 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 fw-bold"> 25,500 Points</div>
        <div class=" me-4 fw-bold" > TWB Traveler</div>
         <div class="flex-grow-1 fw-bold text-end" > 10 USD bank transfer</div>
        </div>


        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  27,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 15 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  30,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 15 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  32,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 15 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  35,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 15 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  37,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 15 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  40,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 15 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  42,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 15 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  45,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 15 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  47,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 15 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 fw-bold"> 50,000 Points</div>
        <div class=" me-4 fw-bold" > TWB Pathfinder</div>
         <div class="flex-grow-1 fw-bold text-end" > 15 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  52,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  55,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" >20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  57,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  60,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  62,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  65,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  67,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  70,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  72,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  75,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  77,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  80,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  82,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  85,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  87,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  90,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  92,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  95,000 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 ">  97,500 Points</div>
        <div class=" me-4 " > </div>
         <div class="flex-grow-1  text-end" > 20 USD bank transfer</div>
        </div>

        <div class=" d-flex  border-bottom border-primaryDark  p-2" >
        <div class=" w-25 me-2 fw-bold ">  100,000 Points</div>
        <div class=" me-4 fw-bold  " > TWB Explorer</div>
         <div class="flex-grow-1  text-end fw-bold" > 20 USD bank transfer</div>
        </div>

</div>


{/if}
</div>


</div>
{/if}

{/if}


{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}

{/if}



{if $roles & ($SITE_ADMIN + $COMMUNITY_OFFICER)}



<div class="mt-2 p-4 rounded-3 bg-body">
<div class="d-flex justify-content-between mb-4 flex-wrap ">
{if !empty($valid_key_certificate)}
    {assign var="valid_key" value=$valid_key_certificate[0]}
  

    <a href='{urlFor name="user-print-certificate" options="valid_key.$valid_key"}' class=" btnSuccess " target="_blank" >
        <i class=" fa-solid fa-print me-2"></i> Generate Certificate
    </a>
 

{/if}

<form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}" >
    <input type="submit" class="btnPrimary text-white border-0 border-0 mt-2 mt-md-0 border-0" name="PrintRequest" value="Request Certification of Volunteer Activity" />
    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
</form>
</div>
<div class="table-responsive fs-5">
<table id="printrequest" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Request Date</th>
                <th>Request Made By</th>
                <th>No of Words upon Request</th>
                <th>No of Hours upon Request</th>
                <th>Validation Key</th>
            </tr>
        </thead>

    </table>
</div>
</div>

<div class="mt-2 p-4 rounded-3 bg-body">
<div class="d-flex justify-content-between mb-4 items-center flex-wrap ">
{if !empty($valid_key_reference_letter)}
    {assign var="valid_key" value=$valid_key_reference_letter[0]}
    <a href='{urlFor name="downloadletter" options="valid_key.$valid_key"}' class="btnSuccess" target="_blank" ">
        <i class=" fa-solid fa-print icon-white me-2"></i> Generate Letter
    </a>
{/if}
<form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}" >
    <input type="submit" class="btnPrimary text-white border-0 border-0" name="PrintRequestLetter" value="Request Reference Letter" />
    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
</form>
</div>
<div class="table-responsive fs-5">
<table id="printrequestletter" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Request Date</th>
                <th>Request Made By</th>
                <th>No of Words upon Request</th>
                <th>No of Hours upon Request</th>
                <th>Validation Key</th>
            </tr>
        </thead>

    </table>
</div>
    


{/if}

</div>



{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
    <div class="mt-2 p-4 rounded-3 bg-body">
{if $roles & ($SITE_ADMIN + $COMMUNITY_OFFICER)}
<form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}" class="">
    <input type="submit" class="btnPrimary text-white border-0 border-0" name="send_contract" value="Send Contract to Linguist" />
    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
</form>
{/if}
{if !empty($sent_contracts)}
<div class="table-responsive fs-5 mt-2">
<table style="width:100%">
    <thead>
        <tr>
            <td><strong>Contract Type</strong></td>
            <td><strong>Admin</strong></td>
            <td><strong>Status</strong></td>
            <td><strong>Update Date</strong></td>
            <td><strong>Contract Date</strong></td>
        </tr>
    </thead>
    {foreach $sent_contracts as $sent_contract}
        <tr>
            <td>{$sent_contract['type']}</td>
            <td><a href="{urlFor name="user-public-profile" options="user_id.{$sent_contract['admin_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($sent_contract['first_name'])} {TemplateHelper::uiCleanseHTML($sent_contract['last_name'])}</a></td>
            <td>
                {if     $sent_contract['status'] == 'recipient-sent'}Sent to Linguist
                {elseif $sent_contract['status'] == 'recipient-delivered'}Viewed by Linguist
                {elseif $sent_contract['status'] == 'recipient-completed'}Signed by Linguist
                {elseif $sent_contract['status'] == 'envelope-completed'}Contract Completed
                {else}{$sent_contract['status']}
                {/if}
            </td>
            <td>{$sent_contract['update_date']}</td>
            <td>{$sent_contract['contract_date']}</td>
        </tr>
    {/foreach}
</table>
</div>
{/if}
</div>
{/if}



{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
    <div class="mt-2 p-4 rounded-3 bg-body">


<h4 class="fw-bold">Administrative Section{if !empty($tracked_registration)} (Tracked Registration: {$tracked_registration}){/if}</h4>
<div class="d-flex justify-content-between fs-5">
<div class="w-25 fw-bold">Comment</div>
<div class="w-25 fw-bold">Willingness to work again score(1 to 5)</div>
<div class="w-25 fw-bold">Created</div>
<div class="w-25 fw-bold">Created by</div>

</div>

<form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
<div class="d-flex mt-2 mb-2 w-50 ">

    <div class="flex-grow-1 w-50 ">
    <input type='text' value="" name="comment" id="comment" class=" form-control form-control-sm" />
    </div>
    <div class="d-flex flex-column flex-grow-1 w-50">

    <input type='text' value="" name="work_again" id="work_again" class=" form-control form-control-sm" />
    <input type="submit" class="btnPrimary text-white border-0 border-0 mt-1" name="admin_comment" value="Submit"  />
{if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    
   
    {if !empty($admin_comments_average)}
    <strong class="mt-2">
    Average: {$admin_comments_average}</strong>
    {/if}
    </div>

</div>
</form>  

<div class="table-responsive fs-5">

<table class="table" border="0">

{foreach $admin_comments as $admin_comment}
<tr valign="top">
    <td style="width: 30%"><ul><li>{$admin_comment['admin_comment']|escape:'html':'UTF-8'}</li></ul></td>
    <td style="width: 22%">{$admin_comment['work_again']}</td>
    <td style="width: 18%">{$admin_comment['created']}</td>
    <td style="width: 18%">{$admin_comment['admin_email']}</td>
    <td style="width: 12%">
        <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
            <input type="submit" class="btn btn-danger" name="mark_comment_delete" value="Delete" onclick="return confirm('Are you sure you want to permanently delete this comment?')" />
            <input type="hidden" name="comment_id" value="{$admin_comment['id']}" />
            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
        </form>
    </td>
</tr>
{/foreach}
</table>

  

</div>


<hr class="bg-light-subtle"/>

<h4 class="fw-bold">Recognition Program Points Adjustment (for Non Strategic languages)</h4>
<div class="d-flex justify-content-between fs-5">
<div class="w-25 fw-bold">Comment</div>
<div class="w-25 fw-bold">Recognition points adjustement</div>
<div class="w-25 fw-bold">Created</div>
<div class="w-25 fw-bold">Created by</div>

</div>

<form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
<div class="d-flex mt-2 mb-2 w-50 ">
    <div class="flex-grow-1 w-50 ">
    <input type='text' value="" name="comment" id="comment" class=" form-control form-control-sm" />
    </div>
    <div class="d-flex flex-column flex-grow-1 w-50">
    <input type='text' value="" name="points" id="points" class="form-control form-control-sm" />
    <input type="submit"  name="mark_adjust_points" value="Submit" class="btn btn-primary text-white mt-1" /> 
    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </div>

</div>
</form>
<div class="table-responsive fs-5">

<table class="table">
{foreach $adjust_points as $adjust_point}
    <tr valign="top">
        <td style="width: 30%"><ul><li>{$adjust_point['admin_comment']|escape:'html':'UTF-8'}</li></ul></td>
        <td style="width: 22%">{$adjust_point['points']}</td>
        <td style="width: 18%">{$adjust_point['created']}</td>
        <td style="width: 18%">{$adjust_point['admin_email']}</td>
        <td style="width: 12%">
        <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
        <input type="submit" class="btn btn-danger" name="mark_points_delete" value="Delete" onclick="return confirm('Are you sure you want to permanently delete this points adjustment?')" />
        <input type="hidden" name="comment_id" value="{$adjust_point['id']}" />
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
        </td>
    </tr>
{/foreach}
</table>

 
</div>
 


<hr/>


<h4 class="fw-bold">Recognition Program Points Adjustment (for  Strategic languages)</h4>
<div class="d-flex justify-content-between fs-5">
<div class="w-25 fw-bold">Comment</div>
<div class="w-25 fw-bold">Recognition points adjustement</div>
<div class="w-25 fw-bold">Created</div>
<div class="w-25 fw-bold">Created by</div>

</div>

<form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
<div class="d-flex mt-2 mb-2 w-50 ">
    <div class="flex-grow-1 w-50 ">
    <input type='text' value="" name="comment" id="comment" class="form-control form-control-sm"  />
    </div>
    <div class="d-flex flex-column flex-grow-1 w-50">
   <input type='text' value="" name="points" id="points" class="form-control form-control-sm" />
  <input type="submit"  name="mark_adjust_points_strategic" value="Submit" class="btn btn-primary text-white  mt-1" name="mark_adjust_points_strategic"  />
{if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}  
  </div>

</div>
</form>
<div class="table-responsive fs-5 mb-3">

<table class="table" border="0">
{foreach $adjust_points_strategic as $adjust_point}
    <tr valign="top">
        <td style="width: 30%"><ul><li>{$adjust_point['admin_comment']|escape:'html':'UTF-8'}</li></ul></td>
        <td style="width: 22%">{$adjust_point['points']}</td>
        <td style="width: 18%">{$adjust_point['created']}</td>
        <td style="width: 18%">{$adjust_point['admin_email']}</td>
        <td style="width: 12%">
        <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                <input type="submit" class="btn btn-danger" name="mark_points_delete_strategic" value="Delete" onclick="return confirm('Are you sure you want to permanently delete this points adjustment?')" />
                <input type="hidden" name="comment_id" value="{$adjust_point['id']}" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
            
        </td>
    </tr>
{/foreach}
</table>
 

    </div>
 




<form method="post"  action="{urlFor name="user-public-profile" options="user_id.$user_id"}">

<div class="table-responsive fs-5 ">
<table class="table" >
    <tr valign="top">
        <td style="width: 25%"><h4 class="fw-bold">Volunteer Restrictions</h4></td>
        <td style="width: 25%"></td>
        <td style="width: 25%"></td>
        <td style="width: 25%"></td>
    </tr>
    <tr valign="top">
        <td style="width: 25%"><strong>Maximum number of claimed/in progress tasks volunteer can have at any one time (0 => no limit)</strong></td>
        <td style="width: 25%"><strong>Comma separated list of task types volunteer can claim (e.g. 2 => Translation, nothing in field (not a blank) => any)</strong></td>
        <td style="width: 25%"><strong>Comma separated list of partner IDs for which the volunteer cannot claim tasks</strong></td>
        <td style="width: 25%"><strong>Restrict volunteer from editing native language, language pairs and task stream (1 => restrict, 0 => none)</strong></td>
    </tr>
    <tr valign="top">
        <td style="width: 25%"><input class="form-control form-control-sm" type='text' value="{$user_task_limitation['max_not_comlete_tasks']}" name="max_not_comlete_tasks" id="max_not_comlete_tasks" /></td>
        <td style="width: 25%"><input class="form-control form-control-sm" type='text' value="{$user_task_limitation['allowed_types']}"         name="allowed_types"         id="allowed_types" /></td>
        <td style="width: 25%"><input class="form-control form-control-sm" type='text' value="{$user_task_limitation['excluded_orgs']}"         name="excluded_orgs"         id="excluded_orgs" /></td>
        <td style="width: 25%"><input class="form-control form-control-sm" type='text' value="{$user_task_limitation['limit_profile_changes']}" name="limit_profile_changes" id="limit_profile_changes" /></td>
    </tr>
    <tr valign="top">
        <td style="width: 25%"><input type="submit" class="btn btn-primary text-white " name="mark_user_task_limitation" value="Submit" /></td>
        <td style="width: 25%"></td>
        <td style="width: 25%"></td>
        <td style="width: 25%"></td>
    </tr>
</table>
</div>
{if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
</form>


{if $roles & ($SITE_ADMIN + $COMMUNITY_OFFICER)}<form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">{/if}
<div class="table-responsive fs-5">
<table class="table" >
    <tr valign="top">
        <td style="width: 20%"><h4 class="fw-bold">Linguist Payment Information</h4></td>
        <td style="width: 20%"></td>
        <td style="width: 20%"></td>
        <td style="width: 40%"></td>
    </tr>
    <tr valign="top">
        <td style="width: 20%"><strong>Admin</strong></td>
        <td style="width: 20%"><strong>Official Name</strong></td>
        <td style="width: 20%"><strong>Country</strong></td>
        <td style="width: 40%"><strong>Google Drive Link</strong></td>
    </tr>
    <tr valign="top">
        <td style="width: 20%"><a href="{urlFor name="user-public-profile" options="user_id.{$linguist_payment_information['admin_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($linguist_payment_information['admin_name'])}</a>{if empty($linguist_payment_information['admin_name'])}-{/if}</td>
        <td style="width: 20%"><input class="form-control form-control-sm" type='text' value="{if !empty($linguist_payment_information['linguist_name'])}{$linguist_payment_information['linguist_name']}{else}{if isset($userPersonalInfo) && !empty($userPersonalInfo->getFirstName())}{$userPersonalInfo->getFirstName()}{/if}{if isset($userPersonalInfo) && !empty($userPersonalInfo->getLastName())} {$userPersonalInfo->getLastName()}{/if}{/if}" name="linguist_name" id="linguist_name" /></td>
        <td style="width: 20%">
            <select class="form-select form-select-sm" name="country_id" id="country">
                <option value="">--Select--</option>
                {foreach $countries as $country}
                    {if $country->getCode() != 'LATN' && $country->getCode() != 'CYRL' && $country->getCode() != '419' && $country->getCode() != 'HANS' && $country->getCode() != 'HANT' && $country->getCode() != 'ARAB' && $country->getCode() != 'BENG' && $country->getCode() != 'ROHG'}
                        <option value="{$country->getId()}" {if $country->getId() == $linguist_payment_information['country_id']}selected="selected"{/if}>{$country->getName()|escape:'html':'UTF-8'}</option>
                    {/if}
                {/foreach}
            </select>
        </td>
        <td style="width: 40%"><input class="form-control form-control-sm" type='text' value="{$linguist_payment_information['google_drive_link']}" name="google_drive_link" id="google_drive_link" /></td>
    </tr>
    {if $roles & ($SITE_ADMIN + $COMMUNITY_OFFICER)}
    <tr valign="top">
        <td style="width: 20%"><input type="submit" class="btn btn-primary text-white  border-0" name="mark_linguist_payment_information" value="Submit" /></td>
        <td style="width: 20%"></td>
        <td style="width: 20%"></td>
        <td style="width: 40%"></td>
    </tr>
    {/if}
</table>
</div>
{if $roles & ($SITE_ADMIN + $COMMUNITY_OFFICER)}
{if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
</form>
{/if}

</div>
</div>
{/if}



{if $private_access}
    <div class="mt-4 rounded-3 p-4 bg-body fs-5">
  
    <div class="d-flex justify-content-between flex-wrap">
        <h3 class="fw-bold">
            {Localisation::getTranslation('user_public_profile_reference_email')} 
            <span class="text-muted fs-5">{Localisation::getTranslation('user_public_profile_16')}</span> </h3>
            <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}"> 
               
                <input type="submit" class="btn btn-primary text-white border-0" name="referenceRequest" 
                    value=" {Localisation::getTranslation('user_public_profile_request_reference')}" />

                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
           
    </div>
    <p>{Localisation::getTranslation('user_public_profile_15')}</p>
    {if isset($requestSuccess)}
        <p class="alert alert-success">{Localisation::getTranslation('user_public_profile_reference_request_success')}</p>
    {/if}
    </div>  
{/if}


{if $private_access || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}


    {if !empty($badges)}
        <div class="mt-4 rounded-3 p-4 bg-body fs-5">
        <div class='d-flex justify-content-between flex-wrap'>
            <h4 class="fw-bold">{Localisation::getTranslation('common_badges')}<span class="text-muted fs-5"> {Localisation::getTranslation('user_public_profile_4')}</span></h4>
                <a href='{urlFor name="badge-list"}' class=' btn btn-primary text-white  '>
                    <i class="icon-list icon-white"></i> {Localisation::getTranslation('user_public_profile_list_all_badges')}
                </a>
           
        </div>

        {foreach $badges as $badge}
                {assign var="user_id" value=$this_user->getId()} 
                    {if $private_access}
                        <form method="post" class="mt-2" action="{urlFor name="user-public-profile" options="user_id.$user_id"}" >
                           
                            <input type="hidden" name="badge_id" value="{$badge->getId()}" />
                            <input type="submit" class='btn btn-primary' name="revokeBadge" value="{Localisation::getTranslation('user_public_profile_remove_badge')}" 
                           onclick="return confirm('{Localisation::getTranslation('user_public_profile_5')}')"/>
                            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                        </form>   
                    {/if}
                {assign var="org_id" value=$badge->getOwnerId()}
                {assign var="org" value=$orgList[$org_id]}
                <h3>
                    <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                        {$org->getName()}</a> - {TemplateHelper::uiCleanseHTML($badge->getTitle())}
                </h3>
                <p>{TemplateHelper::uiCleanseHTML($badge->getDescription())}</p>
    
        {/foreach}

 </div>

  
    
  {/if}


{if ($private_access && $user_task_limitation_current_user['limit_profile_changes'] == 0) || ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER))}
 <div class="mt-4 rounded-3 p-4 bg-body fs-5">
    <div class="d-flex justify-content-between flex-wrap">
        <h3 class="fw-bold">{Localisation::getTranslation('user_public_profile_task_stream_notifications')} <span class="text-muted fs-5">{Localisation::getTranslation('user_public_profile_6')}</span></h3>
            <a href="{urlFor name="stream-notification-edit" options="user_id.$user_id"}" class=" btn btn-primary text-white ">
                <i class="fa-solid fa-screwdriver-wrench me-2"></i> {Localisation::getTranslation('user_public_profile_edit_notifications')}
            </a>
      
    </div>
    <p>
        {if isset($interval)}
            <p>
                {Localisation::getTranslation('common_how_often_receiving_emails')}
                <strong>{$interval}</strong>
            </p>
            <p>
                {if $lastSent != null}
                    {sprintf(Localisation::getTranslation('common_the_last_email_was_sent_on'), {$lastSent})}
                {else}
                    {Localisation::getTranslation('common_no_emails_have_been_sent_yet')}
                {/if}
            </p>
        {else}
            {Localisation::getTranslation('common_you_are_not_currently_receiving_task_stream_notification_emails')}
        {/if}
    </p>
    
    </div> 
{/if}


 <div class="mt-4 rounded-3 p-4 bg-body fs-5">
<div class="d-flex justify-content-between flex-wrap">
    <h3 class="fw-bold">{Localisation::getTranslation('common_tags')}<span class="text-muted fs-5"> {Localisation::getTranslation('user_public_profile_8')}</span></h3>
        <a href='{urlFor name='tags-list'}' class="btn btn-primary text-white">
            <i class="fa-solid fa-search me-1"></i> {Localisation::getTranslation('user_public_profile_search_for_tags')}
        </a>
   
</div>


{if isset($user_tags) && count($user_tags) > 0}

    {foreach $user_tags as $tag}
        <p>
            {assign var="tag_label" value=TemplateHelper::uiCleanseHTML($tag->getLabel())}
            {assign var="tagId" value=$tag->getId()}
            <a class="custom-link" class="tag" href="{urlFor name="tag-details" options="id.$tagId"}">
                <span class="label">{$tag_label}</span>
            </a>
        </p>
    {/foreach}
{else}
    <p class="alert alert-info mt-2">
        {Localisation::getTranslation('user_public_profile_9')}
    </p>
   
{/if}
</div> 

{if isset($user_orgs)}
    {if count($user_orgs) > 0}
    <div class="mt-4 rounded-3 p-4 bg-body fs-5">

    {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}

        <div class='d-flex justify-content-between flex-wrap'>
            <h3 class="fw-bold">
                {Localisation::getTranslation('common_organisations')} <small class="text-muted fs-5">{Localisation::getTranslation('user_public_profile_10')}</small> </h3>
                <div>
                <a href="{urlFor name='org-search'}" class="btn btn-primary text-white " >
                    <i class="fa-solid fa-search me-1"></i> {Localisation::getTranslation('common_search_for_organisations')}
                </a>
                </div>
       
        </div>
    {/if}

        {foreach $user_orgs as $org}
            <div class="row">
                {assign var="org_id" value=$org['id']}
                {assign var="user_id" value=$this_user->getId()}
                <div class="span8">
                    <h3>
                        <i class="fa-solid fa-briefcase me-3"></i>
                        <a href="{urlFor name="org-public-profile" class="custom-link fw-bold" options="org_id.$org_id"}" class="custom-link fw-bold">{$org['name']}</a>
                    </h3>
                    {if $org['roles']&$NGO_ADMIN}NGO ADMIN{if $org['roles']&$NGO_PROJECT_OFFICER},{/if}{/if} {if $org['roles']&$NGO_PROJECT_OFFICER}NGO PROJECT OFFICER{/if}
                    {if $org['roles']&$NGO_LINGUIST}NGO LINGUIST{if !($org['roles']&$LINGUIST)} (exclusive){/if}{/if}
                </div>
                <div class="row">
                    <form method="post" class="pull-right" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                        {if $private_access}
                            <i class="icon-fire icon-white" style="position:relative; right:-25px; top:1px;"></i>
                            <input type="hidden" name="org_id" value="{$org_id}" />
                            <input type="submit" class='btnPrimary' name="revoke" value="    {Localisation::getTranslation('user_public_profile_leave_organisation')}" 
                                   onclick="return confirm('{Localisation::getTranslation('user_public_profile_11')}')"/>
                        {/if}                      
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                </div>
                <div class="span8">
                    <p>
                        <strong>About Me</strong><br/>
                        
                        {if $org['biography'] == ''}
                            {Localisation::getTranslation('org_public_profile_no_biography_listed')}
                        {else}                            
                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org['biography'])}
                        {/if}
                    </p>
                    <p>
                    <strong>{Localisation::getTranslation('common_home_page')}</strong><br/>
                    {if $org['homepage'] != "https://"}
                        <a target="_blank" href="{$org['homepage']}">{$org['homepage']}</a>
                    {else}
                        {Localisation::getTranslation('org_public_profile_no_home_page_listed')}
                    {/if}
                    </p>
                </div>
            </div>
           
            <hr class="bg-light-subtle"/>
        {/foreach}
        
        </div>
    {/if} 
{/if}

{if isset($archivedJobs)}
    {if count($archivedJobs) > 0}
    <div class="mt-4 rounded-3 p-4 bg-body fs-5">
        <div class='d-flex flex-wrap'>
            <h3 class="fw-bold">{Localisation::getTranslation('common_archived_tasks')} <span class="text-muted fs-5">{Localisation::getTranslation('user_public_profile_14')}</span></h3>
                {if $private_access}
                    <a href='{urlFor name="archived-tasks" options="page_no.1"}' class=' btn btn-primary text-white '>
                        <i class=" fa-solid fa-list me-2"></i> {Localisation::getTranslation('user_public_profile_list_all_archived_tasks')}
                    </a>
                {/if}
      
        </div>

        {foreach $archivedJobs as $job}
            {include file="task/task.profile-display.tpl" task=$job}
        {/foreach}
        
    </div>
    

    {/if}

    
   
{/if}

{/if}



   </div>
   </div>
   
  
   
{/if}



{include file='footer2.tpl'}
