{strip}
<div class="six columns">
{if $Buy == ""}
    {$Towerinfo}<br /><br />
    <ul>
    <li><a href="wieza.php?dalej=T">{$Abuyst}</a></li>
    <li><a href="wieza.php?dalej=C">{$Abuyc}</a></li>
    <li><a href="wieza.php?dalej=P">{$Abuys}</a></li>
    </ul>
    {if $Next != ""}
        <table width="100%">
        {if $Next == "P"}
            <tr>
            <td width="40%"><b><u>{$Tname}</u></b></td>
            <td width="29%"><b><u>{$Tpower}</u></b></td>
            <td width="15%"><b><u>{$Tcost}</u></b></td>
            <td width="8%"><b><u>{$Tlevel}</u></b></td>
            <td width="8%"><b><u>{$Toptions}</u></b></td>
            </tr>
            {section name=tower loop=$Name}
                <tr>
                <td>{$Name[tower]}</td>
                {$Efect[tower]}
                <td>{$Cost[tower]}</td>
                <td>{$Itemlevel[tower]}</td>
                <td> <A href="wieza.php?buy={$Itemid[tower]}&type=S">{$Abuy}</a></td>
                </tr>
            {/section}
        {/if}
        {if $Next != "P"}
            <tr>
           <td width="40%"><b><u>{$Tname}</u></b></td>
            <td width="29%"><b><u>{$Tpower}</u></b></td>
            <td width="15%"><b><u>{$Tcost}</u></b></td>
            <td width="8%"><b><u>{$Tlevel}</u></b></td>
            <td width="8%"><b><u>{$Toptions}</u></b></td>
            </tr>
            {section name=tower1 loop=$Name}
                <tr>
                <td>{$Name[tower1]}</td>
                <td>{$Power[tower1]}</td>
                <td>{$Cost[tower1]}</td>
                <td>{$Itemlevel[tower1]}</td>
                <td> <A href="wieza.php?buy={$Itemid[tower1]}&amp;type=I">{$Abuy}</a></td>
                </tr>
            {/section}
        {/if}
        </table>
    {/if}
{/if}

{if $Buy != ""}
    {$Message}
{/if}
{/strip}