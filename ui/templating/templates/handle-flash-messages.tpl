{if isset($flash['error'])}
    <div class="alert alert-error">
        <p><strong>Warning! </strong>{$flash['error']}</p>
    </div>
{/if}

{if isset($flash['info'])}
    <div class="alert alert-info">
        <p><strong>NOTE: </strong>{$flash['info']}</p>
    </div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">
        <p><strong>Congratulations! </strong>{$flash['success']}</p>
    </div>
{/if}

