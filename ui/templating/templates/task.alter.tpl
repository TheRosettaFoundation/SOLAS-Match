{include file="header.tpl"}

{assign var="task_id" value=$task->getId()}
<h1 class="page-header">
    Task {$task->getTitle()}
    <small>Alter task details here</small>
    <a href="{urlFor name="task-view" options="task_id.$task_id"}" class='pull-right btn btn-primary'>
        <i class="icon-list icon-white"></i> View Details
    </a>
</h1>

<h3>Edit Task Details</h3>
<p style="margin-bottom:20px;"></p>
<form method="post" action="{urlFor name="task-alter" options="task_id.$task_id"}" class="well">
    <label for="title">Title</label>
    <textarea wrap="soft" cols="1" rows="2" name="title">{$task->getTitle()}</textarea>

    <label for="impact">Task Comment</label>
    <textarea wrap="soft" cols="1" rows="2" name="impact">{$task->getComment()}</textarea>

    <label for="deadline">Deadline</label>
    {if $deadline_error != ''}
        <div class="alert alert-error">
            {$deadline_error}
        </div>
    {/if}
    <p>
        Date: <input name="deadline_date" id="deadline_date" type="text" value="{$deadlineDate}" />
        Time: <input name="deadline_time" type="text" value="{$deadlineTime}" />
    </p>
    
    <p>
        <label>Source Language:</label>
            {TemplateHelper::languageNameFromCode($project->getSourceLanguageCode())}
            ({TemplateHelper::countryNameFromCode($project->getSourceCountryCode())})
    </p>
    <br />

    <label for="target">Target Language</label>
    <select name="target" id="target">
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

    {if !is_null($word_count_err)}
        <div class="alert alert-error">
            {$word_count_err}
        </div>
    {/if} 
    
    <label for="word_count">Word Count</label>
    <input type="text" name="word_count" id="word_count" maxlength="6" value="{$task->getWordCount()}">

    <label for="prerequisites">Task Prerequisites</label>
    <p id="feedback">
        <input type="hidden" name="selectedList" id="selectedList" value="{$hiddenPreReqList}" />
        <span>You've selected:</span> <span id="select-result">none</span>.
    </p>
    <ol id="selectable">
        {foreach $projectTasks as $projectTask}
            {if in_array($projectTask->getId(), $taskPreReqIds)}
                <li class="ui-widget-content ui-selected" value="{$projectTask->getId()}">{$projectTask->getTitle()}</li>
            {else}
                <li class="ui-widget-content" value="{$projectTask->getId()}">{$projectTask->getTitle()}</li>
            {/if}
        {/foreach}
    </ol>

    <p>
        <button type="submit" value="Submit" name="submit" class="btn btn-primary">
            <i class="icon-refresh icon-white"></i> Update
        </button>
    </p>
</form>

{include file="footer.tpl"}
