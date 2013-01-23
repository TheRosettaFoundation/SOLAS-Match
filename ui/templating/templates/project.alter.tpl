{include file="header.tpl"}

{assign var="project_id" value=$project->getId()}
<h1 class="page-header">
    {$project->getTitle()}
    <small>Alter project details here.</small>
    <a href="{urlFor name="project-view" options="project_id.$project_id"}" class='pull-right btn btn-primary'>
        <i class="icon-list icon-white"></i> View Details
    </a>
</h1>

<h3>Edit Project Details</h3>
<p style="margin-bottom:20px;"></p>
<form method="post" action="{urlFor name="project-alter" options="project_id.$project_id"}" class="well">
    <label for="title">Title:</label>
    <textarea wrap="soft" cols="1" rows="2" name="title">{$project->getTitle()}</textarea>

    <label for="description">Description:</label>
    <textarea wrap="soft" cols="1" rows="6" name="description">{$project->getDescription()}</textarea>
    
    <label for="deadline">Deadline:</label>
    {if isset($deadlineError) && $deadlineError != ''}
        <p class="alert alert-error">{$deadlineError}</p>
    {/if}
    <p>
        Date: <input type="text" id="deadlineDate" name="deadlineDate" value="{$deadlineDate}" />
        Time: <input type="text" name="deadlineTime" value="{$deadlineTime}" />
    </p>

    <label for="source">Source Language</label>
    <select name="sourceLanguage" id="sourceLanguage">
        {foreach $languages as $language}
            {if $project->getSourceLanguageCode() == $language->getCode()}
                <option value="{$language->getCode()}" selected="selected">{$language->getName()}</option>
            {else}
                <option value="{$language->getCode()}">{$language->getName()}</option>
            {/if}
        {/foreach}
    </select>
    {if isset($countries)}
        <select name="sourceCountry" id="sourceCountry">
            {foreach $countries as $country}
                {if $project->getSourceCountryCode() == $country->getCode()}
                    <option value="{$country->getCode()}" selected="selected">{$country->getName()}</option>
                {else}
                    <option value="{$country->getCode()}">{$country->getName()}</option>
                {/if}
            {/foreach}
        </select>
    {/if}
    
    <label for="reference">Reference:</label>
    <input type="text" name="reference" value="{$project->getReference()}" />

    <label for="tags">Tags</label>
    <input type="text" name="tags" id="tags" value="{$tag_list}">
    
    <label for="word_count">Word Count:</label>
    {if isset($wordCountError) && $wordCountError != ''}
        <p class="alert alert-error">{$wordCountError}</p>
    {/if}
    <input type="text" name="word_count" id="word_count" maxlength="6" value="{$project->getWordCount()}">
    <p>
        <button type="submit" value="Submit" name="submit" class="btn btn-primary">
            <i class="icon-refresh icon-white"></i> Update
        </button>
    </p>
</form>


{include file="footer.tpl"}
