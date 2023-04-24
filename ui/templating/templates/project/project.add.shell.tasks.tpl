{include file="header.tpl"}

    <span class="hidden">

        <!-- Parameters... -->
        <div id="siteLocation">{$siteLocation}</div>
        <div id="siteAPI">{$siteAPI}</div>
        <div id="user_id">{$user_id}</div>
        <div id="userIsAdmin">{$isSiteAdmin}</div>

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
                Add Shell Tasks to a Project<br />
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

        <form method="post" action="{urlFor name="project-add-shell-tasks" options="project_id.$project_id"}" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="create_project_button.disabled = true;">

<table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
list 20() in tables
task type => word count units
optional targte language
word count


    <th width="7%">ID</th>
    <th width="25%">Title</th>
    <th width="24%">Creator</th>
    <th width="25%">Partner</th>
    <th width="11%">Phrase TMS URL</th>
    <th width="8%">Word Count</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td>{$user_row['project_id']}</td>
      <td><a href="{urlFor name="project-view" options="project_id.{$user_row['project_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($user_row['title'])}</a></td>
      {if !empty($user_row['creator_id'])}
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['creator_id']}"}" target="_blank">{$user_row['creator_email']}</a></td>
      {else}
      <td></td>
      {/if}
      <td><a href="{urlFor name="org-public-profile" options="org_id.{$user_row['organisation_id']}"}" target="_blank">{$user_row['name']|escape:'html':'UTF-8'}</a></td>
      <td><a href="https://twbplatform.org/simplesaml/saml2/idp/SSOService.php?spentityid=https://cloud.memsource.com/web/saml2Login/metadata/127330&RelayState=https://cloud.memsource.com/web/project2/show/{$user_row['memsource_project_uid']}" target="_blank">Phrase Project</a></td>
      <td>{if $user_row['word-count'] != 1}{$user_row['word-count']}{else}None{/if}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

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
                        <i class="icon-upload icon-white"></i> Add Shell Tasks to Project
                    </button>
                    <p style="margin-bottom:20px;"></p>
                </div>
            </div>
            <input type="hidden" name="sesskey" value="{$sesskey}" />
        </form>
    </div>

{include file="footer.tpl"}
