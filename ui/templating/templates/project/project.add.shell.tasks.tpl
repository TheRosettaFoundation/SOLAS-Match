{include file="header.tpl"}

    <span class="hidden">

        <!-- Parameters... -->
        <div id="siteLocation">{$siteLocation}</div>
        <div id="siteAPI">{$siteAPI}</div>
        <div id="user_id">{$user_id}</div>
        <div id="userIsAdmin">{$isSiteAdmin}</div>

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
                    <th width="25%">Task Type</th>
                    <th width="25%">Unit</th>
                    <th width="25%">Quantity</th>
                    <th width="25%">Target Language</th>
                </thead>
                <tbody>
                    {for $count=0 to 19}
                        <tr>
                            <td>
                                <select name="task_type" id="task_type">
                                    <option value="0"></option>
                                    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                                        {if $ui['shell_task']}
                                            <option value="{$task_type}">{$ui['type_text']}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </td>
                            <td></td>
                            <td></td>
                            <td>
                                <select name="targetLanguage" id="targetLanguage">
                                    <option value="0"></option>
                                    {foreach from=$languages key=codes item=language}
                                        <option value="{$codes}" >{$language}</option>
                                    {/foreach}
                                </select>
                            </td>
                        </tr>
                    {/for}
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
