{include file="header.tpl"}

<div class="page-header">
    <h1>{$task->getTitle()} <small>{Localisation::getTranslation('user_task_reviews_0')}</small></h1>
</div>

<p>
    {Localisation::getTranslation('user_task_reviews_1')}
    {Localisation::getTranslation('user_task_reviews_2')}
    {Localisation::getTranslation('user_task_reviews_3')}
</p>

<div class="well">
    {if count($reviews) == 0}
        <span class="alert alert-info">{Localisation::getTranslation('user_task_reviews_4')}</span>
    {else}
        {assign var="count" value=1}
        {assign var="id" value=$task->getId()}
        {foreach $reviews as $review}
            <h2>Review #{$count}</h2>
            <p>
                {sprintf({Localisation::getTranslation('task_review_form_0')}, {urlFor name="download-task-latest-version" options="task_id.$id"})}
            </p>

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
                <p>{$review->getComment()}</p>
            {else}
                <p>{Localisation::getTranslation('common_no_comment_has_been_listed')}</p>
            {/if}
            {assign var="count" value=($count + 1)}
            <hr />
            <br />
        {/foreach}
    {/if}
</div>

{include file="footer.tpl"}
