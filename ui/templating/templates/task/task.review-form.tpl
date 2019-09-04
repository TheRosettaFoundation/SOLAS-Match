<h2>{TemplateHelper::uiCleanseHTML($reviewedTask->getTitle())}</h2>
{if $reviewedTask->getId() != null}
    {assign var="id" value=$reviewedTask->getId()}
    <p>
        {if empty($is_chunked)}
        {sprintf(Localisation::getTranslation('task_review_form_0'), {urlFor name="download-task-latest-version" options="task_id.$id"})}
        {/if}
    </p>
{else}
    {assign var="id" value=$reviewedTask->getProjectId()}
    <p>
        {sprintf(Localisation::getTranslation('task_review_form_1'), {urlFor name="download-project-file" options="project_id.$id"})}
    </p>
{/if}

{if $reviewedTask->getId() != null && (empty($review) || $review->isNewReviewType())}

{if isset($review)}
    {assign var='value' value=$review->getAccuracy()}
    {assign var='readonly' value="data-rateit-readonly=true"}
{else}
    {assign var='value' value=3}
    {assign var='readonly' value=""}
{/if}
<h3>
    Accuracy
    <small>Does the translation communicate the meaning of the original text correctly and precisely? Does it add or omit any information?</small>
</h3>
<p><i>Star rating of 1-5, where 1 = "Poor", 2 = "Needs work", 3 = "Satisfactory", 4 = "Good" and 5 = "Excellent"</i></p>
<div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
        data-rateit-resetable=false id="rateit_accuracy_{$id}" {$readonly}>
</div>

{if isset($review)}
    {assign var='value' value=$review->getFluency()}
    {assign var='readonly' value="data-rateit-readonly=true"}
{else}
    {assign var='value' value=3}
    {assign var='readonly' value=""}
{/if}
<h3>
    Fluency
    <small>Does the translation have standard (correct and generally accepted) spelling, punctuation, and grammar?</small>
</h3>
<p><i>Star rating of 1-5, where 1 = "Poor", 2 = "Needs work", 3 = "Satisfactory", 4 = "Good" and 5 = "Excellent"</i></p>
<div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
        data-rateit-resetable=false id="rateit_fluency_{$id}" {$readonly}>
</div>

{if isset($review)}
    {assign var='value' value=$review->getTerminology()}
    {assign var='readonly' value="data-rateit-readonly=true"}
{else}
    {assign var='value' value=3}
    {assign var='readonly' value=""}
{/if}
<h3>
    Terminology
    <small>Are the keywords and phrases in the translation (especially humanitarian terms) translated accurately? Is the same translation used for each term throughout the text?</small>
</h3>
<p><i>Star rating of 1-5, where 1 = "Poor", 2 = "Needs work", 3 = "Satisfactory", 4 = "Good" and 5 = "Excellent"</i></p>
<div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
        data-rateit-resetable=false id="rateit_terminology_{$id}" {$readonly}>
</div>

{if isset($review)}
    {assign var='value' value=$review->getStyle()}
    {assign var='readonly' value="data-rateit-readonly=true"}
{else}
    {assign var='value' value=3}
    {assign var='readonly' value=""}
{/if}
<h3>
    Style
    <small>Does the translation sound idiomatic (natural) when you read it? Is the style appropriate for the readers (not too formal or too informal)?</small>
</h3>
<p><i>Star rating of 1-5, where 1 = "Poor", 2 = "Needs work", 3 = "Satisfactory", 4 = "Good" and 5 = "Excellent"</i></p>
<div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
        data-rateit-resetable=false id="rateit_style_{$id}" {$readonly}>
</div>

{if isset($review)}
    {assign var='value' value=$review->getDesign()}
    {assign var='readonly' value="data-rateit-readonly=true"}
{else}
    {assign var='value' value=3}
    {assign var='readonly' value=""}
{/if}
<h3>
    Design
    <small>Is the translation formatted appropriately? If it contains tables, images, or other visual elements, are those translated and easy to read and follow?</small>
</h3>
<p><i>Star rating of 1-5, where 1 = "Poor", 2 = "Needs work", 3 = "Satisfactory", 4 = "Good" and 5 = "Excellent"</i></p>
<div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
        data-rateit-resetable=false id="rateit_design_{$id}" {$readonly}>
</div>

{if isset($review)}
{if !is_null($review->getComment())}
<h3>
    General feedback
    <small>You can use the space below to leave comments, suggestions, or congratulations for your colleague on his/her translation. Please remember to be considerate and constructive in your feedback. Use specific examples whenever possible.</small>
</h3>
    <p>{TemplateHelper::uiCleanseHTML($review->getComment())}</p>
{/if}
{else}

<h3>
    General feedback
    <small>You can use the space below to leave comments, suggestions, or congratulations for your colleague on his/her translation. Please remember to be considerate and constructive in your feedback. Use specific examples whenever possible.</small>
</h3>
    <textarea name="comment_{$id}" cols='40' rows='10' style="width: 80%"></textarea>
{/if}

{else}

{if isset($review)}
    {assign var='value' value=$review->getCorrections()}
    {assign var='readonly' value="data-rateit-readonly=true"}
{else}
    {assign var='value' value=3}
    {assign var='readonly' value=""}
{/if}
<h3>
    {Localisation::getTranslation('task_review_form_corrections')} 
    <small>{Localisation::getTranslation('task_review_form_2')}</small>
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
    {Localisation::getTranslation('task_review_form_grammar')} 
    <small>{Localisation::getTranslation('task_review_form_3')}</small>
</h3>
<p><i>{Localisation::getTranslation('task_review_form_rating')}</i></p>
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
    {Localisation::getTranslation('task_review_form_spelling')}
    <small>{Localisation::getTranslation('task_review_form_4')}</small>
</h3>
<p><i>{Localisation::getTranslation('task_review_form_rating')}</i></p>
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
    {Localisation::getTranslation('task_review_form_consistency')}
    <small>{Localisation::getTranslation('task_review_form_5')}</small>
</h3>
<p><i>{Localisation::getTranslation('task_review_form_rating')}</i></p>
<div class="rateit" data-rateit-value="{$value}" data-rateit-step="1" data-rateit-ispreset=true 
        data-rateit-resetable=false id="rateit_consistency_{$id}" {$readonly}>
</div>

{if isset($review)}
{if !is_null($review->getComment())}
<h3>
    {Localisation::getTranslation('task_review_form_comment')}
    <small>{Localisation::getTranslation('task_review_form_6')}</small>
</h3>
    <p>{TemplateHelper::uiCleanseHTML($review->getComment())}</p>
{/if}
{else}

<h3>
    {Localisation::getTranslation('task_review_form_comment')}
    <small>{Localisation::getTranslation('task_review_form_6')}</small>
</h3>
    <textarea name="comment_{$id}" cols='40' rows='10' style="width: 80%"></textarea>
{/if}

{/if}
<hr>