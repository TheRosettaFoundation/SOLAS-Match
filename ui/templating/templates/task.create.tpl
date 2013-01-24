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
            
            <p class="desc">Title</p>
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
                        <option value="{$language->getCode()}">{$language->getName()}</option>
                    {/foreach}
                </select>
                {if isset($countries)}
                    <select name="targetCountry" id="targetCountry">
                        {foreach $countries as $country}
                            <option value="{$country->getCode()}">{$country->getName()}</option>
                        {/foreach}
                    </select> 
                {/if}
            </p>    
            <p style="margin-bottom:30px;"></p>

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
                <input type="text" name="word_count" id="word_count" maxlength="6">
            </p>
            <p style="margin-bottom:30px;"></p>

            <label for="deadline">Deadline</label>
            {if $deadlineError != ''}
                <div class="alert alert-error">
                    {$deadlineError}
                </div>
            {/if}
            <p>
                Date: <input name="deadline_date" id="deadline_date" type="text" value="{$deadlineDate}" />
                Time: <input name="deadline_time" type="text" value="{$deadlineTime}" />
            </p>

            <label for="published">Publish Task</label>
            <input type="checkbox" name="published" />

            <button type="submit" value="Submit" name="submit" class="btn btn-primary">
                <i class="icon-upload icon-white"></i> Submit
            </button>
        </fieldset> 
    </form>
</div>
{include file="footer.tpl"}
