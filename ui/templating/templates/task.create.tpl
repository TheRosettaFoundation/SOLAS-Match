{include file="header.tpl"}
<div class="grid_8">
    <div class="page-header">
        <h1>
            Describe your task <small>Provide as much information as possible</small><br>   
            <small>
                Note:
                <font color='red'>*</font>
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
        <fieldset>
            <label for="content">
                <h2>Title: <font color='red'>*</font></h2>
                {if !is_null($titleError)}
                    <div class="alert alert-error" style="width:131px">
                        {$titleError}
                    </div>
                {/if}
            </label>
            <textarea wrap="soft" cols="1" rows="3" name="title">{$task->getTitle()}</textarea>				
            <p style="margin-bottom:30px;"></p>

            <label for="comment"><h2>Task Comment:</h2></label>
            <p>Who and what will be affected by the translation of this task</p>
            <textarea wrap="soft" cols="1" rows="4" name="comment">{$task->getComment()}</textarea>
            <p style="margin-bottom:30px;"></p>

            <p>
                <h2>Source Language:</h2><br>
                <p>
                    {TemplateHelper::languageNameFromCode($project->getSourceLanguageCode())}
                    ({TemplateHelper::countryNameFromCode($project->getSourceCountryCode())})
                </p>
            </p>
            <p>
                <h2>Target Language: <font color='red'>*</font></h2><br>
                <select name="targetLanguage" id="targetLanguage">
                    {foreach $languages as $language}
                        {if $task->getTargetLanguageCode() == $language->getCode()}
                            <option value="{$language->getCode()}" selected="selected">{$language->getName()}</option>
                        {else}
                            <option value="{$language->getCode()}">{$language->getName()}</option>
                        {/if}
                    {/foreach}
                </select>
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
            <p style="margin-bottom:30px;"></p>

            <h2>Task Type</h2>
            <select name="taskType">
                {assign var="taskTypeCount" value=count($taskTypes)}
                {for $id=1 to $taskTypeCount}
                    <option value="{$id}">{$taskTypes[$id]}</option>
                {/for}
            </select>

            <p>
                <label for="word_count">
                <h2>Word Count: <font color='red'>*</font></h2>
                <p class="desc">Approximate, or use a site such as <a href="http://wordcounttool.net/" target="_blank">Word Count Tool</a>.</p>
                {if !is_null($wordCountError)}
                    <div class="alert alert-error" style="width:144px">
                        {$wordCountError}
                    </div>
                {/if}
                </label>  
                <input type="text" name="word_count" id="word_count" maxlength="6" value="{$task->getWordCount()}"/>
            </p>
            <p style="margin-bottom:30px;"></p>

            <h2>Deadline</h2>
            {if $deadlineError != ''}
                <div class="alert alert-error">
                    {$deadlineError}
                </div>
            {/if}
            <p>
                Date: <input name="deadline_date" id="deadline_date" type="text" value="{$deadlineDate}" />
                Time: <input name="deadline_time" type="text" value="{$deadlineTime}" />
            </p>

            <p>
                <h2>Publish Task</h2>
                <input type="checkbox" name="published" checked="true" />
            </p>

            <p>
                <h2>Task Prerequisites</h2>
                <p id="feedback">
                    <input type="hidden" name="selectedList" id="selectedList" value="" />
                    <span>You've selected:</span> <span id="select-result">none</span>.
                </p>
                <ol id="selectable">
                    {foreach $projectTasks as $projectTask}
                        <li class="ui-widget-content" value="{$projectTask->getId()}">{$projectTask->getTitle()}</li>
                    {/foreach}
                </ol>
            </p>

            <button type="submit" value="Submit" name="submit" class="btn btn-primary">
                <i class="icon-upload icon-white"></i> Submit
            </button>
        </fieldset> 
    </form>
</div>
{include file="footer.tpl"}
