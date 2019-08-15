{include file="header.tpl"}

<span class="hidden">

    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
    <div id="siteAPI">{$siteAPI}</div>
    <div id="maxfilesize">{$maxFileSize}</div>
    <div id="imageMaxFileSize">{$imageMaxFileSize}</div>
    <div id="supportedImageFormats">{$supportedImageFormats}</div>
    <div id="project_id">{$project_id}</div>
    <div id="org_id">{$org_id}</div>
    <div id="user_id">{$user_id}</div>
    <div id="deadline_timestamp">{$deadline_timestamp}</div>
    <div id="userIsAdmin">{$userIsAdmin}</div>
</span>

<div class="grid_8">
    <div class="page-header">
        <h1>
            <span style="height: auto; width: 750px; overflow-wrap: break-word; display: inline-block;">
                {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getTitle())}
                <small>{Localisation::getTranslation('project_alter_alter_project_details_here')}</small>
            </span>
            <div class="pull-right">
                {if $userIsAdmin}
                <a href="{urlFor name="archive-project" options="project_id.$project_id|sesskey.{$sesskey}"}" class="pull-right btn btn-danger" onclick="return confirm('{Localisation::getTranslation('org_dashboard_1')}')">
                    <i class="icon-fire icon-white"></i> {Localisation::getTranslation('org_dashboard_archive_project')}
                </a>
                <p style="margin-right: 5px;" />
                {/if}
                <a href="{urlFor name="project-view" options="project_id.$project_id"}" class="pull-right btn btn-primary">
                    <i class="icon-list icon-white"></i> {Localisation::getTranslation('project_alter_view_project_details')}
                </a>
            </div>
        </h1>
    </div>
</div>

<div class="well">

    {if isset($flash['error'])}
        <p class="alert alert-error">
            {$flash['error']}
        </p>
    {/if}

    <div id="placeholder_for_errors_1"></div>

    <form method="post" action="{urlFor name="project-alter" options="project_id.$project_id"}" enctype="multipart/form-data" accept-charset="utf-8">

        <table width="100%">
            <tr align="center">
                <td>
                    <label for="title" style="font-size: large"><strong>{Localisation::getTranslation('common_title')}</strong><span style="color: red">*</span></label>
                    <textarea wrap="soft" cols="1" rows="4" style="width: 400px; margin-bottom: 40px" name="project_title" id="project_title" onblur="checkTitleNotUsed();">{$project->getTitle()|escape:'html':'UTF-8'}</textarea>

                    <label for="description" style="font-size: large"><strong>{Localisation::getTranslation('common_description')}</strong><span style="color: red">*</span></label>
                    <textarea wrap="soft" cols="1" rows="6" style="width: 400px; margin-bottom: 40px" name="project_description" id="project_description">{$project->getDescription()|escape:'html':'UTF-8'}</textarea>

                    <label for="impact" style="font-size: large"><strong>{Localisation::getTranslation('common_impact')}</strong><span style="color: red">*</span></label>
                    <textarea wrap="soft" cols="1" rows="4" style="width: 400px; margin-bottom: 40px" name="project_impact" id="project_impact">{$project->getImpact()|escape:'html':'UTF-8'}</textarea>

                    <label for="wordCountInput" style="font-size: large"><strong>{Localisation::getTranslation('common_word_count')}</strong><span style="color: red">*</span></label>
                    <input type="text" maxlength="6" value="{$project->getWordCount()}" style="width: 400px;  margin-bottom: 20px" name="wordCountInput" id="wordCountInput" {if !$userIsAdmin}disabled{/if} />
                    {if $userIsAdmin}
                    <br />
                    <button onclick="updatewordCount(); return false;" class="btn btn-primary" id="updatewordCountBtn">
                        <i class="icon-refresh icon-white"></i>{Localisation::getTranslation('common_submit')} {Localisation::getTranslation('common_word_count')}
                    </button>
                    {/if}
                    {if $enter_analyse_url}
                    <br /><br />
                    <label for="analyse_url" style="font-size: large"><strong>Enter Analysis URL</strong></label>
                    <input type="text" style="width: 400px;  margin-bottom: 20px" name="analyse_url" id="analyse_url" />
                    {/if}
                </td>
                <td>
                    <label for="tagList" style="font-size: large"><strong>{Localisation::getTranslation('common_tags')}</strong></label>
                    <input type="text" name="tagList" id="tagList" value="{TemplateHelper::uiCleanseHTML($project_tags)}" style="width: 400px;  margin-bottom: 20px" />

                    <div class="projFormInput">
                        <div style="margin-bottom:25px;">
                            <h2>{Localisation::getTranslation('common_deadline')}: <span style="color: red">*</span></h2>
                            <p class="desc">{Localisation::getTranslation('project_create_7')}</p>
                            <p>
                                {Localisation::getTranslation('common_day')}:
                                <select name="selectedDay" id="selectedDay" style="width: 4.0em">
                                </select>
                                {Localisation::getTranslation('common_month')}:
                                <select onchange="selectedMonthChanged()" name="selectedMonth" id="selectedMonth" style="width: 8.0em">
                                    {html_options options=$month_list selected=$selected_month}
                                </select>
                                {Localisation::getTranslation('common_year')}:
                                <select onchange="selectedYearChanged()" name="selectedYear" id="selectedYear" style="width: 5.0em">
                                    {html_options options=$year_list selected=$selected_year}
                                </select>
                                <input type="hidden" name="project_deadline" id="project_deadline" />
                            </p>
                            {Localisation::getTranslation('common_hour')}:
                            <select name="selectedHour" id="selectedHour" style="width: 4.0em">
                                {html_options options=$hour_list selected=$selected_hour}
                            </select>
                            {Localisation::getTranslation('common_minute')}:
                            <select name="selectedMinute" id="selectedMinute" style="width: 4.0em">
                                {html_options options=$minute_list selected=$selected_minute}
                            </select>
                        </div>
                    </div>
                    <p style="margin-bottom:20px;"/>

                    <div style="margin-bottom:20px;">
                        <label for="proj-image" style="font-size: large"><strong>{Localisation::getTranslation('common_project_image')}</strong></label>
                        <div id="proj-image-display">
                            {if $project->getImageUploaded() && $project->getImageApproved()}
                                <img class="project-image" src="{$siteLocation}project/{$project_id}/image"/>
                            {/if}
                            {if $project->getImageUploaded() && !$project->getImageApproved()}
                                {Localisation::getTranslation('common_project_image_not_approved')}
                            {/if}
                            {if !$project->getImageUploaded() && !$project->getImageApproved()}
                                {Localisation::getTranslation('common_project_image_not_uploaded')}
                            {/if}
                        </div>
                        <div>
                            <input type="file" name="projectImageFile" id="projectImageFile" />
                            <p id="image_file_desc"></p>
                        </div>
                        <button onclick="deleteImage(); return false;" class="btn btn-danger" id="deleteImageBtn">
                            <i class="icon-fire icon-white"></i>{Localisation::getTranslation('project_alter_delete_project_image')}
                        </button>
                    </div>
                    <label for="project_reference" style="font-size: large"><strong>{Localisation::getTranslation('common_reference')}</strong></label>
                    <input type="text" name="project_reference" id="project_reference" value="{TemplateHelper::uiCleanseHTML($project->getReference())}" style="width: 400px" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr/>
                </td>
            </tr>
            <tr align="center">
                <td>
                    <div id="loading_warning">
                        <p>{Localisation::getTranslation('common_loading')}</p>
                    </div>

                    <p style="margin-bottom:20px;"/>
                    <a href="{$siteLocation}org/dashboard" class="btn btn-danger">
                        <i class="icon-ban-circle icon-white"></i>
                        {Localisation::getTranslation('common_cancel')}
                    </a>
                    <p style="margin-bottom:20px;"/>
                </td>
                <td>
                    <p style="margin-bottom:20px;"/>
                    <button type="submit" onclick="return validateForm();" class="btn btn-primary">
                        <i class="icon-refresh icon-white"></i>
                        {Localisation::getTranslation('project_alter_update_project_details')}
                    </button>
                    <p style="margin-bottom:20px;"/>
                </td>
            </tr>
        </table>

        <input type="hidden" name="sesskey" value="{$sesskey}" />
    </form>

    <div id="placeholder_for_errors_2"></div>
</div>
    
{include file="footer.tpl"}
