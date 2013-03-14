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
                {if $type_id == TaskTypeEnum::CHUNKING}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::CHUNKING]}">Chunking Task</span>                                    
                {elseif $type_id == TaskTypeEnum::TRANSLATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation Task
                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading Task
                {elseif $type_id == TaskTypeEnum::POSTEDITING}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::POSTEDITING]}">Postediting Task
                {/if}
            </strong>
        </small>   
        {assign var="task_id" value=$task->getId()}
    </h1>
        
{include file="task.details.tpl"}        

    <div class="well">
        {if isset($flash['Warning'])}
            <div class="alert alert-error">
                <h3>Please fill in all required information:</h3>        
                {$flash['Warning']}
            </div>        
        {/if}
        <form method="post" enctype="multipart/form-data" action="{urlFor name="task-chunking" options="task_id.$task_id"}">
        <input type="hidden" id="totalWordCount" name="totalWordCount" value="{$task->getWordCount()}" />
        <table border="0" width="100%">
            <tbody id="taskChunks">
                <input type="hidden" id="totalWordCount" name="totalWordCount" value="{$task->getWordCount()}" />
                <tr>
                    <td colspan="4">
                        <label for="title">
                            <h2>Chunking:
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
                    <td><strong>Number of chunks:</strong></td>         
                    <td align="center" valign="bottom"><strong>Translation</strong></td>
                    <td align="center" valign="bottom"><strong>Proofreading</strong></td>
                    <td align="center" valign="bottom"><strong>Postediting</strong></td>
                </tr>
                <tr>
                    <td id="chunkingElements"></td>  
                    <td align="center" title="Create a translation task for volunteer translators to pick up." valign="middle">
                        <input type="checkbox" id="translation_0" name="translation_0" value="y" />
                     </td>
                    <td align="center" title="Create a proofreading task for evaluating the translation provided by a volunteer." valign="middle">
                        <input type="checkbox" id="proofreading_0" name="proofreading_0" value="y" />
                    </td>
                    <td align="center" title="Create a postediting task for merging together task chunks created by a chunking task." valign="middle">
                        <input type="checkbox" id="postediting_0" checked="true" name="postediting_0" value="y" disabled />
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                    <hr/>
                    </td>
                </tr>
                <tr id="taskUploadTemplate_0" valign="top">
                    <td colspan="4">
                        <p class="desc">Upload your chunked file. Max file size is 8 MB.</p>
                        <input type="file" name="chunkUpload_0" id="chunkUpload_0"/>
                        <label>Word Count:</label>
                        <input type="text" name="wordCount_0" id="wordCount_0" />
                        <hr/>
                    </td>                
                </tr>
                <tr id="taskUploadTemplate_1" valign="top">
                    <td colspan="4"> 
                        <p class="desc">Upload your chunked file. Max file size is 8 MB.</p>
                        <input type="file" name="chunkUpload_1" id="chunkUpload_1"/>
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
                    <button type="submit" name="createChunking" value="1" class="btn btn-success">
                        <i class="icon-upload icon-white"></i> Submit Chunked Tasks
                    </button>
                </td>
            </tr>                        
        </table>
        </form>
    </div>
        
{include file="footer.tpl"}
