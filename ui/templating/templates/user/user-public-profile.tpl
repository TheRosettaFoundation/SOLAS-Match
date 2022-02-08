{include file='header.tpl'}

{if isset($this_user)}
    <div class="page-header">
        <h1>
        <table>
            <tr>
                <td>                    
                    <img src="https://www.gravatar.com/avatar/{md5( strtolower( trim($this_user->getEmail())))}?s=80{urlencode("&")}r=g" alt="" />
                    {assign var="user_id" value=$this_user->getId()}
                    {if $this_user->getDisplayName() != ''}
                        {TemplateHelper::uiCleanseHTML($this_user->getDisplayName())}
                    {else}
                        {Localisation::getTranslation('common_user_profile')}
                    {/if}
                    {if !isset($no_header)}<small>{Localisation::getTranslation('user_public_profile_0')}</small>{/if}
                    
                </td>
                <td>                    
                    <div class="pull-right">
                        {if $isSiteAdmin}
                            <a href="{urlFor name="claimed-tasks" options="user_id.{$this_user->getId()}"}" class="btn btn-primary">
                                <i class="icon-list icon-white"></i> {Localisation::getTranslation('claimed_tasks_claimed_tasks')}
                            </a>
                        {/if}
                        {if $private_access && isset($org_creation)}
                            {if $org_creation == 'y'}
                                <a href="{urlFor name="create-org"}" class="btn btn-success"
                                   onclick="return confirm('{Localisation::getTranslation('user_public_profile_1')}')">
                                    <i class="icon-star icon-white"></i> {Localisation::getTranslation('common_create_organisation')}
                                </a>
                            {else if $org_creation == 'h'}
                            {/if}
                        {/if} 
                        {if ($private_access && !$is_admin_or_org_member) || $isSiteAdmin}
                            <a href='{urlFor name="user-private-profile" options="user_id.$user_id"}' class='btn btn-primary'>
                                <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('user_public_profile_edit_profile_details')}
                            </a>
                        {/if}
                        {if $isSiteAdmin && $howheard['reviewed'] == 0}
                            <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                <input type="submit" class="btn btn-primary" name="mark_reviewed" value="Mark New User as Reviewed" />
                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                            </form>
                        {/if}
                        {if $show_create_memsource_user}
                            <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                <input type="submit" class="btn btn-primary" name="mark_create_memsource_user" value="Create Matching Memsource User" />
                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                            </form>
                        {/if}
                    </div>
                </td>
            </tr>
        </table>
        </h1>
    </div>
            
{else}
    <div class='page-header'><h1>{Localisation::getTranslation('common_user_profile')} <small>{Localisation::getTranslation('user_public_profile_2')}</small></h1></div>
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


{if isset($this_user) && ($private_access || $isSiteAdmin || $receive_credit)}

<span class="hidden">
<div id="dialog_for_verification" title="Perform a translation test?">
<p>Becoming verified will give you access to more tasks in your language pair. For more information please visit <a href="https://community.translatorswb.org/t/how-to-become-a-kato-verified-translator/262">this page</a>.</p>
<p>By clicking “OK” below, a test will be created for you, and you will receive an email with instructions on how to complete the test.</p>
<p>When you have completed the test, one of our Senior Translators will review it. When we have the results we will contact you by email. Please note, this can take 3-4 weeks.</p>
<p>If you do not want to take the test, please click “Cancel”.</p>
</div>
</span>


<table border="0">
    <tr valign="top">
        <td style="width: 48%">
            <div>
                <table border="0" width="40%" style="overflow-wrap: break-word; word-break:break-all;">
                    <tbody>
                        {if isset($userPersonalInfo)}
                            <tr>
                                 <td>
                                     <h3>{if !empty($userPersonalInfo->getFirstName())}{TemplateHelper::uiCleanseHTML($userPersonalInfo->getFirstName())}{/if} {if !empty($userPersonalInfo->getLastName())}{TemplateHelper::uiCleanseHTML($userPersonalInfo->getLastName())}{/if}</h3>
                                 </td>
                             </tr>
                        {/if}
                        {if $private_access || $isSiteAdmin}
                            <tr>
                                <td>
                                    {mailto address={$this_user->getEmail()} encode='hex' text={$this_user->getEmail()}}
                                    {if $isSiteAdmin}
                                        <a href='{urlFor name="change-email" options="user_id.$user_id"}' class='pull-right btn btn-primary'>
                                            <i class="icon-list icon-white"></i> {Localisation::getTranslation('common_change_email')}
                                        </a>
                                    {/if}
                                </td>
                            </tr>
                        {/if}
                        {if $isSiteAdmin}
                            <tr>
                                <td>
                                    Joined: {substr($this_user->getCreatedTime(), 0, 10)}
                                </td>
                            </tr>
                        {/if}
                        {if isset($userPersonalInfo)}
                        {if !empty($userPersonalInfo->getMobileNumber())}
                            <tr>
                                <td>
                                    {TemplateHelper::uiCleanseHTML($userPersonalInfo->getMobileNumber())}
                                </td>
                            </tr>
                        {/if}
                        {if !empty($userPersonalInfo->getCity())}
                                <td>
                                    {TemplateHelper::uiCleanseHTML($userPersonalInfo->getCity())}
                                </td>
                            </tr>
                        {/if}
                        {if !empty($userPersonalInfo->getCountry())}
                            <tr>
                                <td>
                                    {TemplateHelper::uiCleanseHTML($userPersonalInfo->getCountry())}
                                </td>
                            </tr>
                        {/if}
                        {/if}

                        {foreach from=$url_list item=url}
                            {if $url['state']}<tr><td><a href="{$url['state']}" target="_blank">{$url['state']|escape:'html':'UTF-8'}</a></td></tr>{/if}
                        {/foreach}

                        {assign var=bio value={TemplateHelper::uiCleanseHTMLNewlineAndTabs($this_user->getBiography())}}
                        {if !empty($bio)}
                        <tr>
                            <td>
                                <h3>About Me</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                {$bio}
                            </td>
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                        </tr>
                        {/if}

                        {assign var="native_language_code" value=""}
                        {if $this_user->getNativeLocale() != null}
                        {assign var="native_language_code" value=$this_user->getNativeLocale()->getLanguageCode()}
                        <tr>
                            <td>
                                Native in <strong>{TemplateHelper::getLanguageAndCountry($this_user->getNativeLocale())}</strong>
                            </td>
                        </tr>
                        {/if}

                        {if !empty($userQualifiedPairs)}
                            <tr>
                                <td style="padding-bottom: 10px"/>
                            </tr>
                            <tr>
                                <td>
                                    <h3>{Localisation::getTranslation('common_secondary_languages')}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
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
                                        <p>
                                            {if $userQualifiedPair['country_source'] == 'ANY'}{$userQualifiedPair['language_source']}{else}{$userQualifiedPair['language_source']} - {$userQualifiedPair['country_source']}{/if} &nbsp;&nbsp;&nbsp;{Localisation::getTranslation('common_to')}&nbsp;&nbsp;&nbsp; {if $userQualifiedPair['country_target'] == 'ANY'}{$userQualifiedPair['language_target']}{else}{$userQualifiedPair['language_target']} - {$userQualifiedPair['country_target']}{/if}&nbsp;&nbsp;&nbsp;&nbsp;
                                            <strong>
                                            {if $userQualifiedPair['qualification_level'] == 1}({Localisation::getTranslation('user_qualification_level_1')}){/if}
                                            {if $userQualifiedPair['qualification_level'] == 2}({Localisation::getTranslation('user_qualification_level_2')}){/if}
                                            {if $userQualifiedPair['qualification_level'] == 3}({Localisation::getTranslation('user_qualification_level_3')}){/if}
                                            </strong>

                                            {assign var="pair" value="`$userQualifiedPair['language_code_source']`-`$userQualifiedPair['language_code_target']`"}
                                            {if false && $userQualifiedPair['qualification_level'] == 1 && in_array($pair, ['en-ar', 'en-fr', 'en-es', 'fr-en', 'es-en', 'en-pt', 'en-it']) && $native_language_code === $userQualifiedPair['language_code_target'] && ($private_access || $isSiteAdmin) && $button_count.$pair == 0}
                                                {$button_count.$pair=1}
                                            <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                                <input type="hidden" name="source_language_country" value="{$userQualifiedPair['language_code_source']}-{$userQualifiedPair['country_code_source']}" />
                                                <input type="hidden" name="target_language_country" value="{$userQualifiedPair['language_code_target']}-{$userQualifiedPair['country_code_target']}" />
                                                {if empty($testing_center_projects_by_code[$pair]) || $isSiteAdmin}
                                                    <input type="submit" class="add_click_handler btn btn-primary" name="btnSubmit" value="Get Verified" />
                                                {else}
                                                    <input type="submit" class="btn btn-primary" name="btnSubmit" value="Get Verified" onclick="
alert('You have already requested to take a test in order to become a Kató Verified Translator. If you would like to take a second test, please contact translators@translatorswithoutborders.org');
                                                    return false;" />
                                                {/if}
                                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                            </form>
                                            {/if}
                                        </p>
                                    {/foreach}
                                </td>
                            </tr>
                        {/if}

                            <tr>
                                <td>
                                    <h3>Services</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <ul>
                                {foreach from=$capability_list item=capability}
                                    {if $capability['state']}<li>{$capability['desc']|escape:'html':'UTF-8'}</li>{/if}
                                {/foreach}
                                </ul>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <h3>Experienced in</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <ul>
                                {foreach from=$expertise_list item=expertise}
                                    {if $expertise['state']}<li>{$expertise['desc']|escape:'html':'UTF-8'}</li>{/if}
                                {/foreach}
                                </ul>
                                </td>
                            </tr>

                            {if $private_access || $isSiteAdmin}
                            <tr>
                                <td>
                                    <h3>Share this link with anyone you wish to see your profile:</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="{urlFor name="shared_with_key" options="key.{$key}"}" target="_blank"><span style="font-size: xx-small;">{substr(Settings::get('site.location'), 0, -1)}{urlFor name="shared_with_key" options="key.{$key}"}</span></a>
                                </td>
                            </tr>
                            {/if}
                    </tbody>
                </table>
            </div>
        </td>
        
        <td style="width: 4%"/>
        <td style="width: 48%">
            <div>
                <table border="0" width="40%" style="overflow-wrap: break-word; word-break:break-all;">
                    <tbody align="left" width="48%">
                        {if !empty($certificate)}
                        <tr>
                            <td>
                                <img src="{$certificate}" width="50%" />
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px"/>
                        </tr>
                        {/if}

                        <tr>
                            <td>
                                <h3>Supported NGOs</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <ul>
                            {foreach from=$supported_ngos item=supported_ngo}
                                <li>{$supported_ngo['org_name']|escape:'html':'UTF-8'}</li>
                            {/foreach}
                            </ul>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <h3>Certificates and training courses</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <ul>
                        {foreach from=$certifications item=certification}
                        <li>
                        {if $private_access || $isSiteAdmin}
                            {if $isSiteAdmin && $certification['reviewed'] == 0 && $certification['certification_key'] != 'TRANSLATOR' && $certification['certification_key'] != 'TWB'}
                            <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                <input type="submit" class="btn btn-primary" name="mark_certification_reviewed" value="Mark Reviewed" />
                                <input type="hidden" name="certification_id" value="{$certification['id']}" />
                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                <a href="{urlFor name="user-download" options="id.{$certification['id']}"}">{$certification['note']|escape:'html':'UTF-8'}</a>
                            </form>
                           {else}
                           <a href="{urlFor name="user-download" options="id.{$certification['id']}"}">{$certification['note']|escape:'html':'UTF-8'}</a>{if $private_access && $certification['reviewed'] == 1} (reviewed){/if}
                           {/if}
                            {if $isSiteAdmin}
                                <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
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
                            </td>
                        </tr>

                        {if $isSiteAdmin}
                        <tr><td><a href="{urlFor name="user-uploads" options="user_id.$user_id|cert_id.TWB"}" target="_blank">Upload a new file for this user</a></td></tr>
                        {/if}

                        {if $private_access || $isSiteAdmin}
                        <tr>
                            <td style="padding-bottom: 10px"/>
                        </tr>
                        <tr>
                            <td>
                               <table>
                                   <tr><td><h3>Average scores in reviews<br />This information is only visible to you</h3></td><td><h3>Average score out of 5</h3></td></tr>
                                   <tr><td>Accuracy</td><td>{$quality_score['accuracy']}</td></tr>
                                   <tr><td>Fluency</td><td>{$quality_score['fluency']}</td></tr>
                                   <tr><td>Terminology</td><td>{$quality_score['terminology']}</td></tr>
                                   <tr><td>Style</td><td>{$quality_score['style']}</td></tr>
                                   <tr><td>Design</td><td>{$quality_score['design']}</td></tr>
                               </table>
                            </td>
                        </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
</table>

<div class="page-header">
    <h1>Community Recognition Program<small>Contribute to our mission and obtain rewards</small></h1>
</div>
<p style="margin-bottom:50px;" />
<hr />
<p>Translators without Borders believe it is important to acknowledge the value and impact of the support that our community members provide. As part of our Community Recognition Program, our volunteers can receive rewards depending on their level of contribution. Please remember that when you join our TWB Community, you agree to abide by TWB’s <a href="https://translatorswithoutborders.org/wp-content/uploads/2020/07/Plain-language-Code-of-Conduct-for-Translators-July-2020.pdf" target="_blank">Code of conduct</a> and commit to deliver the highest standard of professional translations, and only undertake work you are competent to perform in the languages and subject areas for which you are qualified.</p>
<p>Deliver tasks on the TWB platform to build up points. Once you reach the thresholds described in the rewards chart, you can request the respective rewards.</p>

<table border="0">
    <tr valign="top">
        <td style="width: 48%">
            <div>
                <h2>
                <span style="color: #9e6100;">Rewards chart</span>
                <a href="mailto:xxx@twb.org?subject=Request reward" target="_blank" class="pull-right btn btn-primary" style="position:relative; right:-30px; top:12px;">
                    <i class="icon-list icon-white"></i> Request reward
                </a>
                </h2>
                <table width="40%" style="border: 2px solid #e8991c; border-collapse: collapse; overflow-wrap: break-word; word-break: break-all;">
                    <tbody>
                        <tr><td style="border:2px solid #e8991c; color: #576e82;">Points</td><td style="border:2px solid #e8991c; color: #576e82;">Reward</td></tr>
                        <tr><td style="border:2px solid #e8991c"><strong>5,000</strong></td> <td style="border:2px solid #e8991c">Certification of volunteer activity</td></tr>
                        <tr><td style="border:2px solid #e8991c"><strong>15,000</strong></td><td style="border:2px solid #e8991c">Reference letter</td></tr>
                        <tr><td style="border:2px solid #e8991c"><strong>30,000</strong></td><td style="border:2px solid #e8991c">Recommendation on professional platforms</td></tr>
                        <tr><td style="border:2px solid #e8991c"><strong>50,000</strong></td><td style="border:2px solid #e8991c">Shout-out post on TWB social media account</td></tr>
                    </tbody>
                </table>

                <h2 style="color: #9e6100;">How do I earn points?</h2>
                The points are calculated as follows:
                <table width="40%" style="border: 2px solid #e8991c; border-collapse: collapse; overflow-wrap: break-word; word-break: break-all;">
                    <tbody>
                        <tr><td style="border:2px solid #e8991c; color: #576e82;">Type of task</td><td style="border:2px solid #e8991c; color: #576e82;">Unit</td><td style="border:2px solid #e8991c; color: #576e82;">Points accrued per unit</td></tr>
                        <tr><td style="border:2px solid #e8991c">Translation</td>                  <td style="border:2px solid #e8991c">1 word</td>               <td style="border:2px solid #e8991c">1</td></tr>
                        <tr><td style="border:2px solid #e8991c">Revision</td>                     <td style="border:2px solid #e8991c">1 word</td>               <td style="border:2px solid #e8991c">0.5</td></tr>
                        <tr><td style="border:2px solid #e8991c">Transcription</td>                <td style="border:2px solid #e8991c">1 word</td>               <td style="border:2px solid #e8991c">0.5</td></tr>
                        <tr><td style="border:2px solid #e8991c">Voice recording</td>              <td style="border:2px solid #e8991c">1 word</td>               <td style="border:2px solid #e8991c">1</td></tr>
                        <tr><td style="border:2px solid #e8991c">Translation if subtitles</td>     <td style="border:2px solid #e8991c">1 word</td>               <td style="border:2px solid #e8991c">1</td></tr>
                        <tr><td style="border:2px solid #e8991c">Revision of subtitles</td>        <td style="border:2px solid #e8991c">1 word</td>               <td style="border:2px solid #e8991c">0.5</td></tr>
                        <tr><td style="border:2px solid #e8991c">Terminology</td>                  <td style="border:2px solid #e8991c">1 term</td>               <td style="border:2px solid #e8991c">10</td></tr>
                    </tbody>
                </table>
            </div>
        </td>

        <td style="width: 4%" />
        <td style="width: 48%">
            <div>
                <table border="0" width="40%" style="overflow-wrap: break-word; word-break:break-all;">
                    <tbody align="left" width="48%">
                        {if !empty($recognition)}
                        <tr>
                            <td>
                                <img src="{$recognition}" width="50%" />
                            </td>
                        </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
</table>

{if !empty($user_has_strategic_languages)}
<p>Your work in strategic languages for TWB allows you to access further special rewards. These do not constitute payments, but support towards expenses incurred while volunteering for TWB, such as connectivity costs.</p>
<p>This is our current list of strategic languages:<br />
Hausa, Kanuri, Fulfulde, Shuwa Arabic, Bura-Pabir, Marghi, Waha, Kibaku, Mandara, Burmese, Rohingyalish, Bengali, Chittagonian, Amharic, Haitian Creole, Dari, Kurdish, Lingala, Luganda/Ganda, Mongo, Nande, Ngombe, Oromo, Pashto, Shi, Somali, Swahili, Tigrinya</p>

<table border="0">
    <tr valign="top">
        <td style="width: 80%">
            <div>
                <h2 style="color: #9e6100;">Rewards chart for work in strategic languages</h2>
                <table width="40%" style="border: 2px solid #e8991c; border-collapse: collapse; overflow-wrap: break-word; word-break: break-all;">
                    <tbody>
                        <tr><td style="border:2px solid #e8991c; color: #576e82;">Points in strategic languages</td><td style="border:2px solid #e8991c; color: #576e82;">Status</td><td style="border:2px solid #e8991c; color: #576e82;">Recognition reward</td></tr>
                        <tr><td style="border:2px solid #e8991c">5,000</td>                                         <td style="border:2px solid #e8991c">TWB New Community Member</td><td style="border:2px solid #e8991c">100 USD phone top-up or online voucher, where applicable</td></tr>
                        <tr><td style="border:2px solid #e8991c">25,000</td>                                        <td style="border:2px solid #e8991c">TWB Traveler</td>            <td style="border:2px solid #e8991c">100 USD bank transfer</td></tr>
                        <tr><td style="border:2px solid #e8991c">50,000</td>                                        <td style="border:2px solid #e8991c">TWB Pathfinder</td>          <td style="border:2px solid #e8991c">150 USD bank transfer</td></tr>
                        <tr><td style="border:2px solid #e8991c">100,000</td>                                       <td style="border:2px solid #e8991c">TWB Explorer</td>            <td style="border:2px solid #e8991c">400 USD bank transfer</td></tr>
                        <tr><td style="border:2px solid #e8991c">200,000</td>                                       <td style="border:2px solid #e8991c">TWB Navigator</td>           <td style="border:2px solid #e8991c">750 USD bank transfer</td></tr>
                        <tr><td style="border:2px solid #e8991c">300,000</td>                                       <td style="border:2px solid #e8991c">TWB Voyager</td>             <td style="border:2px solid #e8991c">750 USD bank transfer</td></tr>
                        <tr><td style="border:2px solid #e8991c">400,000</td>                                       <td style="border:2px solid #e8991c">TWB Trailblazer</td>         <td style="border:2px solid #e8991c">750 USD bank transfer</td></tr>
                        <tr><td style="border:2px solid #e8991c">500,000</td>                                       <td style="border:2px solid #e8991c">TWB Pioneer</td>             <td style="border:2px solid #e8991c">750 USD bank transfer</td></tr>
                    </tbody>
                </table>
            </div>
        </td>

        <td style="width: 20%">
            <a href="mailto:xxx@twb.org?subject=Request reward for work in strategic languages" target="_blank" class="btn btn-primary">
                <i class="icon-list icon-white"></i> Request reward
            </a>
        </td>
    </tr>
</table>
{/if}

{if $isSiteAdmin}
<hr/>
<table border="0">
    <tr valign="top">
        <td style="width: 30%"><h3>Administrative Section{if !empty($tracked_registration)} (Tracked Registration: {$tracked_registration}){/if}</h3></td>
        <td style="width: 22%"></td>
        <td style="width: 18%"></td>
        <td style="width: 18%"></td>
        <td style="width: 12%"></td>
    </tr>
    <tr valign="top">
        <td style="width: 30%"><strong>Comment</strong></td>
        <td style="width: 22%"><strong>Willingness to work again score (1 to 5)</strong></td>
        <td style="width: 18%"><strong>Created</strong></td>
        <td style="width: 18%"><strong>Created by</strong></td>
        <td style="width: 12%"><strong></strong></td>
    </tr>
</table>

<form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
<table border="0">
    <tr valign="top">
        <td style="width: 30%"><input type='text' value="" name="comment" id="comment" style="width: 98%" /></td>
        <td style="width: 22%"><input type='text' value="" name="work_again" id="work_again" /></td>
        <td style="width: 18%"></td>
        <td style="width: 18%"></td>
        <td style="width: 12%"></td>
    </tr>
    <tr valign="top">
        <td style="width: 30%"></td>
        <td style="width: 22%"><input type="submit" class="btn btn-primary" name="admin_comment" value="Submit" /></td>
        <td style="width: 18%"></td>
        <td style="width: 18%"></td>
        <td style="width: 12%"></td>
    </tr>
</table>
{if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
</form>

<table border="0">
    {if !empty($admin_comments_average)}
    <tr valign="top">
        <td style="width: 30%"></td>
        <td style="width: 22%"><strong>Average: {$admin_comments_average}</strong></td>
        <td style="width: 18%"></td>
        <td style="width: 18%"></td>
        <td style="width: 12%"></td>
    </tr>
    {/if}
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

<hr/>
<table border="0">
    <tr valign="top">
        <td style="width: 30%"><h3>Recognition Program Points Adjustment</h3></td>
        <td style="width: 22%"></td>
        <td style="width: 18%"></td>
        <td style="width: 18%"></td>
        <td style="width: 12%"></td>
    </tr>
    <tr valign="top">
        <td style="width: 30%"><strong>Comment</strong></td>
        <td style="width: 22%"><strong>Recognition points adjustment</strong></td>
        <td style="width: 18%"><strong>Created</strong></td>
        <td style="width: 18%"><strong>Created by</strong></td>
        <td style="width: 12%"><strong></strong></td>
    </tr>
</table>

<form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
<table border="0">
    <tr valign="top">
        <td style="width: 30%"><input type='text' value="" name="comment" id="comment" style="width: 98%" /></td>
        <td style="width: 22%"><input type='text' value="" name="points" id="points" /></td>
        <td style="width: 18%"></td>
        <td style="width: 18%"></td>
        <td style="width: 12%"></td>
    </tr>
    <tr valign="top">
        <td style="width: 30%"></td>
        <td style="width: 22%"><input type="submit" class="btn btn-primary" name="mark_adjust_points" value="Submit" /></td>
        <td style="width: 18%"></td>
        <td style="width: 18%"></td>
        <td style="width: 12%"></td>
    </tr>
</table>
{if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
</form>

<table border="0">
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
<hr/>
{/if}

<p style="margin-bottom:50px;"/>
{if $private_access}
    <div class="page-header">
        <h1>
            {Localisation::getTranslation('user_public_profile_reference_email')} 
            <small>{Localisation::getTranslation('user_public_profile_16')}</small>
            <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}" class="pull-right"> 
                <i class="icon-list-alt icon-white" style="position:relative; right:-30px; top:12px;"></i>
                <input type="submit" class="btn btn-primary" name="referenceRequest" 
                    value="    {Localisation::getTranslation('user_public_profile_request_reference')}" />
                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
            </form>
        </h1>            
    </div>
    <p>{Localisation::getTranslation('user_public_profile_15')}</p>
    {if isset($requestSuccess)}
        <p class="alert alert-success">{Localisation::getTranslation('user_public_profile_reference_request_success')}</p>
    {/if}
    <p style="margin-bottom:50px;"/>
{/if}

{if $private_access || $isSiteAdmin}
    {if !empty($badges)}
        <div class='page-header'>
            <h1>{Localisation::getTranslation('common_badges')}<small> {Localisation::getTranslation('user_public_profile_4')}</small>
                <a href='{urlFor name="badge-list"}' class='pull-right btn btn-primary'>
                    <i class="icon-list icon-white"></i> {Localisation::getTranslation('user_public_profile_list_all_badges')}
                </a>
            </h1>
        </div>

        {foreach $badges as $badge}
                {assign var="user_id" value=$this_user->getId()} 
                    {if $private_access}
                        <form method="post" action="{urlFor name="user-public-profile" options="user_id.$user_id"}" class="pull-right">
                            <i class="icon-fire icon-white" style="position:relative; right:-25px; top:1px;"></i>
                            <input type="hidden" name="badge_id" value="{$badge->getId()}" />
                            <input type="submit" class='btn btn-inverse' name="revokeBadge" value="    {Localisation::getTranslation('user_public_profile_remove_badge')}" 
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
            <p style="margin-bottom:20px;"/>
        {/foreach}
        
        <p style="margin-bottom:50px;"/>
    {/if}

{if $private_access || $isSiteAdmin}
    <div class="page-header">
        <h1>{Localisation::getTranslation('user_public_profile_task_stream_notifications')} <small>{Localisation::getTranslation('user_public_profile_6')}</small>
            <a href="{urlFor name="stream-notification-edit" options="user_id.$user_id"}" class="pull-right btn btn-primary">
                <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('user_public_profile_edit_notifications')}
            </a>
        </h1>
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
    <p style="margin-bottom:50px;"/>
{/if}

<div class="page-header">
    <h1>{Localisation::getTranslation('common_tags')}<small> {Localisation::getTranslation('user_public_profile_8')}</small>
        <a href='{urlFor name='tags-list'}' class="pull-right btn btn-primary">
            <i class="icon-search icon-white"></i> {Localisation::getTranslation('user_public_profile_search_for_tags')}
        </a>
    </h1>
</div>

{if isset($user_tags) && count($user_tags) > 0}
    {foreach $user_tags as $tag}
        <p>
            {assign var="tag_label" value=TemplateHelper::uiCleanseHTML($tag->getLabel())}
            {assign var="tagId" value=$tag->getId()}
            <a class="tag" href="{urlFor name="tag-details" options="id.$tagId"}">
                <span class="label">{$tag_label}</span>
            </a>
        </p>
    {/foreach}
{else}
    <p class="alert alert-info">
        {Localisation::getTranslation('user_public_profile_9')}
    </p>
{/if}
<p style="margin-bottom:50px;"/>

{if isset($user_orgs)}
    {if count($user_orgs) > 0}
        <div class='page-header'>
            <h1>
                {Localisation::getTranslation('common_organisations')} <small>{Localisation::getTranslation('user_public_profile_10')}</small>
                <a href="{urlFor name='org-search'}" class="pull-right btn btn-primary">
                    <i class="icon-search icon-white"></i> {Localisation::getTranslation('common_search_for_organisations')}
                </a>
            </h1>
        </div>

        {foreach $user_orgs as $org}
            <div class="row">
                {assign var="org_id" value=$org->getId()}
                {assign var="user_id" value=$this_user->getId()}
                <div class="span8">
                    <h3>
                        <i class="icon-briefcase"></i>
                        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">{$org->getName()}</a>
                    </h3>
                </div>
                <div class="row">
                    <form method="post" class="pull-right" action="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                        {if $private_access}
                            <i class="icon-fire icon-white" style="position:relative; right:-25px; top:1px;"></i>
                            <input type="hidden" name="org_id" value="{$org_id}" />
                            <input type="submit" class='btn btn-inverse' name="revoke" value="    {Localisation::getTranslation('user_public_profile_leave_organisation')}" 
                                   onclick="return confirm('{Localisation::getTranslation('user_public_profile_11')}')"/>
                        {/if}                      
                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                    </form>
                </div>
                <div class="span8">
                    <p>
                        <strong>About Me</strong><br/>
                        
                        {if $org->getBiography() == ''}
                            {Localisation::getTranslation('org_public_profile_no_biography_listed')}
                        {else}                            
                            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($org->getBiography())}
                        {/if}
                    </p>
                    <p>
                    <strong>{Localisation::getTranslation('common_home_page')}</strong><br/>
                    {if $org->getHomepage() != "https://"}
                        <a target="_blank" href="{$org->getHomepage()}">{$org->getHomepage()}</a>
                    {else}
                        {Localisation::getTranslation('org_public_profile_no_home_page_listed')}
                    {/if}
                    </p>
                </div>
            </div>
            <p style="margin-bottom:20px;"/>
            <hr/>
        {/foreach}
        
        <p style="margin-bottom:50px;"/>
    {/if}
{/if}

{if isset($archivedJobs)}
    {if count($archivedJobs) > 0}
        <div class='page-header'>
            <h1>{Localisation::getTranslation('common_archived_tasks')} <small>{Localisation::getTranslation('user_public_profile_14')}</small>
                {if $private_access}
                    <a href='{urlFor name="archived-tasks" options="page_no.1"}' class='pull-right btn btn-primary'>
                        <i class="icon-list icon-white"></i> {Localisation::getTranslation('user_public_profile_list_all_archived_tasks')}
                    </a>
                {/if}
            </h1>
        </div>

        {foreach $archivedJobs as $job}
            {include file="task/task.profile-display.tpl" task=$job}
        {/foreach}
        <p style="margin-bottom:50px;"/>
    {/if}
{/if}
{/if}

{/if}

{include file='footer.tpl'}
