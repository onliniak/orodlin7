{strip}
<div class="six columns">
{if $View == "" && $Remowe == "" && $Buy == ""}
    {$Minfo}.<br />
    <ul>
    <li><a href="{$SCRIPT_NAME}?view=market&amp;lista=id&amp;limit=0">{$Aview}</a>
    <li><a href="{$SCRIPT_NAME}?view=szukaj">{$Asearch}</a></li>
    <li><a href="{$SCRIPT_NAME}?view=add">{$Aadd}</a>
    <li><a href="{$SCRIPT_NAME}?view=del">{$Adelete}</a>
    <li><a href="{$SCRIPT_NAME}?view=all">{$Alist}</a>
    </ul>
    (<a href="market.php">{$Aback2}</a>)
{/if}

{if $View == "szukaj"}
    {$Sinfo} <a href="hmarket.php">{$Aback}</a>. {$Sinfo2}.<br /><br />
    <form method="post" action="hmarket.php?view=market&amp;limit=0&amp;lista=nazwa"><table>
    <tr><td colspan="2">{$Herb}: <input type="text" name="szukany" /></td></tr>
    <tr><td colspan="2" align="center"><input type="submit" value="{$Asearch}" /></td></tr>
    </table></form>
{/if}

{if $View == "market"}
    {$Viewinfo} <a href="hmarket.php">{$Aback}</a>.<br /><br />
    <table width="100%">
    <tr>
    <td width="35%"><a href="hmarket.php?view=market&amp;lista=nazwa&amp;limit=0"><b><u>{$Therb}</u></b></a></td>
    <td width="15%"><a href="hmarket.php?view=market&amp;lista=ilosc&amp;limit=0"><b><u>{$Tamount}</u></b></a></td>
    <td width="15%"><a href="hmarket.php?view=market&amp;lista=cost&amp;limit=0"><b><u>{$Tcost}</u></b></a></td>
    <td width="20%"><a href="hmarket.php?view=market&amp;lista=seller&amp;limit=0"><b><u>{$Tseller}</u></b></a></td>
    <td width="15%"><b><u>{$Toptions}</u></b></td>
    </tr>
    {section name=herb loop=$Name}
        <tr>
        <td>{$Name[herb]}</td>
        <td>{$Amount[herb]}</td>
        <td>{$Cost[herb]}</td>
        <td><a href="view.php?view={$Seller[herb]}">{$User[herb]}</a></td>
        {$Action[herb]}
    {/section}
    </table>
    {$Previous}{$Next}
{/if}

{if $View == "add"}
    {$Addinfo} <a href="hmarket.php">{$Aback}</a>.<br /><br />
    {if $Addofert == "0"}
        <form method="post" action="hmarket.php?view=add&amp;step=add"><table>
        <tr><td>{$Herb}:</td><td><select name="mineral">
            {section name=addherb loop=$Herbname}
                <option value="{$Sqlname[addherb]}">{$Herbname[addherb]} ({$Tamount}: {if $Herbamount[addherb] != ""}{$Herbamount[addherb]}{else}0{/if})</option>
            {/section}
        </select></td></tr>
        <tr><td>{$Hamount}:</td><td><input type="text" name="ilosc" /></td></tr>
        <tr><td>{$Hcost}:</td><td><input type="text" name="cost" /></td></tr>
        <tr><td colspan="2" align="center"><input type="submit" value="{$Aadd}" /></td></tr>
        </table></form>
    {/if}
    {if $Addofert != "0"}
        <form method="post" action="hmarket.php?view=add&amp;step=add">
            {$Youwant}<br />
            <input type="hidden" name="ofert" value="{$Addofert}" />
            <input type="hidden" name="mineral" value="{$Herbname}" />
            <input type="hidden" name="ilosc" value="{$Herbamount}" />
            <input type="hidden" name="cost" value="{$Herbcost}" />
            <input type="submit" value="{$Ayes}" />
        </form>
    {/if}
{/if}

{if $View == "all"}
    {$Listinfo}<br />
    <table width="50%">
    <tr>
    <td width="50%"><b><u>{$Hname}</u></b></td><td align="center" width="25%"><b><u>{$Hamount}</u></b></td><td align="center"  width="25%"><b><u>{$Haction}</u></b></td>
    </tr>
    {section name=all loop=$Name}
        <tr>
        <td>{$Name[all]}</td>
    <td align="center">{$Amount[all]}</td>
    <td align="center"><form method="post" action="hmarket.php?view=market&amp;limit=0&amp;lista=id">
        <input type="hidden" name="szukany" value="{$Name[all]}" />
        <input type="submit" value="{$Ashow}" /></form>
    </td>
    </tr>
    {/section}
    </table>
{/if}

{if $Buy != ""}
    {$Buyinfo} <a href="hmarket.php">{$Aback}</a>.<br /><br />
    <b>{$Bherb}:</b> {$Name} <br />
    <b>{$Oamount}:</b> {$Amount1} <br />
    <b>{$Hcost}:</b> {$Cost} <br />
    <b>{$Hseller}:</b> <a href="view.php?view={$Sid}">{$Seller}</a> <br /><br />
    <table><form method="post" action="hmarket.php?buy={$Itemid}&amp;step=buy">
    <tr><td>{$Bamount}:</td><td><input type="text" name="amount" /></td></tr>
    <tr><td colspan="2" align="center"><input type="submit" value="{$Abuy}" /></td></tr>
    </form></table>
{/if}

{$Message}
{/strip}