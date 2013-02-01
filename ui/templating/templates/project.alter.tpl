{include file="header.tpl"}

{assign var="project_id" value=$project->getId()}
<h1 class="page-header">
    {$project->getTitle()}
    <small>Alter project details here.</small>
    <a href="{urlFor name="project-view" options="project_id.$project_id"}" class='pull-right btn btn-primary'>
        <i class="icon-list icon-white"></i> View Details
    </a>
</h1>
<form method="post" action="{urlFor name="project-alter" options="project_id.$project_id"}" class="well">
    <h3>Edit Project Details</h3>    
    <hr/>
    
    <table width="100%">
        <tr align="center">
            <td>

                <label for="title" style="font-size: large"><b>Title:</b></label>
                <textarea wrap="soft" cols="1" rows="4" name="title">{$project->getTitle()}</textarea>
                <p style="margin-bottom:20px;"></p>
                
                <label for="description" style="font-size: large"><b>Description:</b></label>
                <textarea wrap="soft" cols="1" rows="6" name="description">{$project->getDescription()}</textarea>
                
                <p style="margin-bottom:20px;"></p>
                <label for="tags" style="font-size: large"><b>Tags:</b></label>
                <textarea wrap="soft" cols="1" rows="4" name="tags">{$tag_list}</textarea>


            </td>
            <td>
                <label for="deadline" style="font-size: large"><b>Deadline:</b></label>
                {if isset($deadlineError) && $deadlineError != ''}
                    <p class="alert alert-error">{$deadlineError}</p>
                {/if}
                <p>
                    Date: <input type="text" id="deadlineDate" name="deadlineDate" value="{$deadlineDate}" />
                </p>
                <p>
                    Time: <input type="text" name="deadlineTime" value="{$deadlineTime}" />
                </p>
                <p style="margin-bottom:20px;"></p>
                
                {if isset($languages)}
                <p>
                    <label for="source" style="font-size: large"><b>Source Language:</b></label>
                    <select name="sourceLanguage" id="sourceLanguage">
                        {foreach $languages as $language}
                            {if $project->getSourceLanguageCode() == $language->getCode()}
                                <option value="{$language->getCode()}" selected="selected">{$language->getName()}</option>
                            {else}
                                <option value="{$language->getCode()}">{$language->getName()}</option>
                            {/if}
                        {/foreach}
                    </select>
                </p>
                {/if}
                <p style="margin-bottom:20px;"></p>
                
                {if isset($countries)}
                <p>
                    <label for="source" style="font-size: large"><b>Source Country:</b></label>
                    <select name="sourceCountry" id="sourceCountry">
                        {foreach $countries as $country}
                            {if $project->getSourceCountryCode() == $country->getCode()}
                                <option value="{$country->getCode()}" selected="selected">{$country->getName()}</option>
                            {else}
                                <option value="{$country->getCode()}">{$country->getName()}</option>
                            {/if}
                        {/foreach}
                    </select>
                </p>
                {/if}
                <p style="margin-bottom:20px;"></p>
                
                <label for="reference" style="font-size: large"><b>Reference:</b></label>
                <input type="text" name="reference" value="{$project->getReference()}" />
                <p style="margin-bottom:20px;"></p>
                
                <label for="word_count" style="font-size: large"><b>Word Count:</b></label>
                {if isset($wordCountError) && $wordCountError != ''}
                    <p class="alert alert-error">{$wordCountError}</p>
                {/if} 
                <input type="text" name="word_count" id="word_count" maxlength="6" value="{$project->getWordCount()}"/>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <hr/>
            </td>
        </tr>
        <tr align="center">
            <td>
                <a href='{urlFor name="org-dashboard"}' class='btn btn-danger'>
                    <i class="icon-ban-circle icon-white"></i> Cancel
                </a>
            </td>
            <td>
                <button type="submit" value="Submit" name="submit" class="btn btn-primary">
                    <i class="icon-refresh icon-white"></i> Update
                </button> 
            </td>
        </tr>
    </table>
</form>

{include file="footer.tpl"}
