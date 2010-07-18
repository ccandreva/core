<h2>{gt text="Select installation scope"}</h2>
<form class="z-form" action="install.php?lang={$lang}" method="post">
    <div>
        <input type="hidden" name="action" value="createadmin" />
        <input type="hidden" name="locale" value="{$locale}" />
        <fieldset class="z-linear">
            <legend>{gt text="Select installation"}</legend>
            <div class="z-formrow">
                {installtypes name=installtype}
            </div>
        </fieldset>
        <div class="z-buttons z-formbuttons">
            <input type="submit" value="{gt text="Next"}" class="z-bt-ok" />
        </div>
    </div>
</form>
