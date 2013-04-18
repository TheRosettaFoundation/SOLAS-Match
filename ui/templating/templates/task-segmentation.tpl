{include file="header.tpl"}

    <h1 class="page-header">
        {if $task->getTitle() != ''}
            {$task->getTitle()}
        {else}
            Task {$task->getId()}
        {/if}
        <small>
            <strong>
                -
                {assign var="type_id" value=$task->getTaskType()}
                {if $type_id == TaskTypeEnum::SEGMENTATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">Segmentation Task</span>                                    
                {elseif $type_id == TaskTypeEnum::TRANSLATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation Task
                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading Task
                {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">Desegmentation Task
                {/if}
            </strong>
        </small>   
        {assign var="task_id" value=$task->getId()}
    </h1>
        
{include file="task.details.tpl"}

    {if isset($errors)}
        <div class="alert alert-error">
            <h3>Please fill in all required fields:</h3>
            <ol>
                {foreach from=$errors item=error}
                        <li>{$error}</li>
                {/foreach}
            </ol>
        </div>                        
    {/if}
    
    <div class="well">
        <form method="post" enctype="multipart/form-data" action="{urlFor name="task-segmentation" options="task_id.$task_id"}">
        <input type="hidden" id="totalWordCount" name="totalWordCount" value="{$task->getWordCount()}" />
        <table border="0" width="100%">
            <tbody id="taskSegments">
                <input type="hidden" id="totalWordCount" name="totalWordCount" value="{$task->getWordCount()}" />
                <tr>
                    <td colspan="4">
                        <label for="title">
                            <h2>Segmentation:
                                <a href="{urlFor name="task-user-feedback" options="task_id.$task_id"}" style="float: right" class="btn btn-success">
                                    <i class="icon-upload icon-white"></i> Provide Feedback
                                </a>
                            </h2>
                        </label>
                        <p class="desc">Divide large source files into smaller and more managable tasks.<br />
                            Recommended limit of approximately 2000 words or less per task.<br />
                            If you would like to re-download the file click 
                            <a href="{urlFor name="download-task" options="task_id.$task_id"}">here</a>.
                        </p>

                        <hr/>
                    </td>    

                </tr>
                <tr>
                    <td><strong>Number of segments:</strong></td>         
                    <td align="center" valign="bottom"><strong>Translation</strong></td>
                    <td align="center" valign="bottom"><strong>Proofreading</strong></td>
                    <td align="center" valign="bottom"><strong>Desegmentation</strong></td>
                </tr>
                <tr>
                    <td id="segmentationElements"></td>  
                    <td align="center" title="Create a translation task for volunteer translators to pick up." valign="middle">
                        <input type="checkbox" id="translation_0" name="translation_0" value="y" />
                     </td>
                    <td align="center" title="Create a proofreading task for evaluating the translation provided by a volunteer." valign="middle">
                        <input type="checkbox" id="proofreading_0" name="proofreading_0" value="y" />
                    </td>
                    <td align="center" title="Create a desegmentation task for merging together task segments created by a segmentation task." valign="middle">
                        <input type="checkbox" id="desegmentation_0" checked="true" name="desegmentation_0" value="y" disabled />
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                    <hr/>
                    </td>
                </tr>
                <tr id="taskUploadTemplate_0" valign="top">
                    <td colspan="4">
                        <p class="desc">Upload your segmented file. Max file size is 8 MB.</p>
                        <input type="file" name="segmentationUpload_0" id="segmentationUpload_0"/>
                        <label>Word Count:</label>
                        <input type="text" name="wordCount_0" id="wordCount_0" />
                        <hr/>
                    </td>                
                </tr>
                <tr id="taskUploadTemplate_1" valign="top">
                    <td colspan="4"> 
                        <p class="desc">Upload your segmented file. Max file size is 8 MB.</p>
                        <input type="file" name="segmentationUpload_1" id="segmentationUpload_1"/>
                        <label>Word Count:</label>
                        <input type="text" name="wordCount_1" id="wordCount_1" />
                        <hr/>
                    </td>                
                </tr>
            </tbody>    
        </table> 
        <table width="100%">
            <tr>
                <td align="center" colspan="5">
                    <p style="margin-bottom:20px;"></p> 
                    <button type="submit" name="createSegmentation" value="1" class="btn btn-success">
                        <i class="icon-upload icon-white"></i> Submit Segmented Tasks
                    </button>
                </td>
            </tr>                        
        </table>
        </form>
    </div>
        
{include file="footer.tpl"}
