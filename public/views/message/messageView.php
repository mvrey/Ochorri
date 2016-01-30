<link rel="stylesheet" type="text/css" href="../../views/message/messageView.css.php" />

<table id="messages_container" class="tablesorter">
    <thead>
        <tr>
            <th class="sorter" onclick="setTimeout('setZebraTable();', 20);">Jugador</th>
            <th class="sorter" onclick="setTimeout('setZebraTable();', 20);">Asunto</th>
            <th class="sorter" onclick="setTimeout('setZebraTable();', 20);">Fecha</th>
            <td>Leído</td>
            <td></td>
        </tr>
        <tr><td colspan="5"><hr/><td></tr>
    </thead>
    <tbody>
<?      $i=0;
        if (count($messages)==0)
            echo "<tr><td colspan='4' style='text-align:center;'>No hay mensajes<td></tr>";
        else
            {
            foreach ($messages as $msg)
                {
                //echo $msg->getContent();
                $sender = $allPlayers[$msg->getFrom()]  ?>

                <tr class="message_title" id="message_row<?=$i?>" onclick="show_selectedMessage(<?=$i?>, <?=$msg->getId()?>, <?=$msg->getRead()?>);">
                    <td><?=$sender->getNick()?></td>
                    <td><?=$msg->getSubject()?></td>
                    <td><?=$msg->getDate()?></td>
                    <td>
                        <img id="message_read_icon<?=$i?>" src="<?=$img_buttons?>done.gif" style="<? if (!$msg->getRead()) echo 'display: none;'?>" />
                    </td>
                    <td class="message_buttons">
                        <img src="<?=$img_buttons?>delete.png" alt="Borrar mensaje" title="Borrar mensaje" onclick="deleteMessage(<?=$i?>, <?=$msg->getId()?>)"/>
                    </td>
                </tr>
                <tr id="content<?=$i?>" class="message_container"><td colspan="5"><?=$msg->getContent()?></td></tr>
    <?          $i++;
                }
            }?>
    </tbody>
</table>


<table id="sendMessage_container">
    <tr>
        <td>
            <label for="message_subject">Asunto</label>
        </td>
        <td colspan="2">
            <input id="message_subject" type="text" size="70"/>
        </td>
    </tr>
    <tr>
        <td>
            <label for="message_content">Mensaje</label>
        </td>
        <td colspan="2">
            <textarea id="message_content" cols="80" rows="5"></textarea>
        </td>
    </tr>
    <tr>
        <td>
            <label for="message_recipient">Destinatario</label>
        </td>
        <td>
            <select id="message_recipient">
            <? foreach ($allPlayers as $recipient)
                { ?>
                <option value="<?=$recipient->getId()?>"><?=$recipient->getNick()?></option>
            <?  } ?>
            </select>
        </td>
        <td>
            <input type="button" id="sendMessage_button" value="Enviar" onclick="sendMessage();"/>
        </td>
    </tr>
</table>