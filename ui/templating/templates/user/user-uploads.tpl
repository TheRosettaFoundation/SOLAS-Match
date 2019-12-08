        $certification_list['ATA']     = ['desc' => 'American Translators Association (ATA) - ATA Certified', 'state' => 0, 'reviewed' => 0];
CREATE TABLE IF NOT EXISTS `UserCertifications` (
  user_id           INT(10) UNSIGNED NOT NULL,
  vid               INT(10) UNSIGNED NOT NULL default 0,
  reviewed          INT(10) UNSIGNED NOT NULL DEFAULT 0,
  certification_key VARCHAR(20)  COLLATE utf8_unicode_ci NOT NULL,
  filename          VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL,
  mimetype          VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL,
  note              TEXT         COLLATE utf8_unicode_ci NOT NULL,
{include file="header.tpl"}

    <span class="hidden">

        <!-- Parameters... -->
        <div id="siteLocation">{$siteLocation}</div>
        <div id="siteAPI">{$siteAPI}</div>
        <div id="maxfilesize">{$maxFileSize}</div>
        <div id="user_id">{$user_id}</div>
    </span>

    <div class="grid_8">
        <div class="page-header">
            <h1>
                {Localisation::getTranslation('project_create_create_a_project')} <small>{Localisation::getTranslation('project_create_0')}</small><br>   
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

        <form method="post" action="{urlFor name="project-create" options="org_id.$org_id"}" enctype="multipart/form-data" accept-charset="utf-8">

            <div id ="projFormTop" class="pull-left">
            <div class="projFormTopBlock">
                <div class="projFormInput">
                    <h2>{Localisation::getTranslation('common_title')}: <span style="color: red">*</span></h2>
                    <p class="desc">{Localisation::getTranslation('project_create_1')}</p>
                    <textarea wrap="soft" cols="1" rows="3" style="width: 400px" name="project_title" id="project_title" onblur="checkTitleNotUsed();"></textarea>
                    <p style="margin-bottom:40px;"></p>
                </div>
            </div>

            <div class="projFormTopBlock">
                <div class="projFormInput">
                    <div style="margin-bottom:25px;">
                        <h2>{Localisation::getTranslation('project_create_source_text')}: <span style="color: red">*</span></h2>
                        <p id="source_text_desc" class="desc"></p>
                        <input type="file" name="projectFile" id="projectFile" />
                    </div>
                </div>
            </div>
        </div>
        <div id ="projFormBottom">
            <div id="loading_warning">
                <p>{Localisation::getTranslation('common_loading')}</p>
            </div>
        </div>
???            </div>

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
                    <button type="submit" onclick="return validateForm();" class="btn btn-success">
                        <i class="icon-upload icon-white"></i> {Localisation::getTranslation('common_create_project')}
                    </button>
                    <p style="margin-bottom:20px;"></p>
                </div>
            </div>
            <input type="hidden" name="sesskey" value="{$sesskey}" />
        </form>
    </div>
    
{include file="footer.tpl"}
