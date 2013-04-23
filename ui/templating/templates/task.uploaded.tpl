{include file="header.tpl"}

    <div class="page-header">
        <h1>Thank you for your submission!</h1>
    </div>

    <div class="alert alert-success">
        <strong>Success</strong> Your file has been uploaded.
    </div>

    {include file="handle-flash-messages.tpl"}

    <section>
            <h1>What now? <small>Give the organisation time to review</small></h1>
            <p>Here's what will now happen:</p>
            <ol>
                <li>Your task <strong>is complete</strong>.</li>
                <li>The <strong>organisation will receive your uploaded work</strong></li>
                {if isset($org_name)}
                    <li>{$org_name}
                {else}
                    <li>The Organisation
                {/if}
                really appreciates being able to make use of your translation. Thanks so much.</li>
            </ol>
    </section>
    <section>    
        {if isset($tip)}
            <hr>
                <p><strong>Correct Translations - They Matter!</strong></p>
                <p>
                    <i>{$tip}</i>
                </p>
            <hr>
        {/if}
    </section>
    <section>
        <p>
            <a href="{urlFor name="home"}" class="btn btn-primary">
                <i class="icon-search icon-white"></i> Find A New Task
            </a>
        </p>
    </section>

{include file="footer.tpl"}
