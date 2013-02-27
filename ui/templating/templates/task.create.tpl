{include file="header.tpl"}
<div class="grid_8">
    <div class="page-header">
        <h1>
            Create New Task <small>For Project: <strong>{$project->getTitle()}</strong></small><br>   
            <small>
                
                Note:
                <span style="color: red">*</span>
                Denotes required field.
            </small>
        </h1>
    </div>           

    {if isset($error)}
        <div class="alert alert-error">
            {$error}
        </div>
    {/if}

    {assign var="project_id" value=$project->getId()}
    <form method="post" action="{urlFor name="task-create" options="project_id.$project_id"}" class="well">
        <table width="100%">
            <fieldset>
                <tr align="center">
                    <td width="50%">
                        <label for="content">
                            <h2>Title: <span style="color: red">*</span></h2>
                            <p class="desc">Provide a meaningful title for the task.</p>
                            {if !is_null($titleError)}
                                <div class="alert alert-error" style="width:131px">
                                    {$titleError}
                                </div>
                            {/if}
                        </label>
                        <textarea wrap="soft" cols="1" rows="3" name="title">{$task->getTitle()}</textarea>				
                        <p style="margin-bottom:20px;"></p>

                        <label for="comment"><h2>Task Comment:</h2></label>
                        <p>Who and what will be affected by the translation of this task.</p>
                        <textarea wrap="soft" cols="1" rows="4" name="comment">{$task->getComment()}</textarea>
                        <p style="margin-bottom:20px;"></p>

                        <p>
                            <h2>Source Language:</h2><br>
                            <p>
                                {TemplateHelper::languageNameFromCode($project->getSourceLanguageCode())}
                                ({TemplateHelper::countryNameFromCode($project->getSourceCountryCode())})
                            </p>
                        </p>
                        <p style="margin-bottom:20px;"></p>
                        <p>
                            <h2>Target Language: <span style="color: red">*</span></h2><br>
                            <select name="targetLanguage" id="targetLanguage">
                                {foreach $languages as $language}
                                    {if $task->getTargetLanguageCode() == $language->getCode()}
                                        <option value="{$language->getCode()}" selected="selected">{$language->getName()}</option>
                                    {else}
                                        <option value="{$language->getCode()}">{$language->getName()}</option>
                                    {/if}
                                {/foreach}
                            </select>
                        </p>
                        <p>
                            {if isset($countries)}
                                <select name="targetCountry" id="targetCountry">
                                    {foreach $countries as $country}
                                        {if $task->getTargetCountryCode() == $country->getCode()}
                                            <option value="{$country->getCode()}" selected="selected">{$country->getName()}</option>
                                        {else}
                                            <option value="{$country->getCode()}">{$country->getName()}</option>
                                        {/if}
                                    {/foreach}
                                </select> 
                            {/if}
                        </p>
                        <p style="margin-bottom:20px;"></p>
                        <h2>Task Type</h2>
                        <p class="desc">Provide the type of the task.</p>
                        <select name="taskType">
                            {assign var="taskTypeCount" value=count($taskTypes)}
                            {for $id=1 to $taskTypeCount}
                                <option value="{$id}">{$taskTypes[$id]}</option>
                            {/for}
                        </select>
                    </td>
                    <td>
                        <p>
                            <label for="word_count">
                            <h2>Word Count: <span style="color: red">*</span></h2>
                            <p class="desc">Approximate, or use a site such as <a href="http://wordcounttool.net/" target="_blank">Word Count Tool</a>.</p>
                            {if !is_null($wordCountError)}
                                <div class="alert alert-error" style="width:144px">
                                    {$wordCountError}
                                </div>
                            {/if}
                            </label>  
                            <input type="text" name="word_count" id="word_count" maxlength="6" value="{$task->getWordCount()}"/>
                        </p>
                        <p style="margin-bottom:20px;"></p>

                        <h2>Deadline <span style="color: red">*</span></h2>
                        <p class="desc">Provide a deadline by which the task must be completed.</p>
                        {if $deadlineError != ''}
                            <div class="alert alert-error">
                                {$deadlineError}
                            </div>
                        {/if}
                        <p style="margin-bottom:20px;"></p>
                        
                        <p>
                            {assign var="deadlineDateTime" value=$task->getDeadline()}
                            <input class="hasDatePicker" type="text" id="deadline" name="deadline" value="{if isset($deadlineDateTime)} {date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))} {/if}" />
                        </p>

                        <p style="margin-bottom:20px;"></p>

                        <p>
                            <h2>Publish Task</h2>
                            <p class="desc">Doy you want the task to be published on the live task steam.</p>
                            <input type="checkbox" name="published" checked="true" />
                        </p>
                        <p style="margin-bottom:20px;"></p>

                        <p>
                            <h2>Task Prerequisite</h2>
                            <p class="desc">Provide a prerequisite task for this task - if any.</p>
                            <p id="feedback">
                                <input type="hidden" name="selectedList" id="selectedList" value="" />
                                <span>You've selected:</span> <span id="select-result">none</span>.
                            </p>
                            <ol class ="pull-left" id="selectable">
                                {foreach $projectTasks as $projectTask}
                                    <li class="ui-widget-content" style="width: 470px" value="{$projectTask->getId()}">{$projectTask->getTitle()}</li>
                                {/foreach}
                            </ol>
                        </p>                   
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr/>
                    </td> 
                </tr>
                <tr align="center">
                    <td>
                        <p style="margin-bottom:20px;"></p>  
                        <a href='{urlFor name="project-view" options="project_id.$project_id"}' class='btn btn-danger'>
                            <i class="icon-ban-circle icon-white"></i> Cancel
                        </a>
                            <p style="margin-bottom:20px;"></p>  
                    </td>                    
                    <td>
                        <p style="margin-bottom:20px;"></p>  
                        <button type="submit" value="Submit" name="submit" class="btn btn-success">
                            <i class="icon-upload icon-white"></i> Create Task
                        </button>
                        <p style="margin-bottom:20px;"></p>  
                    </td>
                </tr>                
            </fieldset>
        </table>
    </form>
</div>
{include file="footer.tpl"}
