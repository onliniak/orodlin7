{strip}
<div class="six columns">
<p>{$Loginfo}</p>

{if $Send != ""}
    <form method="post" action="log.php?send&amp;step=send">
    {$Sendthis}: <select name="staff">
    {section name=log1 loop=$Name}
        <option value="{$StaffId[log1]}">{$Name[log1]}</option>
    {/section}
    </select><br />
    <input type="hidden" name="lid" value="{$Send}" />
    <input type="submit" value="{$Asend}" /></form>
{/if}

{if $LogId[0] != "0"}
	<table align="center" width="50%">
		<tr>
			{if $Previous != ""}
			<td align="left">{$Previous}</td>
			{/if}
			{if $Next != ""}
			<td align="right">{$Next}</td>
			{/if}
		</tr>
	</table>
    <form method="post" action="log.php?action=delete">
            {section name=log loop=$Date}
                <div class="overflow"><div class="log">
                <div class="eventname">{$Event}</div>
                <table><tr>
                    <td><input id="{$LogId[log]}" type="checkbox" name="{$LogId[log]}" /></td>
                    <td><label for="{$LogId[log]}">
                        <span class="date">{$Edate}: {$Date[log]}</span><br />
                        {$Text[log]}<br />
                        <a href="log.php?send={$LogId[log]}">{$Sendevent}</a>
                    </label></td>
                </tr></table>
                </div></div>
            {/section}
        <br />
        <input type="submit" value="{$Adelete}" />
    </form>
    <form method="post" action="log.php?step=deleteold">
        <input type="submit" value="{$Adeleteold}" /> <select name="oldtime">
            <option value="7">{$Aweek}</option>
            <option value="14">{$A2week}</option>
            <option value="30">{$Amonth}</option>
        </select>
    </form>
    <a href="log.php?akcja=wyczysc">{$Clearlog}</a><br />
{/if}
{/strip}
