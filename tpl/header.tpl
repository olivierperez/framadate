    <header role="banner">
    {if count($langs)>1}
        <form method="post" action="#">
            <div class="input-group input-group-sm pull-right col-md-2 col-xs-4">
                <select name="lang" class="form-control" title="${_("Select the language")}" >
                {foreach $langs as $lang_key=>$lang_value}
                	<option lang="{$lang_key|truncate:2:''}" selected value="{$lang_key}">{$lang_value}</option>
                {/foreach}
                </select>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default btn-sm" title="{_("Change the language")}">OK</button>
                </span>
            </div>
        </form>
    {/if}

        <h1><a href="{$SERVER_URL}" title="{_("Home")} - {$APPLICATION_NAME}"><img src="{$TITLE_IMAGE}" alt="{$APPLICATION_NAME}"/></a></h1>
        {if !empty($title)}<h2 class="lead"><i>{$title}</i></h2>{/if}
        <hr class="trait" role="presentation" />
    </header>
    <main role="main">