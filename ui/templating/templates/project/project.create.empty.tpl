{include file="header.tpl"}

    <span class="hidden">

        <!-- Parameters... -->
        <div id="siteLocation">{$siteLocation}</div>
        <div id="siteAPI">{$siteAPI}</div>
        <div id="imageMaxFileSize">{$imageMaxFileSize}</div>
        <div id="supportedImageFormats">{$supportedImageFormats}</div>
        <div id="org_id">{$org_id}</div>
        <div id="user_id">{$user_id}</div>
        <div id="deadline_timestamp">{$deadline_timestamp}</div>
        <div id="userIsAdmin">{if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}1{else}0{/if}</div>

        <!-- Templates... -->
        <div id="template_language_options">
            <option value="0"></option>
            {foreach from=$languages key=codes item=language}
                <option value="{$codes}" >{$language}</option>
            {/foreach}
        </div>

    </span>

    <div class="grid_8">
        <div class="page-header">
            <h1>
                Create An Empty Project For Shell Tasks <small>{Localisation::getTranslation('project_create_0')}</small><br>   
                <small>
                    {Localisation::getTranslation('common_denotes_a_required_field')}
                </small>
            </h1>
        </div>           
    </div>  

    <div class="well pull-left" style="margin-bottom: 50px">

        {if isset($flash['error'])}
            <p class="alert alert-error">
                {$flash['error']}
            </p>
        {/if}

        <div id="placeholder_for_errors_1"></div>

        <form method="post" action="{urlFor name="project-create-empty" options="org_id.$org_id"}" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="create_project_button.disabled = true;">

            <div id ="projFormTop" class="pull-left">
            <div class="projFormTopBlock">
                <div class="projFormInput">
                    <h2>{Localisation::getTranslation('common_title')}: <span style="color: red">*</span></h2>
                    <p class="desc">{Localisation::getTranslation('project_create_1')}</p>
                    <textarea wrap="soft" cols="1" rows="3" style="width: 400px" name="project_title" id="project_title" onblur="checkTitleNotUsed();"></textarea>
                    <p style="margin-bottom:40px;"></p>
                </div>
                <div class="projFormInput">
                    <h2>{Localisation::getTranslation('common_description')}: <span style="color: red">*</span></h2>
                    <p class="desc">{Localisation::getTranslation('project_create_2')}</p>
                    <textarea wrap="soft" cols="1" rows="8" style="width: 400px" name="project_description" id="project_description"></textarea>
                    <p style="margin-bottom:37.5px;"></p>
                </div>
                <div class="projFormInput">
                    <h2>{Localisation::getTranslation('common_impact')}: <span style="color: red">*</span></h2>
                    <p class="desc">
                        {Localisation::getTranslation('project_create_3')}<br/> {Localisation::getTranslation('project_create_4')}
                    </p>
                    <textarea wrap="soft" cols="1" rows="3" style="width: 400px" name="project_impact" id="project_impact"></textarea>
                    <p style="margin-bottom:37.5px;"></p>
                </div>
                <div class="projFormInput">
                <h2>{Localisation::getTranslation('common_reference')}:</h2>
                <p class="desc">{Localisation::getTranslation('project_create_5')}</p>
                <input type="text" style="width: 400px" name="project_reference" id="project_reference" />
                </div>
            </div>

            <div class="projFormTopBlock">
                <div class="projFormInput">
                    <div style="margin-bottom:25px;">
                        <h2>{Localisation::getTranslation('common_project_image')}</h2>
                        <p id="image_file_desc" class="desc"></p>
                        <p>If you do not upload an image, the most recent will be reused.</p>
                        <input type="file" name="projectImageFile" id="projectImageFile" />
                    </div>
                </div>
                {if false}
                <div class="projFormInput">
                    <div style="margin-bottom:25px;">
                        <h2>This project is part of the emergency response to the Türkiye/Syria earthquake:</h2>
                        <p class="desc">Check if that is the case.</p>
                        <input type="checkbox" name="earthquake" id="earthquake" value="1" />
                    </div>
                </div>
                {else}
                    <input type="hidden" id="earthquake" />
                 {/if}
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
                        <p>
                            {Localisation::getTranslation('common_hour')}:
                            <select name="selectedHour" id="selectedHour" style="width: 4.0em">
                                {html_options options=$hour_list selected=$selected_hour}
                            </select>
                            {Localisation::getTranslation('common_minute')}:
                            <select name="selectedMinute" id="selectedMinute" style="width: 4.0em">
                                {html_options options=$minute_list selected=$selected_minute}
                            </select>
                        </p>
                    </div>
                </div>
                <div class="projFormInput">
                    <div style="margin-bottom:25px;">
                        <h2>{Localisation::getTranslation('common_tags')}:</h2>
                        <p class="desc">
                            {Localisation::getTranslation('project_create_8')}
                            <br />
                            {Localisation::getTranslation('project_create_separated_by')} {Localisation::getTranslation('project_create_seperator')}. 
                            {Localisation::getTranslation('project_create_for_multiword_tags_joinwithhyphens')}
                        </p>
                        <input type="text" name="tagList" id="tagList" style="width: 400px" />
                    </div>
                </div>
            </div>
        </div>
        <div id ="projFormBottom">
            <div id="loading_warning">
                <p>{Localisation::getTranslation('common_loading')}</p>
            </div>
            <div id="projFormBottomBlockLeft">
                <div id="sourceLanguageDiv">
                    <h2>{Localisation::getTranslation('common_source_language')}: <span style="color: red">*</span></h2>
                    <select name="sourceLanguageSelect" id="sourceLanguageSelect" style="width: 400px">
                        <option value="0"></option>
                        {foreach from=$languages key=codes item=language}
                            <option value="{$codes}" >{$language}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>

            <div id="placeholder_for_errors_2"></div>

            <div class="" style="text-align:center; width:100%">
                <div class="pull-left width-50">
                    <p style="margin-bottom:20px;"></p>
                    <a href="{$siteLocation}org/dashboard" class="btn btn-danger">
                        <i class="icon-ban-circle icon-white"></i>
                        {Localisation::getTranslation('common_cancel')}
                    </a>
                    <p style="margin-bottom:20px;"></p>
                </div>
                <div class="pull-left width-50">
                    <p style="margin-bottom:20px;"></p>
                    <button type="submit" onclick="return validateForm();" class="btn btn-success" name="create_project_button" id="create_project_button">
                        <i class="icon-upload icon-white"></i> {Localisation::getTranslation('common_create_project')}
                    </button>
                    <p style="margin-bottom:20px;"></p>
                </div>
            </div>
            <input type="hidden" name="sesskey" value="{$sesskey}" />
        </form>

    </div>

{include file="footer.tpl"}
