{include file="header.tpl"}
	<div class="grid_8">
                <div class="page-header">
                        <h1>
                            Describe your task <small>Provide as much information as possible</small>                        
                        </h1>
                </div>           

		{if isset($error)}
			<div class="alert alert-error">
				{$error}
			</div>
		{/if}
                
		<form method="post" action="{$url_task_describe}">
			<fieldset>
				<label for="content">
                    <h2>Descriptive Title</h2>
                    {if !is_null($title_error)}
                        <div class="alert alert-error">
                            {$title_error}
                        </div>
                    {/if}
                </label>
                <p class="desc">You may replace the file name with something more descriptive.</p>
                
				<textarea wrap="soft" cols="1" rows="2" name="title">{$task->getTitle()}</textarea>				
                                <p style="margin-bottom:30px;"></p>
                <label for="impact"><h2>Task Impact</h2></label>
                <p>Who and what will be affected by the translation of this task</p>
                <textarea wrap="soft" cols="1" rows="2" name="impact">{$task->getImpact()}</textarea>

                <p style="margin-bottom:30px;"></p>
                <label for="reference"><h2>Context Reference</h2></label>
                <p class="desc">Enter a URL that gives context to this task</p>
                {if $task->getReferencePage() != '' }
                    {assign var="url_text" value=$task->getReferencePage()}
                {else}
                    {assign var="url_text" value="http://"}
                {/if}
                <textarea wrap="soft" cols="1" rows="2" name="reference">{$url_text}</textarea>

                <p style="margin-bottom:30px;"></p>
                {if isset($languages)}
                    <p>
                        <label for="source"><b>From language</b></small></label>
                        <select name="source" id="source">
                            {foreach $languages as $language}
                                    <option value="{$language->getCode()}">{$language->getEnName()}</option>
                            {/foreach}
                        </select>
                        {if isset($countries)}
                            <select name="sourceCountry" id="sourceCountry">
                                {foreach $countries as $country}
                                     <option value="{$country->getCode()}">{$country->getEnName()}</option>
                                {/foreach}                                
                            </select>
                        {/if}
                    </p>
                    <p>
                        <label for="target_0"><b>To language</b></label>
                        <select name="target_0" id="target_0">
                            {foreach $languages as $language}
                                  <option value="{$language->getCode()}">{$language->getEnName()}</option>
                            {/foreach}
                        </select>
                        {if isset($countries)}
                            <select name="targetCountry_0" id="targetCountry">
                                {foreach $countries as $country}
                                    <option value="{$country->getCode()}">{$country->getEnName()}</option>
                                {/foreach}
                            </select> 
                        {/if}
                        <div id="text0"></div>
                        <div id="text1"></div>
                        <div id="text2"></div>
                        <div id="text3"></div>
                        <div id="text4"></div>
                        <div id="text5"></div>
                        <div id="text6"></div>
                        <div id="text7"></div>
                        <div id="text8"></div>
                        <div id="text9"></div>
                        <div id="alertinfo" class="alert alert-info" style="display: none;">You have reached the maximum number of target translation fields allowed.</div>                       
                        <input id="addMoreTargetsBtn" type="button" onclick="addInput()" value="Add More Targets"/>
                        <input id="removeBottomTargetBtn" type="button" onclick="removeInput()" value="Remove" style="visibility: hidden;"/>
                    </p>
                    <p style="margin-bottom:30px;"></p>
                {else}
    				<p>
    					<label for="source">From language</label>
    					<input type="text" name="source" id="source">
                                        <input type="text" name="sourceCountry" id="source">
    				</p>
    				<p>
    					<label for="target">To language</label>
    					<input type="text" name="target" id="target">
                                        <input type="text" name="targetCountry" id="source">
    				</p>
                {/if}
				
				<p>
					<label for="tags"><b>Tags</b></label>
                                        <p class="desc">Separated by spaces. For multiword tags: join-with-hyphens</p>
					<input type="text" name="tags" id="tags">
				</p>
				<p style="margin-bottom:30px;"></p>
				
				<p>
					<label for="word_count">
                        <b>Word count</b>
                        <p class="desc">Approximate, or leave black.</p>
                        {if !is_null($word_count_err)}
                            <div class="alert alert-error">
                                {$word_count_err}
                            </div>
                        {/if}
                    </label>  
					<input type="text" name="word_count" id="word_count" maxlength="6">
				</p>
				  
				
				<button type="submit" value="Submit" name="submit" class="btn btn-primary">
                                    <i class="icon-upload icon-white"></i> Submit
                                </button>
			</fieldset> 
		</form>
	</div>
{include file="footer.tpl"}
