{include file="header.tpl" body_id="userprofile"}
<style>
.twb_color{
  color:#e8991c;
}
.clear_brand{
  color:#143878;
}
 .required:after {
    content:" *";
    color: #F00;
  }
 

	 #userprofile label {
  color:#143878 !important;
}
 #userprofile .check {
  color:#333 !important;
}
#userprofile label.error {
		
		width: auto;
	
	}
#userprofile .error{
    color:#F00 !important;
}

	
</style>

<div class="hero-unit">
<br/>
<h2 class="twb_color">Please complete your profile <span class="tabcounter twb_color tabcounter1"></span></h2>
{include file="handle-flash-messages.tpl"}
{if isset($error)}
    <div class="alert alert-error">
        <strong>{Localisation::getTranslation('common_error')}:</strong> {$error}
    </div>
{/if}

<div class="bs-docs-example">
<form method="post" id="userprofile" action="{urlFor name="userprofile"}" class="well" accept-charset="utf-8">
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
                        <label class="clear_brand required">Username</label>
                        <input type="text" name="username" id="username" placeholder="Username" placeholder="Type something…" value="{$user->getDisplayName()|escape:'html':'UTF-8'}">
                    </div>
                <div>
                         <label class="clear_brand required">First name</label>
                         <input type="text" name="first_name" value="{$userPersonalInfo->getFirstName()|escape:'html':'UTF-8'}" id="first_name" placeholder="First name" >
                </div>
                <div>
                         <label class="clear_brand required">Last name</label>
                         <input type="text" name="last_name" value="{$userPersonalInfo->getLastName()|escape:'html':'UTF-8'}" id="last_name" placeholder="Last name" >
                </div>
                <div>
                         <label class="clear_brand required">Email</label>
                         <input type="text" name="email" id="email" placeholder="Your email" value="{$user->getEmail()|escape:'html':'UTF-8'}">
                </div>
                <div>
                         <label class="clear_brand">City</label>
                         <input type="text" name="city" id="city" placeholder="Your city">
                </div>
                <div>
                         <label class="clear_brand">Country</label>
                         <select name="country" class="country">
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
                        <textarea name="about" id="about" style="width:400px;" rows="8"></textarea>
                    </div>
                  <div>
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
                </div>
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
                {foreach $languages as $language}
                                <option value="{$language->getCode()}" {if $language->getCode() == $nativeLanguageSelectCode}selected="selected"{/if}>{$language->getName()}</option>
                            {/foreach}
              </select>
              </div>
              <div class="span6">
              <label class="clear_brand required"><strong>Variant</strong> <i class="icon-question-sign" id="tool4" data-toggle="tooltip" title="--"></i></label>
                <select name="nativeCountrySelect" class="variant">
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
              <select name="translate_from">
              <option>Select a language</option>
                 {foreach $languages_source as $language_source}
                                <option value="{$language_source->getCode()}" >{$language_source->getName()}</option>
                            {/foreach}
              </select>
              </div>
              <div class="span5">
              <label class="clear_brand required"><strong>To</strong> <i class="icon-question-sign" id="tool2" data-toggle="tooltip" title="--"></i></label>
                <select name="translate_to">
                <option>Select a language</option>
               
                 {foreach $languages_target as $language_target}
                                <option value="{$language_target->getCode()}" >{$language_target->getName()}</option>
                            {/foreach}
             
              </select> 
              </div>
              <div class="span1" style="margin-top: 1.6%;margin-left: -18%;">
              <label></label>
              <input type="button" value="+" class="add" id="add"  /></div>
           
              </div>
              </div>
                    <div class="row-fluid">
              <div class="span6 clear_brand">
              How often do you want to receive tasks availability email <i class="icon-question-sign" id="tool1" data-toggle="tooltip" title="Let us know how often you want to receive email notifications about tasks available in your language pairs."></i>
              <select name="interval">
              <option value>--select--</option>
               <option value="0">Never</option>
              <option value="1">Daily</option>
              <option value="2">Weekly</option>
              <option value="3">Monthly</option>
              
              </select>
              </div>
              </div>
              <div class="row-fluid">
              <div class="span4 clear_brand">
              <label class="clear_brand required"><strong>Services I can provide</strong></label>
                         <input  type="checkbox" name="services[]"  value="6"> Translation
                         <br/>
                         <input  type="checkbox" name="services[]" value="7"> Revision
                         <br/>
                         <input  type="checkbox" name="services[]" value="10"> Subtitling
                         <br/>
                         <input  type="checkbox" name="services[]" value="11"> Monolingual editing
                         <br/>
                         <input  type="checkbox" name="services[]" value="12"> DTP
                         <br/>
                         <input  type="checkbox" name="services[]" value="13"> Voiceover
                         <br/>
                         <input  type="checkbox" name="services[]" value="8"> Intepretation
              </div>
              <div class="span8 clear_brand">
              <div class="span4">
              <label class="clear_brand required"><strong>Fields of expertise</strong></label>
             
                         <input  type="checkbox" name="expertise[]" value="Accounting"> Accounting & Finance
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
                         <input  type="checkbox" class="clear_brand" name="expertise[]" value="Education"> Education
              
              </div>
              <div class="span4">
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
              </div>
              </div>
              </div>
              <br/>

        <a style="cursor:pointer;color:#FFFFFF;" href="#verifications" class="pull-right next111 btn btn-primary" id="btnTrigger1">Next</a> <a style="cursor:pointer;color:#FFFFFF;" href="#home" class="pull-right next111 btn btn-primary" id="btnTrigger11">Prev</a>
              
              </div>
              <div class="tab-pane fade" id="verifications">
              <br/>
              <p class="desc">If you hold a certification or membership from any of the organizations below, you could qualify to be a verified translator. Please select the organization and click to submit a proof of certification/membership. You will be upgraded to Verified Translator, which will give you immediate access to all projects available, for the verified combination. if you have any questions or can't upload the certificate, please email <a href="mailto:translators@translatorswithoutborders.org?subject=Translation%20Certification" target="_blank">translators@translatorswithoutborders.org</a></p>
              <ul>
               
                            <li><a href="/{$user_id}/user-uploads/ATA/" target="_blank">American Translators Association (ATA) - ATA Certified</a></li>
                            <li><a href="/{$user_id}/user-uploads/APTS/" target="_blank">Arab Professional Translators Society (APTS) - Certified Translator, Certified Translator/Interpreter or Certified Associate</a></li> 
                            <li><a href="/{$user_id}/user-uploads/ATIO/" target="_blank">Association of Translators and Interpreters of Ontario (ATIO) - Certified Translators or Candidates</a></li>
                            <li><a href="/{$user_id}/user-uploads/ATIM/" target="_blank">Association of Translators, Terminologists and Interpreters of Manitoba - Certified Translators</a></li>
                            <li><a href="/{$user_id}/user-uploads/ABRATES/" target="_blank">Brazilian Association of Translators and Interpreters (ABRATES) - Accredited Translators (Credenciado)</a></li>
                            <li><a href="/{$user_id}/user-uploads/CIOL/" target="_blank">Chartered Institute of Linguists (CIOL) - Member, Fellow, Chartered Linguist, or DipTrans IOL Certificate holder</a></li>
                            <li><a href="/{$user_id}/user-uploads/ITIA/" target="_blank">Irish Translators’ and Interpreters’ Association (ITIA) - Professional Member</a></li>
                            <li><a href="/{$user_id}/user-uploads/ITI/" target="_blank">Institute of Translation and Interpreting (ITI) - ITI Assessed</a></li>
                            <li><a href="/{$user_id}/user-uploads/NAATI/" target="_blank">National Accreditation Authority for Translators and Interpreters (NAATI) - Certified Translator or Advanced Certified Translator</a></li>
                            <li><a href="/{$user_id}/user-uploads/NZSTI/" target="_blank">New Zealand Society of Translators and Interpreters (NZSTI) - Full Members</a></li>
                            <li><a href="/{$user_id}/user-uploads/ProZ/" target="_blank">ProZ Certified PRO members</a></li>
                            <li><a href="/{$user_id}/user-uploads/Austria/" target="_blank">UNIVERSITAS Austria Interpreters’ and Translators’ Association - Certified Members</a></li>
                            <li><a href="/{$user_id}/user-uploads/ETLA/" target="_blank">Egyptian Translators and Linguists Association (ETLA) - Members</a></li>
                            <li><a href="/{$user_id}/user-uploads/SATI/" target="_blank">South African Translators’ Institute (SATI) - Accredited Translators or Sworn Translators</a></li>
                            <li><a href="/{$user_id}/user-uploads/CATTI/" target="_blank">China Accreditation Test for Translators and Interpreters (CATTI) - Senior Translators or Level 1 Translators</a></li>
                            <li><a href="/{$user_id}/user-uploads/STIBC/" target="_blank">Society of Translators and Interpreters of British Columbia (STIBC) - Certified Translators or Associate Members</a></li>
              </ul>
              <br/>
               <h4 style="font-weight: bold"><br />Other Certificates and Documentation</h4>
              <p class="desc">Certificates or other relevant documents about your translation qualifications. Please provide a short title for your qualification and upload the corresponding file. Project Officers will also upload here any certificates you obtain while volunteering with TWB. If you have any questions or can’t upload the certificate, please email <a href="mailto:translators@translatorswithoutborders.org?subject=Translation%20Certification" target="_blank">translators@translatorswithoutborders.org</a></p>
              <a href="/{$user_id}/user-uploads/TRANSLATOR/" target="_blank">Upload file</a>
              <br/>
              <button type="submit" class="pull-right btn btn-primary"><a style="cursor:pointer;color:#FFFFFF;" class="pull-right complete" id="complete">Complete</a></button> <a style="cursor:pointer;color:#FFFFFF;" href="#profile1" class="pull-right next111 btn btn-primary" id="btnTrigger11">Prev</a>
              </div>
            </div>
            </form>
          </div>

{include file="footer.tpl"}