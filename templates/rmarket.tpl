{strip}
<div class="six columns">
{if $View == "" && $Remowe == "" && $Buy == ""}
    {$Minfo}.<br />
    <ul>
    <li><a href="{$SCRIPT_NAME}?view=market&amp;lista=id&amp;limit=0">{$Aview}</a></li>
    <li><a href="{$SCRIPT_NAME}?view=szukaj">{$Asearch}</a></li>
    <li><a href="{$SCRIPT_NAME}?view=add">{$Aadd}</a></li>
    <li><a href="{$SCRIPT_NAME}?view=del">{$Adelete}</a></li>
    <li><a href="{$SCRIPT_NAME}?view=all&amp;limit=0">{$Alist}</a></li>
    </ul>
    (<a href="market.php">{$Aback2}</a>)
{/if}

{if $View == "szukaj"}
    {$Sinfo} <a href="imarket.php">{$Aback}</a>{$Sinfo2}<br /><br />
    <form method="post" action="rmarket.php?view=market&amp;limit=0&amp;lista=name"><table>
    <tr><td colspan="2" align="left">{$Item}: <input type="text" name="szukany" /></td></tr>
    <tr><td colspan="2" align="left"><input type="submit" value="{$Asearch}" /></td></tr>
    </table></form>
{/if}

{if $View == "market"}
    {$Viewinfo} <a href="rmarket.php">{$Aback}</a>.<br /><br />
    <table width="100%">
    <tr>
    <td width="41%"><a href="rmarket.php?view=market&amp;lista=name&amp;limit=0"><b><u>{$Tname}</u></b></a></td>
    <td width="10%"><a href="rmarket.php?view=market&amp;lista=power&amp;limit=0"><b><u>{$Tpower}</u></b></a></td>
    <td width="8%"><a href="rmarket.php?view=market&amp;lista=minlev&amp;limit=0"><b><u>{$Tlevel}</u></b></a></td>
    <td width="8%"><a href="rmarket.php?view=market&amp;lista=amount&amp;limit=0"><b><u>{$Tamount}</u></b></a></td>
    <td width="13%"><a href="rmarket.php?view=market&amp;lista=cost&amp;limit=0"><b><u>{$Tcost}</u></b></a></td>
    <td width="10%"><a href="rmarket.php?view=market&amp;lista=owner&amp;limit=0"><b><u>{$Tseller}</u></b></a></td>
    <td width="10%"><b><u>{$Toptions}</u></b></td>
    </tr>
    {section name=item loop=$Name}
        <tr>
        <td>{$Name[item]}</td>
        <td align="center">{$Power[item]}</td>
        <td align="center">{$Minlev[item]}</td>
        <td align="center">{$Amount[item]}</td>
        <td>{$Cost[item]}</td>
        <td><a href="view.php?view={$Owner[item]}">{$Seller[item]}</a></td>
        {$Action[item]}
    {/section}
    </table>
    {$Previous}{$Next}
{/if}

{if $View == "add"}
    {$Addinfo} <a href="rmarket.php">{$Aback}</a>.<br /><br />
    <form method="post" action="rmarket.php?view=add&amp;step=add"><table>
    <tr><td colspan="2">
    {$Item}: <select name="przedmiot">
    {section name=item1 loop=$Name}
        <option value="{$Itemid[item1]}">{$Name[item1]} ({$Iamount}: {$Amount[item1]})</option>
    {/section}</select></td></tr>
    <tr><td>{$Iamount2}:</td><td><input type="text" name="amount" /></td></tr>
    <tr><td>{$Icost}:</td><td><input type="text" name="cost" /></td></tr>
    <tr><td colspan="2" align="center"><input type="submit" value="{$Aadd}" /></td></tr>
    </table></form>
{/if}

{if $Buy != ""}
    {$Buyinfo} <a href="rmarket.php">{$Aback}</a>.<br /><br />
    <b>{$Item}:</b> {$Name} <br />
    <b>{$Ipower}:</b> {$Power} <br />
    <b>{$Oamount}:</b> {$Amount1} <br />
    <b>{$Icost}:</b> {$Cost} <br />
    <b>{$Iseller}:</b> <a href="view.php?view={$Sid}">{$Seller}</a> <br /><br />
    <form method="post" action="rmarket.php?buy={$Itemid}&amp;step=buy"><table>
    <tr><td>{$Bamount}:</td><td><input type="text" name="amount" /></td></tr>
    <tr><td colspan="2" align="center"><input type="submit" value="{$Abuy}" /></td></tr>
    </table></form>
{/if}

{if $View == "all"}
    {$Listinfo}<br />
    <table width="70%">
    <tr>
    <td width="60%"><b><u>{$Iname}</u></b></td><td align="center" width="20%"><b><u>{$Iamount}</u></b></td><td width="20%" align="center"><b><u>{$Iaction}</u></b></td>
    </tr>
    {section name=all loop=$Name}
        <tr>
        <td>{$Name[all]}</td>
        <td align="center">{$Amount[all]}</td>
        <td align="center"><form method="post" action="rmarket.php?view=market&amp;limit=0&amp;lista=id">
            <input type="hidden" name="szukany" value="{$Name[all]}" />
            <input type="submit" value="{$Ashow}" /></form>
        </td>
        </tr>
    {/section}
    </table>
{/if}

{$Message}
{/strip}