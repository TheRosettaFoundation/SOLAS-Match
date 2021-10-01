<!-- Editor Hint: ¿áéíóú -->
{include file='header.tpl'}

<span class="hidden">

    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
    <div id="siteAPI">{$siteAPI}</div>
    <div id="user_id">{$user_id}</div>
    <div id="userQualifiedPairsLimit">{$userQualifiedPairsLimit}</div>
    <div id="userQualifiedPairsCount">{$userQualifiedPairsCount}</div>
    {assign var="i" value=0}
    {foreach $userQualifiedPairs as $userQualifiedPair}
        <div id="userQualifiedPairLanguageCodeSource_{$i}">{$userQualifiedPair['language_code_source']}</div>
        <div id="userQualifiedPairLanguageCodeTarget_{$i}">{$userQualifiedPair['language_code_target']}</div>
        <div id="userQualifiedPairQualificationLevel_{$i}">{$userQualifiedPair['qualification_level']}</div>
        {assign var="i" value=$i+1}
    {/foreach}
    <div id="isSiteAdmin">{if $isSiteAdmin}1{else}0{/if}</div>
    <div id="langPrefSelectCodeSaved">{$langPrefSelectCode}</div>
    <input type='text' value="{$langPrefSelectCode}" id="langPrefSelect"/>
    <div id="capabilityCount">{$capabilityCount}</div>
    <div id="expertiseCount">{$expertiseCount}</div>

    <!-- Templates... -->
    <div id="template_language_options">
        <option value="0"></option>
        {foreach from=$language_selection key=codes item=language}
            <option value="{$codes}" >{$language}</option>
        {/foreach}
    </div>

    <div id="template_qualification_options">
        <option value="1">{Localisation::getTranslation('user_qualification_level_1')}</option>
        <option value="2">{Localisation::getTranslation('user_qualification_level_2')}</option>
        <option value="3">{Localisation::getTranslation('user_qualification_level_3')}</option>
    </div>

</span>

<div class="well">

    {if isset($flash['error'])}
        <p class="alert alert-error">
            {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
        </p>
    {/if}

    <div id="placeholder_for_errors_1"></div>
    
                <div id="loading_warning">
                    <p><i>{Localisation::getTranslation('common_loading')}</i></p>
                </div> -->

              <!--  <div id="language_area">
                    <div id = "nativeLanguageDiv">
                        <label><strong>{Localisation::getTranslation('common_native_language')}: <span style="color: red">*</span></strong></label>
                        <p class="desc">Your native language is a language you have been exposed to from birth or within a long period of your life or education. It generally is the language you are more fluent in.</p>
                        <select name="nativeLanguageSelect" id="nativeLanguageSelect" style="width: 41%">
                            <option value=""></option>
                            {foreach $languages as $language}
                                <option value="{$language->getCode()}" {if $language->getCode() == $nativeLanguageSelectCode}selected="selected"{/if}>{$language->getName()}</option>
                            {/foreach}
                        </select>
                        <select name="nativeCountrySelect" id="nativeCountrySelect" style="width: 41%">
                            <option value=""></option>
                            {foreach $countries as $country}
                                {if $country->getCode() != '90' && $country->getCode() != '91' && $country->getCode() != '49' && $country->getCode() != '92' && $country->getCode() != '93' && $country->getCode() != '94'}
                                <option value="{$country->getCode()}" {if $country->getCode() == $nativeCountrySelectCode}selected="selected"{/if}>{$country->getName()}</option>
                                {/if}
                            {/foreach}
                        </select>
                    </div> -->

                   <!-- <div id="secondaryLanguageDiv">
                        <hr/>
                        <label><strong>Language Pairs (From/To): <span style="color: red">*</span></strong></label>
                        <p class="desc">In general, people translate from any language they are proficient in, into their first/native language. For example, if the language you are more fluent in is Spanish, and you are also fluent in English and French, you can translate from English to Spanish and from French to Spanish.</p>
                        <button onclick="addSecondaryLanguage(); return false;" class="btn btn-success" id="addLanguageButton" {if $userQualifiedPairsCount >= $userQualifiedPairsLimit}disabled{/if}>
                            <i class="icon-upload icon-white"></i> {Localisation::getTranslation('user_private_profile_add_secondary_language')}
                        </button>
                        <button onclick="removeSecondaryLanguage(); return false;" class="btn btn-inverse" id="removeLanguageButton" {if $userQualifiedPairsCount <= 1}disabled{/if}>
                            <i class="icon-fire icon-white"></i> {Localisation::getTranslation('common_remove')}
                        </button>
                        <hr/>
                    </div> -->
                </div>


                <label for='communications_consent'><strong>Communications Consent:</strong></label>
                <p class="desc">We’d like to keep in touch with you about the lives we can change thanks to your support.</p>
                <input type="checkbox" value="1" name="communications_consent" id="communications_consent" {if $communications_consent}checked="checked"{/if} /> Subscribe to the TWB email newsletter. <i>You can unsubscribe at any time.</i>
                <hr/>

                GONE CHECK CODE OK<select name="howheard" id="howheard">

            <tr><td><p class="desc">Certificates or other relevant documents about your translation qualifications. Please provide a short title for your qualification and upload the corresponding file. Project Officers will also upload here any certificates you obtain while volunteering with TWB. If you have any questions or can’t upload the certificate, please email <a href="mailto:translators@translatorswithoutborders.org?subject={rawurlencode('Translation Certification')}" target="_blank">translators@translatorswithoutborders.org</a></p></td></tr>
            <tr><td><a href="{urlFor name="user-uploads" options="user_id.$user_id|cert_id.TRANSLATOR"}" target="_blank">Upload file</a></td></tr>

            <tr><td> -->
            <!--    <hr/>
                <label for='receiveCredit'><strong>Do you want all the above (including NGOs you have contributed to) to be visible to all members of the TWB community?:</strong></label>
                <p class="desc">If at any point you wish to change this setting, you can always do that. Additionally you will be able to have a link to this information which you can share with selected people.</p>
                <input type="checkbox" value="1" name="receiveCredit" id="receiveCredit" {if $userPersonalInfo->getReceiveCredit()}checked="checked"{/if} /> Make the above information visible to TWB community
            </td></tr> -->

            <tr><td style="padding-bottom: 20px">
               <!-- <hr/> -->
                <div id="placeholder_for_errors_2"></div>
            </td></tr>

           <!-- <tr><td align="center">
                <div id="loading_warning1">
                    <p><i>{Localisation::getTranslation('common_loading')}</i></p>
                </div>
                <button type="submit" onclick="return validateForm();" class='btn btn-primary' id="updateBtn">
                    <i class="icon-refresh icon-white"></i> {Localisation::getTranslation('user_private_profile_update_profile_details')}
                </button>
                <button onclick="deleteUser(); return false;" class="btn btn-inverse" id="deleteBtn">
                    <i class="icon-fire icon-white"></i> {Localisation::getTranslation('user_private_profile_delete_user_account')}
                </button>
            </td></tr> -->
        </table>

        <input type="hidden" name="sesskey" value="{$sesskey}" />
    </form>
    <h2 class="twb_color">Please complete your profile <span class="tabcounter twb_color tabcounter1"></span></h2>
    <form method="post" id="userprofile" action="{urlFor name="user-private-profile" options="user_id.$user_id"}" enctype="multipart/form-data" accept-charset="utf-8">
            <ul id="myTab" class="nav nav-tabs">
              <li class="active"><a href="#home" data-toggle="tab" id="btnTrigger"><span class="clear_brand">1. Personal Information</span></a></li>
              <li class=""><a href="#profile" data-toggle="tab" id="btnTrigger"><span class="clear_brand">2. Language and professional Information</span></a></li>
              <li class=""><a href="#verifications" data-toggle="tab" id="btnTrigger"><span class="clear_brand">3. Verfications</span></a></li>
           
            </ul>
             <div id="myTabContent" class="tab-content">
              <div class="tab-pane fade active in" id="home">
              <br/>
            
                <div class="row-fluid">
                 <div class="span6">
                    <div>
                        <label for='displayName' class="clear_brand required">Username</label>
                        <input type="text" name="displayName" id="displayName" placeholder="displayName" placeholder="Type something…" value="{$user->getDisplayName()|escape:'html':'UTF-8'}">
                    </div>
                <div>
                         <label for='firstName' class="clear_brand required">First name</label>
                         <input type="text" name="firstName" value="{$userPersonalInfo->getFirstName()|escape:'html':'UTF-8'}" id="firstName" placeholder="First name" >
                </div>
                <div>
                         <label for='lastName' class="clear_brand required">Last name</label>
                         <input type="text" name="lastName" value="{$userPersonalInfo->getLastName()|escape:'html':'UTF-8'}" id="lastName" placeholder="Last name" >
                </div>
                <div>
                         <label class="clear_brand required">Email</label>
                         <input type="text" name="email" id="email" placeholder="Your email" value="{$user->getEmail()|escape:'html':'UTF-8'}">
                </div>
                <div>

                         <label for='city' class="clear_brand">City</label>
                         <input type="text" name="city" id="city" value="{$userPersonalInfo->getCity()|escape:'html':'UTF-8'}" placeholder="Your city">
                </div>
                <div>
               
                         <label class="clear_brand">Country</label>
                         <select name="country" id="country" class="country">
                           <option value="{$userPersonalInfo->getCountry()|escape:'html':'UTF-8'}" selected="selected">{$userPersonalInfo->getCountry()|escape:'html':'UTF-8'}</option>
                    {foreach $countries as $country}
                        {if $country->getCode() != '90' && $country->getCode() != '91' && $country->getCode() != '49' && $country->getCode() != '92' && $country->getCode() != '93' && $country->getCode() != '94'}
                        <option value="{$country->getName()|escape:'html':'UTF-8'}" data-id="{$country->getCode()}">  {$country->getName()|escape:'html':'UTF-8'}</option>
                        {/if}
                    {/foreach}
                         </select>
                </div>
                <div>
                          <label  class="checkbox clear_brand">
                          <input type="checkbox" name="receiveCredit"> Make my information including,which non-profits I contribute to visible to the TWB Community   
                          </label>
                </div>
                 
                </div>
                <div class="span6 clear_brand">
                    <div >
                    <label class="clear_brand">About me</label>
                        <textarea name="biography" id="biography" style="width:400px;" rows="8">{$user->getBiography()|escape:'html':'UTF-8'}</textarea>
                    </div>
                  <!--<div>
                         <label class="clear_brand">Linkedin URL</label>
                         <input style="width:400px;" type="text" name="linked" placeholder="">
                </div>
                <div>
                         <label class="clear_brand">Proz URL</label>
                         <input style="width:400px;" type="text" name="proz" placeholder="Type something…">
                </div>
                <div>
                         <label class="clear_brand">Other URL</label>
                         <input style="width:400px;" type="text" name="other" placeholder="Type something…">
                </div> -->
                 {foreach from=$url_list key=name item=url}
                 <div>
                <label for='{$name}'><strong>{$url['desc']}:</strong></label>
                <input type='text' value="{$url['state']|escape:'html':'UTF-8'}" style="width: 400px;" name="{$name}" id="{$name}" />
                </div>
                {/foreach}
                <div>
                          <label class="checkbox clear_brand">
                          <input name="newsletter_consent" id="newsletter_consent" value="{$communications_consent}" type="checkbox"> Subscribe to the TWB Email newsletter
                          <br/><small><i>You can unsubscribe at any time</i></small>   
                          </label>
                </div>
                </div>
                </div>
                
                 <a style="cursor:pointer;color:#FFFFFF;" href="#profile1" class="pull-right next111 pull-right btn btn-primary" id="btnTrigger1">Next</a>
              </div>
              <div class="tab-pane fade" id="profile">
                 <br/>
              
              <div class="row-fluid" >
              <div class="span6">
              <label class="clear_brand required"><strong>Native language</strong> <i class="icon-question-sign" id="tool5" data-toggle="tooltip" title="Please choose your native language and the country of your dialect/variant"></i></label>
              <select name="nativeLanguageSelect" class="nativeLanguageSelect">
                <option value=""></option>
                {foreach $languages as $language}
                                <option value="{$language->getCode()}" {if $language->getCode() == $nativeLanguageSelectCode}selected="selected"{/if}>{$language->getName()}</option>
                            {/foreach}
              </select>
              </div>
              <div class="span6">
              <label class="clear_brand required"><strong>Variant</strong> <i class="icon-question-sign" id="tool4" data-toggle="tooltip" title="--"></i></label>
                <select name="nativeCountrySelect" class="variant">
                    <option value=""></option>
                    {foreach $countries as $country}
                                {if $country->getCode() != '90' && $country->getCode() != '91' && $country->getCode() != '49' && $country->getCode() != '92' && $country->getCode() != '93' && $country->getCode() != '94'}
                                <option value="{$country->getCode()}" {if $country->getCode() == $nativeCountrySelectCode}selected="selected"{/if}>{$country->getName()}</option>
                                {/if}
                            {/foreach}
              </select>
              </div>
              </div>
              <div id="buildyourform">
               <div class="row-fluid" >
           
              <div class="span6">
              <label class="clear_brand required"><strong>I can translate from</strong> <i class="icon-question-sign" id="tool3" data-toggle="tooltip" title="Your language pairs should reflect the languages you are proficient or native in. We encourage linguists to translate into their native language(s)."></i></label>
              <select name="language_code_source_0" class="translate_from">
              <option>Select a language</option>
               {foreach from=$language_selection key=codes item=language}
                    <option value="{$codes}" >{$language}</option>
               {/foreach}
             
              </select>
              </div>
              <div class="span5">
              <label class="clear_brand required"><strong>To</strong> <i class="icon-question-sign" id="tool2" data-toggle="tooltip" title="--"></i></label>
                <select name="language_code_target_0" class="translate_to">
                <option>Select a language</option>
                 {foreach from=$language_selection key=codes item=language}
                    <option value="{$codes}" >{$language}</option>
                 {/foreach}
               
             
             
              </select> 
              </div>
              <div class="span1" style="margin-top: 1.6%;margin-left: -18%;">
              <label></label>
              <input type="button" value="+" class="add" id="add"  /></div> <span id="btnclick" class="countclick"><span>
           
              </div>
              </div>
                    <div class="row-fluid">
              <div class="span6 clear_brand">
                  {if $isSiteAdmin}
                    {if !(isset($strict))}
                        {assign var="strict" value=false}
                    {/if}
                {else}
                    {if !(isset($strict))}
                        {assign var="strict" value=true}
                    {/if}
                    {if !(isset($intervalId))}
                        {assign var="intervalId" value={NotificationIntervalEnum::DAILY}}
                    {/if}
                {/if}
              How often do you want to receive tasks availability email <i class="icon-question-sign" id="tool1" data-toggle="tooltip" title="Let us know how often you want to receive email notifications about tasks available in your language pairs."></i><br/>
            <select name="interval">
                        <option value="0"
                            {if !isset($intervalId)}
                                selected="true"
                            {/if}
                        >
                           {Localisation::getTranslation('user_task_stream_notification_edit_never')}
                        </option>
                        <option value="{NotificationIntervalEnum::DAILY}"
                            {if isset($intervalId) && $intervalId == NotificationIntervalEnum::DAILY}
                                selected="true"
                            {/if}
                        >
                            {Localisation::getTranslation('user_task_stream_notification_edit_daily')}
                        </option>
                        <option value="{NotificationIntervalEnum::WEEKLY}"
                            {if isset($intervalId) && $intervalId == NotificationIntervalEnum::WEEKLY}
                                selected="true"
                            {/if}
                        >
                            {Localisation::getTranslation('user_task_stream_notification_edit_weekly')}
                        </option>
                        <option value="{NotificationIntervalEnum::MONTHLY}"
                            {if isset($intervalId) && $intervalId == NotificationIntervalEnum::MONTHLY}
                                selected="true"
                            {/if}
                        >
                            {Localisation::getTranslation('user_task_stream_notification_edit_monthly')}
                        </option>

                        {if $isSiteAdmin}
                        <option value="10">
                            Set this volunteer as in-kind sponsor
                        </option>
                        {/if}
                    </select>
                    {if $in_kind}&nbsp;In-kind Sponsor{/if}
              </div>
              </div>
              <div class="row-fluid">
              <div class="span4 clear_brand">
              <label class="clear_brand required"><strong>Services I can provide</strong></label>
                   {assign var="i" value=0}
                {foreach from=$capability_list key=name item=capability}
                    <input type="checkbox" {if $capability['state']}checked="checked"{/if} name="{$name}" id="capability{$i}" /> {$capability['desc']|escape:'html':'UTF-8'}  <br/>
                    {assign var="i" value=$i+1}
                {/foreach}
         
              </div>
              <div class="span8 clear_brand">
              <div class="span6">
              <label class="clear_brand required"><strong>Fields of expertise</strong></label>
                {assign var="i" value=0}
                {foreach from=$expertise_list key=name item=expertise}
                    <input type="checkbox" {if $expertise['state']}checked="checked"{/if} name="{$name}" id="expertise{$i}" /> {$expertise['desc']|escape:'html':'UTF-8'}<br/>
                    {assign var="i" value=$i+1}
                {/foreach}
             
                       <!--  <input  type="checkbox" name="expertise[]" value="Accounting"> Accounting & Finance
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Legal"> Legal documents / Contracts / Law 
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Technical"> Technical / Engineering
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="IT"> Information Technology (IT)
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Medical"> Medical / Pharmaceutical
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Science"> Science / Scientific
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Health"> Health
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Nutrition"> Food security & Nutrition
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Telecommunications"> Telecommunications
                         <br/>
                         <input  type="checkbox" class="clear_brand" name="expertise[]" value="Education"> Education -->
              
              </div>
            <!--  <div class="span4">
               <br/>
              <input  type="checkbox" class="clear_brand" name="expertise[]" value="Migration"> Migration & Displacement
                         <br/>
                         <input  type="checkbox" class="clear_brand" name="expertise[]" value="CCCM"> Camp cordination & management
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Shelter"> Shelter
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="WASH"> Water,sanitation and Hygiene promotion
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Logistics"> Logistics
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Equality"> Equality & Inclusion
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Gender"> Gender equality
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Peace"> Peace & Justice
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Environment"> Environment & Climate Action
                         <br/>
                         <input  type="checkbox" name="expertise[]" value="Protection"> Protection & Early recovery
              </div> -->
              </div>
              </div>
              <br/>
        <a style="cursor:pointer;color:#FFFFFF;" href="#verifications" class="pull-right next111 btn btn-primary" id="btnTrigger1">Next</a> <a style="cursor:pointer;color:#FFFFFF;" href="#home" class="pull-right next111 btn btn-primary" id="btnTrigger11">Prev</a>
              
              </div>
              <div class="tab-pane fade" id="verifications">
              <br/>
              <p class="desc">If you hold a certification or membership from any of the organizations below, you could qualify to be a verified translator. Please select the organization and click to submit a proof of certification/membership. You will be upgraded to Verified Translator, which will give you immediate access to all projects available, for the verified combination. if you have any questions or can't upload the certificate, please email <a href="mailto:translators@translatorswithoutborders.org?subject=Translation%20Certification" target="_blank">translators@translatorswithoutborders.org</a></p>
              
              <ul>
               {foreach from=$certification_list key=name item=certification}
                <li>{if $certification['state']}Already submitted{if $certification['reviewed'] == 1} and reviewed{/if}: {/if}<a href="{urlFor name="user-uploads" options="user_id.$user_id|cert_id.$name"}" target="_blank">{$certification['desc']|escape:'html':'UTF-8'}</a></li>
            {/foreach}
  
              </ul>
              <br/>
               <h4 style="font-weight: bold"><br />Other Certificates and Documentation</h4>
              <p class="desc">Certificates or other relevant documents about your translation qualifications. Please provide a short title for your qualification and upload the corresponding file. Project Officers will also upload here any certificates you obtain while volunteering with TWB. If you have any questions or can’t upload the certificate, please email <a href="mailto:translators@translatorswithoutborders.org?subject=Translation%20Certification" target="_blank">translators@translatorswithoutborders.org</a></p>
              <a href="/{$user_id}/user-uploads/TRANSLATOR/" target="_blank">Upload file</a>
              <br/>
                <button type="submit" onclick="return validateForm();" class='pull-right btn btn-primary' id="updateBtn">
                    <i class="icon-refresh icon-white"></i> Complete
                </button> <a style="cursor:pointer;color:#FFFFFF;" href="#profile1" class="pull-right next111 btn btn-primary" id="btnTrigger11">Prev</a>
              </div>
            </div>
            <input type="hidden" name="sesskey" value="{$sesskey}" />
    </form>
</div>

{include file='footer.tpl'}
