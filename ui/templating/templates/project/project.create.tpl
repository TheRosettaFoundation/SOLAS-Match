{include file="header.tpl"}

    <div class="grid_8">
        <div class="page-header">
            <h1>
                {Localisation::getTranslation(Strings::PROJECT_CREATE_CREATE_A_PROJECT)} <small>{Localisation::getTranslation(Strings::PROJECT_CREATE_0)}.</small><br>   
                <small>
                    {Localisation::getTranslation(Strings::COMMON_NOTE)}:
                    <span style="color: red">*</span>
                    {Localisation::getTranslation(Strings::COMMON_DENOTES_A_REQUIRED_FIELD)}.
                </small>
            </h1>
        </div>           

        {if isset($error)}
            <div class="alert alert-error">
                    {$error}
            </div>
        {/if}
    </div>  
    <p style="margin-bottom:20px;"/>
    
    <div class="well">
        <form id="createProjectForm" method="post" enctype="multipart/form-data" action="{$url_project_upload}" onsubmit="return checkWordCount()" accept-charset="utf-8">
            <table id="createProjectTable">
                <tr>
                    <td colspan="2">
                        {if (isset($title_err) || isset($description_err) || isset($wordcount_err) || isset($deadline_err)
                            || isset($impact_err) || isset($targetLanguage_err) || isset($uniqueLanguageCountry_err)
                            || isset($file_upload_err) || isset($file_err))}
                            <div class="alert alert-error">
                                <h3>{Localisation::getTranslation(Strings::COMMON_PLEASE_FILL_IN_ALL_REQUIRED_FIELDS)}:</h3>
                                <ol>
                                    {if isset($title_err)}
                                        <li>{$title_err}</li>
                                    {/if}
                                    {if isset($description_err)}
                                        <li>{$description_err}</li>
                                    {/if}
                                    {if isset($wordcount_err)}
                                        <li>{$wordcount_err}</li>
                                    {/if}
                                    {if isset($deadline_err)}
                                        <li>{$deadline_err}</li>
                                    {/if}
                                    {if isset($impact_err)}
                                        <li>{$impact_err}</li>
                                    {/if}
                                    {if isset($targetLanguage_err)}
                                        <li>{$targetLanguage_err}</li>
                                    {/if}
                                    {if isset($uniqueLanguageCountry_err)}
                                        <li>{$uniqueLanguageCountry_err}</li>
                                    {/if}
                                    {if isset($file_upload_err)}
                                        <li><strong>{Localisation::getTranslation(Strings::PROJECT_CREATE_SOURCE_TEXT)}</strong> - {$file_upload_err}</li>
                                    {/if}
                                    {if isset($file_err)}
                                        <li>{$file_err}</li>
                                    {/if}
                                </ol>
                            </div>                        
                        {/if}
                    </td>
                </tr>
                <tr valign="middle">
                    <td width="493" align="center" valign="middle">
                        <label for="title"><h2>{Localisation::getTranslation(Strings::COMMON_TITLE)}: <span style="color: red">*</span></h2></label>
                        <p class="desc">{Localisation::getTranslation(Strings::PROJECT_CREATE_1)}.</p>
                        <textarea wrap="soft" cols="1" rows="3"name="title" style="width: 400px" >{if isset($project)}{$project->getTitle()}{/if}</textarea>
                        <p style="margin-bottom:40px;"></p>

                        <label for="description"><h2>{Localisation::getTranslation(Strings::COMMON_DESCRIPTION)}: <span style="color: red">*</span></h2></label>
                        <p class="desc">{Localisation::getTranslation(Strings::PROJECT_CREATE_2)}.</p>                    
                        <textarea wrap="soft" cols="1" rows="8" name="description" style="width: 400px">{if isset($project)}{$project->getDescription()}{/if}</textarea>                    
                        <p style="margin-bottom:37.5px;"></p>

                        <label for="impact"><h2>{Localisation::getTranslation(Strings::COMMON_IMPACT)}: <span style="color: red">*</span></h2></label>
                        <p class="desc">{Localisation::getTranslation(Strings::PROJECT_CREATE_3)}.<br/> {Localisation::getTranslation(Strings::PROJECT_CREATE_4)}.</p>
                        <textarea wrap="soft" cols="1" rows="3" name="impact" style="width: 400px">{if isset($project)}{$project->getImpact()}{/if}</textarea>    
                        <p style="margin-bottom:37.5px;"></p>

                        <label for="reference"><h2>{Localisation::getTranslation(Strings::COMMON_REFERENCE)}:</h2></label>
                        <p class="desc">{Localisation::getTranslation(Strings::PROJECT_CREATE_5)}.</p>
                        <input type="text" name="reference" {if isset($project)}value="{$project->getReference()}"{/if} style="width: 400px" />    
                    </td>
                    <td width="493" align="center" valign="middle">    
                        <div style="margin-bottom:25px;">
                            <label for="{$field_name}"><h2>{Localisation::getTranslation(Strings::PROJECT_CREATE_SOURCE_TEXT)}: <span style="color: red">*</span></h2></label>
                            <p class="desc">{Localisation::getTranslation(Strings::PROJECT_CREATE_6)}. {Localisation::getTranslation(Strings::COMMON_MAXIMUM_FILE_SIZE_IS)} <strong>{TemplateHelper::maxFileSizeMB()}MB</strong>.</p>
                            <input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size_bytes}"/>
                            <input type="file" name="{$field_name}" id="{$field_name}" onchange="checkFormat()"/>
                            <input type="hidden" name="organisation_id" value="1"/>
                        </div>
                        <div style="margin-bottom: 25px;">
                        <label for="word_count"><h2>{Localisation::getTranslation(Strings::COMMON_WORD_COUNT)}: <span style="color: red">*</span></h2></label>
                            <p class="desc">{Localisation::getTranslation(Strings::COMMON_APPROXIMATE_OR_USE_A_WEBSITE_SUCH_AS)} 
                                <a href="http://wordcounttool.net/" target="_blank">Word Count Tool</a>.
                            </p>
                            <input type="text" name="word_count" id="word_count" maxlength="6" 
                                    value="{if isset($project)}{$project->getWordCount()}{/if}" style="width: 400px"/>
                        </div>                    
                        <div style="margin-bottom:25px;">                    
                            <label><h2>{Localisation::getTranslation(Strings::COMMON_DEADLINE)}: <span style="color: red">*</span></h2></label>
                            <p class="desc">{Localisation::getTranslation(Strings::PROJECT_CREATE_7)}.</p>
                            <input class="hasDatePicker" type="text" id="deadline" name="deadline" value="{if isset($project)}{$project->getDeadline()}{/if}" style="width: 400px"/>                    
                        </div>
                        <div style="margin-bottom:25px;">
                            <label for="tags"><h2>{Localisation::getTranslation(Strings::COMMON_TAGS)}:</h2></label>
                            <p class="desc">{Localisation::getTranslation(Strings::PROJECT_CREATE_8)}. <br/>{Localisation::getTranslation(Strings::PROJECT_CREATE_SEPARATED_BY)} <strong>spaces</strong>. {Localisation::getTranslation(Strings::PROJECT_CREATE_FOR_MULTIWORD_TAGS_JOINWITHHYPHENS)}.</p>
                            <input id="tags" name="tags" value="{if isset($tagList)}{$tagList}{/if}" style="width: 400px" />
                        </div>
                        <div style="margin-bottom:25px;">
                            <label for="publishtasks"><h2>{Localisation::getTranslation(Strings::PROJECT_CREATE_PUBLISH_TASKS)}:</h2></label>
                            <p class="desc">{Localisation::getTranslation(Strings::COMMON_IF_CHECKED_TASKS_WILL_APPEAR_IN_THE_TASK_STREAM)}.</p>
                            <input type="checkbox" name="publishTasks" value="1" checked="true"/>
                        </div>
                        <div style="margin-bottom:25px;">
                            <label for="trackProject"><h2>{Localisation::getTranslation(Strings::COMMON_TRACK_PROJECT)}:</h2></label>
                            <p class="desc">{Localisation::getTranslation(Strings::PROJECT_CREATE_12)}</p>
                            <input type="checkbox" name="trackProject" value="1" checked="true"/>
                        </div>
                    </td>                    
                </tr>
                <tr>
                    <td colspan="2">
                        <hr/>
                    </td>
                </tr>
                <tr>
                    <td align="left" valign="top">
                        {if isset($languages)}
                            <h2>{Localisation::getTranslation(Strings::COMMON_SOURCE_LANGUAGE)}: <span style="color: red">*</span></h2><br>
                                <select name="sourceLanguage" id="sourceLanguage" style="width: 400px">
                                    
                                    {if isset($project) && $project->hasSourceLocale()}                                    
                                        {foreach $languages as $language} 
                                            {if $language->getCode() == $project->getSourceLocale()->getLanguageCode()}
                                                <option value="{$language->getCode()}" selected>{$language->getName()}</option>
                                            {else}                                           
                                                <option value="{$language->getCode()}">{$language->getName()}</option>
                                            {/if}
                                        {/foreach}
                                    {else}
                                        {foreach $languages as $language} 
                                           <option value="{$language->getCode()}">{$language->getName()}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                                {if isset($countries)}
                                    <select name="sourceCountry" id="sourceCountry" style="width: 400px">
                                        {if isset($project) && $project->hasSourceLocale()}
                                            {foreach $countries as $country}
                                                {if $country->getCode() == $project->getSourceLocale()->getCountryCode()}
                                                    <option value="{$country->getCode()}" selected>{$country->getName()}</option>
                                                {else}
                                                    <option value="{$country->getCode()}">{$country->getName()}</option>
                                                {/if}
                                            {/foreach}     
                                        {else}
                                            {foreach $countries as $country}
                                                 <option value="{$country->getCode()}">{$country->getName()}</option>
                                            {/foreach} 
                                        {/if}
                                    </select>
                                {/if}
                        {else}
                            <label for="source"><h2>{Localisation::getTranslation(Strings::COMMON_SOURCE_LANGUAGE)}: <span style="color: red">*</span></h2></label>
                            <input type="text" name="source" id="source"/>
                            <input type="text" name="sourceCountry" id="source"/>
                        {/if}                     
                    </td>                
                    <td align="center" valign="middle">
                        <h2>{Localisation::getTranslation(Strings::COMMON_TASK_TYPE)}: <span style="color: red">*</span></h2>
                        <p class="desc">{Localisation::getTranslation(Strings::PROJECT_CREATE_9)}.</p>                 
                    </td>
                <tr>
                    <td>                    
                        <h2>{Localisation::getTranslation(Strings::PROJECT_CREATE_TARGET_LANGUAGES)}: <span style="color: red">*</span></h2><br>
                    </td>
                    <td valign="center">
                        <table border="0" width="100%"> 
                            <tr align="center">
                                <td width="33%"><strong>{Localisation::getTranslation(Strings::COMMON_SEGMENTATION)}</strong></td>
                                <td width="33%"><strong>{Localisation::getTranslation(Strings::COMMON_TRANSLATION)}</strong></td>
                                <td width="33%"><strong>{Localisation::getTranslation(Strings::COMMON_PROOFREADING)}</strong></td>
                            </tr> 
                        </table>
                    </td>
                </tr>
                <tr id="targetLanguageTemplate_0">
                    <td> 
                        {if isset($languages)}
                            <select name="targetLanguage_0" id="targetLanguage_0" style="width: 400px">
                                {if !empty($targetLocales)}
                                    {foreach $languages as $language}
                                        {if $targetLocales[0]->getLanguageCode() == $language->getCode()}
                                            <option value="{$language->getCode()}" selected>{$language->getName()}</option>
                                        {else}
                                            <option value="{$language->getCode()}">{$language->getName()}</option>
                                        {/if}
                                    {/foreach}
                                {else}
                                    {foreach $languages as $language}
                                        <option value="{$language->getCode()}">{$language->getName()}</option>
                                    {/foreach}
                                {/if}
                            </select>
                            {if isset($countries)}
                                <select name="targetCountry_0" id="targetCountry_0" style="width: 400px">
                                    {if !empty($targetLocales)}
                                        {foreach $countries as $country}
                                            {if $targetLocales[0]->getCountryCode() == $country->getCode()}
                                                <option value="{$country->getCode()}" selected>{$country->getName()}</option>
                                            {else}
                                                <option value="{$country->getCode()}">{$country->getName()}</option>
                                            {/if}
                                        {/foreach}
                                    {else}
                                        {foreach $countries as $country}
                                            <option value="{$country->getCode()}">{$country->getName()}</option>
                                        {/foreach}
                                    {/if}
                                </select> 
                            {/if}
                        {else}
                            <label for="source"><h2>{Localisation::getTranslation(Strings::COMMON_SOURCE_LANGUAGE)}: <span style="color: red">*</span></h2></label>
                            <input type="text" name="source" id="source"/>
                            <input type="text" name="sourceCountry" id="source"/>
                        {/if} 
                    </td>
                    <td valign="middle">
                        <table> 
                            <tr align="center">
                                <td valign="middle"><input title="{Localisation::getTranslation(Strings::PROJECT_CREATE_10)}." type="checkbox" id="segmentation_0" name="segmentation_0" value="y" onchange="segmentationEnabled(0)" {if !empty($targetLocales) && $targetLocales[0]['segmentation']} checked {/if} /></td>                            
                                <td valign="middle"><input title="{Localisation::getTranslation(Strings::COMMON_CREATE_A_TRANSLATION_TASK_FOR_VOLUNTEER_TRANSLATORS_TO_PICK_UP)}." type="checkbox" id="translation_0" name="translation_0" value="y" {if !empty($targetLocales)} {if $targetLocales[0]['translation']} checked {/if} {else} checked {/if} /></td>
                                <td valign="middle"><input title="{Localisation::getTranslation(Strings::COMMON_CREATE_A_PROOFREADING_TASK_FOR_EVALUATING_THE_TRANSLATION_PROVIDED_BY_A_VOLUNTEER)}." type="checkbox" id="proofreading_0" name="proofreading_0" value="y" {if !empty($targetLocales)} {if $targetLocales[0]['proofreading']} checked {/if} {else} checked {/if} /></td>
                            </tr>                        
                        </table>  
                    </td>
                </tr>
                <tr id="horizontalLine_0">
                    <td colspan="2"><hr/></td>
                </tr>
                {if !empty($targetLocales)}
                    {for $i=1 to (count($targetLocales)-1)}
                    <tr id="targetLanguageTemplate_{$i}">
                        <td> 
                            <select name="targetLanguage_{$i}" id="targetLanguage_{$i}" style="width: 400px">
                                {foreach $languages as $language} 
                                    {if $language->getCode() == $targetLocales[$i]->getLanguageCode()}
                                        <option value="{$language->getCode()}" selected>{$language->getName()}</option>
                                    {else}                                           
                                        <option value="{$language->getCode()}">{$language->getName()}</option>
                                    {/if}
                                {/foreach}
                            </select>

                            <select name="targetCountry_{$i}" id="targetCountry_{$i}" style="width: 400px">
                                {foreach $countries as $country}
                                    {if $country->getCode() == $targetLocales[$i]->getCountryCode()}
                                        <option value="{$country->getCode()}" selected>{$country->getName()}</option>
                                    {else}
                                        <option value="{$country->getCode()}">{$country->getName()}</option>
                                    {/if}
                                {/foreach}     
                            </select> 
                        </td>
                        <td valign="middle">
                            <table> 
                                <tr align="center">
                                    
                                    
                                    <td valign="middle"><input title="{Localisation::getTranslation(Strings::PROJECT_CREATE_10)}." type="checkbox" id="segmentation_{$i}" name="segmentation_{$i}" value="y" onchange="segmentationEnabled({$i})" {if $targetLocales[$i]['segmentation']} checked {/if} /></td>                            
                                    <td valign="middle"><input title="{Localisation::getTranslation(Strings::COMMON_CREATE_A_TRANSLATION_TASK_FOR_VOLUNTEER_TRANSLATORS_TO_PICK_UP)}." type="checkbox" id="translation_{$i}" name="translation_{$i}" value="y" {if $targetLocales[$i]['translation']} checked {/if} /></td>
                                    <td valign="middle"><input title="{Localisation::getTranslation(Strings::COMMON_CREATE_A_PROOFREADING_TASK_FOR_EVALUATING_THE_TRANSLATION_PROVIDED_BY_A_VOLUNTEER)}." type="checkbox" id="proofreading_{$i}" name="proofreading_{$i}" value="y" {if $targetLocales[$i]['proofreading']} checked {/if} /></td>
                                </tr>                        
                            </table>  
                        </td>
                    </tr>
                    <tr id="horizontalLine_{$i}">
                        <td colspan="2"><hr/></td>
                    </tr>
                    {/for}
                {/if}
                <tr>
                    <td colspan="2">
                        <div id="alertinfo" class="alert alert-info" style="display: none; text-align: center">{Localisation::getTranslation(Strings::PROJECT_CREATE_11)}.</div>  

                        <button id="addMoreTargetsBtn" class="btn btn-success" type="button" onclick="addNewTarget()"><i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::PROJECT_CREATE_ADD_MORE_TARGET_LANGUAGES)}</button>
                        <button id="removeBottomTargetBtn" class="btn btn-inverse" type="button" onclick="removeNewTarget()" disabled="true" style="visibility: hidden"><i class="icon-fire icon-white"></i> {Localisation::getTranslation(Strings::COMMON_REMOVE)}</button>
                        <input type="hidden" id="targetLanguageArraySize" name="targetLanguageArraySize" {if !empty($targetLocales)} value="{count($targetLocales)}" {else} value="1" {/if} />
                    </td>
                </tr>                
                <tr>
                    <td colspan="2">
                        <hr/>
                    </td> 
                </tr>
                <tr align="center">
                    <td>
                        <p style="margin-bottom:20px;"/> 
                            <a href='{urlFor name="org-dashboard"}' class='btn btn-danger'>
                                <i class="icon-ban-circle icon-white"></i> {Localisation::getTranslation(Strings::COMMON_CANCEL)}
                            </a>
                        <p style="margin-bottom:20px;"/> 
                    </td>
                    <td>

                        <p style="margin-bottom:20px;"/> 
                        <button type="submit" name="submit" value="createproject" class="btn btn-success">
                                <i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::COMMON_CREATE_PROJECT)}
                            </button>                            
                        <p style="margin-bottom:20px;"/>                     
                    </td>
                </tr>          
            </table>   
        </form>  
    </div>
    
{include file="footer.tpl"}
