<h2>{$reviewedTask->getTitle()}</h2>
{if $reviewedTask->getId() != null}
    {assign var="id" value=$reviewedTask->getId()}
    <p>
        {sprintf(Localisation::getTranslation(Strings::TASK_REVIEW_FORM_0), {urlFor name="download-task-latest-version" options="task_id.$id"})}
    </p>
{else}
    {assign var="id" value=$reviewedTask->getProjectId()}
    <p>
        {sprintf(Localisation::getTranslation(Strings::TASK_REVIEW_FORM_1), {urlFor name="download-project-file" options="project_id.$id"})}
    </p>
{/if}

{if isset($review)}
    {assign var='value' value=$review->getCorrections()}
    {assign var='readonly' value="data-rateit-readonly=true"}
{else}
    {assign var='value' value=3}
    {assign var='readonly' value=""}
{/if}
<h3>
    {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_CORRECTIONS)} 
    <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_2)}</small>
</h3>
<p><i>{Localisation::getTranslation("task_review_form_rating")}</i></p>
<div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
        data-rateit-resetable=false id="rateit_corrections_{$id}" {$readonly}>
</div>

{if isset($review)}
    {assign var='value' value=$review->getGrammar()}
    {assign var='readonly' value="data-rateit-readonly=true"}
{else}
    {assign var='value' value=3}
    {assign var='readonly' value=""}
{/if}
<h3>
    {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_GRAMMAR)} 
    <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_3)}</small>
</h3>
<p><i>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_RATING)}</i></p>
<div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
        data-rateit-resetable=false id="rateit_grammar_{$id}" {$readonly}>
</div>

{if isset($review)}
    {assign var='value' value=$review->getSpelling()}
    {assign var='readonly' value="data-rateit-readonly=true"}
{else}
    {assign var='value' value=3}
    {assign var='readonly' value=""}
{/if}
<h3>
    {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_SPELLING)}
    <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_4)}</small>
</h3>
<p><i>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_RATING)}</i></p>
<div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
        data-rateit-resetable=false id="rateit_spelling_{$id}" {$readonly}>
</div>

{if isset($review)}
    {assign var='value' value=$review->getConsistency()}
    {assign var='readonly' value="data-rateit-readonly=true"}
{else}
    {assign var='value' value=3}
    {assign var='readonly' value=""}
{/if}
<h3>
    {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_CONSISTENCY)}
    <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_5)}</small>
</h3>
<p><i>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_RATING)}</i></p>
<div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
        data-rateit-resetable=false id="rateit_consistency_{$id}" {$readonly}>
</div>

{if isset($review)}
	{if !is_null($review->getComment())}
    	<h3>
    		{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_COMMENT)}
        	<small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_6)}</small>
		</h3>
    	<p>{$review->getComment()}</p>
    {/if}
{else}

	<h3>
    	{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_COMMENT)}
        <small>{Localisation::getTranslation(Strings::TASK_REVIEW_FORM_6)}</small>
	</h3>
    <textarea name="comment_{$id}" cols='40' rows='10' style="width: 80%"></textarea>
{/if}
<hr>