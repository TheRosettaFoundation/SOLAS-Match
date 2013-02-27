{include file="header.tpl"}

{assign var="project_id" value=$project->getId()}
<h1 class="page-header">
    <span style="height: auto; width: 750px; overflow-wrap: break-word; display: inline-block;">
        {$project->getTitle()}
        <small>Alter project details here.</small>
    </span>
    <div class="pull-right">
        <a href="{urlFor name="project-view" options="project_id.$project_id"}" class='pull-right btn btn-primary'>
            <i class="icon-list icon-white"></i> View Project Details
        </a>
    </div>
</h1>
<form method="post" action="{urlFor name="project-alter" options="project_id.$project_id"}" class="well">
    <table width="100%">
        <tr align="center">
            <td>

                <label for="title" style="font-size: large"><strong>Title:</strong></label>
                <textarea wrap="soft" cols="1" rows="4" name="title">{$project->getTitle()}</textarea>
                <p style="margin-bottom:30px;"></p>
                
                <label for="description" style="font-size: large"><strong>Description:</strong></label>
                <textarea wrap="soft" cols="1" rows="6" name="description">{$project->getDescription()}</textarea>
                
                <p style="margin-bottom:30px;"></p>
                <label for="impact" style="font-size: large"><strong>Impact:</strong></label>
                <textarea wrap="soft" cols="1" rows="4" name="impact">{$project->getImpact()}</textarea>
            </td>
            <td>
                <label for="tags" style="font-size: large"><strong>Tags:</strong></label>
                <input id="tags" name="tags" value="{$tag_list}">
                <p style="margin-bottom:20px;"></p>
                
                <label for="deadline" style="font-size: large"><strong>Deadline:</strong></label>
                {if isset($deadlineError) && $deadlineError != ''}
                    <p class="alert alert-error">{$deadlineError}</p>
                {/if}
                <p>
                    <input class="hasDatePicker" type="text" id="deadline" name="deadline" value="{date(Settings::get("ui.date_format"), strtotime($project->getDeadline()))}" />
                </p>
                <p style="margin-bottom:20px;"/>
                
                {if isset($languages)}
                <p>
                    <label for="source" style="font-size: large"><strong>Source Language:</strong></label>
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
                
                {if isset($countries)}
                <p>
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
                
                <label for="reference" style="font-size: large"><strong>Reference:</strong></label>
                <textarea wrap="soft" cols="1" rows="4" name="reference">{$project->getReference()}</textarea>
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
                <a href='{urlFor name="org-dashboard"}' class='btn btn-danger'>
                    <i class="icon-ban-circle icon-white"></i> Cancel
                </a>
                <p style="margin-bottom:20px;"></p>  
            </td>
            <td>
                <p style="margin-bottom:20px;"></p>  
                <button type="submit" value="Submit" name="submit" class="btn btn-primary">
                    <i class="icon-refresh icon-white"></i> Update Project Details
                </button> 
                <p style="margin-bottom:20px;"></p>  
            </td>
        </tr>
    </table>
</form>

{include file="footer.tpl"}
