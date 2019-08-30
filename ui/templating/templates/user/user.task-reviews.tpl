{include file="header.tpl"}

<div class="page-header">
    <h1>{TemplateHelper::uiCleanseHTML($task->getTitle())} <small>{Localisation::getTranslation('user_task_reviews_0')}</small></h1>
</div>

<p>
    {Localisation::getTranslation('user_task_reviews_1')}
    {Localisation::getTranslation('user_task_reviews_2')}
    {Localisation::getTranslation('user_task_reviews_3')}
</p>

<div class="well">
    {if !empty($reviews) && count($reviews) == 0}
        <span class="alert alert-info">{Localisation::getTranslation('user_task_reviews_4')}</span>
    {else}
        {assign var="count" value=1}
        {assign var="id" value=$task->getId()}
        {foreach $reviews as $review}
            <h2>
            	{sprintf({Localisation::getTranslation('task_review_count')}, {$count})}
            </h2>
            <p>
                {sprintf({Localisation::getTranslation('task_review_form_0')}, {urlFor name="download-task-latest-version" options="task_id.$id"})}
            </p>

          {if $review->isNewReviewType()}
            <h3>
                Accuracy
                <small>Does the translation communicate the meaning of the original text correctly and precisely? Does it add or omit any information?</small>
            </h3>
            <p>
                <i> Star rating of 1-5, where 1 = "Poor", 2 = "Needs work", 3 = "Satisfactory", 4 = "Good" and 5 = "Excellent"</i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getAccuracy()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                Fluency
                <small>Does the translation have standard (correct and generally accepted) spelling, punctuation, and grammar?</small>
            </h3>
            <p>
                <i> Star rating of 1-5, where 1 = "Poor", 2 = "Needs work", 3 = "Satisfactory", 4 = "Good" and 5 = "Excellent"</i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getFluency()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                Terminology
                <small>Are the keywords and phrases in the translation (especially humanitarian terms) translated accurately? Is the same translation used for each term throughout the text?</small>
            </h3>
            <p>
                <i> Star rating of 1-5, where 1 = "Poor", 2 = "Needs work", 3 = "Satisfactory", 4 = "Good" and 5 = "Excellent"</i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getTerminology()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                Style
                <small>Does the translation sound idiomatic (natural) when you read it? Is the style appropriate for the readers (not too formal or too informal)?</small>
            </h3>
            <p>
                <i> Star rating of 1-5, where 1 = "Poor", 2 = "Needs work", 3 = "Satisfactory", 4 = "Good" and 5 = "Excellent"</i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getStyle()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                Design
                <small>Is the translation formatted appropriately? If it contains tables, images, or other visual elements, are those translated and easy to read and follow?</small>
            </h3>
            <p>
                <i> Star rating of 1-5, where 1 = "Poor", 2 = "Needs work", 3 = "Satisfactory", 4 = "Good" and 5 = "Excellent"</i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getDesign()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                General feedback
                <small>Optional comments, suggestions or congratulations</small>
            </h3>
            {if $review->getComment() != ''}
                <p>{TemplateHelper::uiCleanseHTML($review->getComment())}</p>
            {else}
                <p>No general feedback has been listed.</p>
            {/if}
          {else}
            <h3>
                {Localisation::getTranslation('task_review_form_corrections')} 
                <small>{Localisation::getTranslation('task_review_form_2')}</small>
            </h3>
            <p>
                <i> {Localisation::getTranslation("task_review_form_rating")}</i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getCorrections()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                {Localisation::getTranslation('task_review_form_grammar')} 
                <small>{Localisation::getTranslation('task_review_form_3')}</small>
            </h3>
            <p>
                <i> {Localisation::getTranslation("task_review_form_rating")}</i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getGrammar()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                {Localisation::getTranslation('task_review_form_spelling')}
                <small>{Localisation::getTranslation('task_review_form_4')}</small>
            </h3>
            <p>
                <i> {Localisation::getTranslation("task_review_form_rating")}</i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getSpelling()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                {Localisation::getTranslation('task_review_form_consistency')}
                <small>{Localisation::getTranslation('task_review_form_5')}</small>
            </h3>
            <p>
                <i> {Localisation::getTranslation("task_review_form_rating")}</i>
            </p>
            <div class="rateit" data-rateit-value="{$review->getConsistency()}" data-rateit-step="1" data-rateit-ispreset=true 
                    data-rateit-resetable=false data-rateit-readonly=true>
            </div>

            <h3>
                {Localisation::getTranslation('task_review_form_comment')}
                <small>{Localisation::getTranslation('task_review_form_6')}</small>
            </h3>
            {if $review->getComment() != ''}
                <p>{TemplateHelper::uiCleanseHTML($review->getComment())}</p>
            {else}
                <p>{Localisation::getTranslation('common_no_comment_has_been_listed')}</p>
            {/if}
          {/if}
            {assign var="count" value=($count + 1)}
            <hr />
            <br />
        {/foreach}
    {/if}
</div>

{include file="footer.tpl"}
