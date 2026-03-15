<div role="tabpanel" class="tab-pane" id="lead_conversas_whats">

    <style>
        .collapse-chat-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .collapse-chat-box div {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .collapse-chat-box img {
            margin-right: 10px;
        }

        .collapse-chat {
            max-height: 500px;
            overflow-y: scroll;
        }
    </style>

    <section id='conversas_whats_lead'>

        <?php
        $session = get_sesson_leads_chat($lead->id);
        if ($session) {
            foreach ($session as $key => $sessions) {

                $device = get_device_token($sessions->session);
                // Get staffid from session result, or fallback to device staffid
                $staffId = isset($sessions->staffid) && $sessions->staffid ? $sessions->staffid : ($device ? $device->staffid : null);
                $staffName = $staffId ? get_staff_full_name($staffId) : ($device ? $device->dev_name : 'Unknown');
                echo "<div type='button' class='collapse-chat-box' data-toggle='collapse' data-target='#{$sessions->session}'>   
                            <div>                                 
                                " . ($staffId ? staff_profile_image($staffId, ['staff-profile-image-small tw-mr-2'], 'small') : '<div class="staff-profile-image-small tw-mr-2" style="width: 32px; height: 32px; border-radius: 50%; background: #ccc; display: inline-block;"></div>') . "
                                <h5>{$staffName}</h5>
                            </div>";
                if ($device) {
                    echo "<div>
                                <span data-toggle='tooltip' data-title='" . _l('contac_chat_open') . "'><a target='_blank' href='" . admin_url("contactcenter/chatsingle/{$device->dev_id}?number={$sessions->phonenumber}") . "'><i class='fa-regular fa-comment'></i></a></span>
                            </div>";
                }

                echo "</div>";
                // echo "<div id='{$sessions->msg_session}' class='collapse collapse-chat'>";
                // echo "<hr>";
                // echo monta_html_chat(get_conversas_leads($sessions->msg_conversation_number, $sessions->msg_session));
                // echo "</div>";
                echo "<hr>";
            }
        }

        ?>
    </section>





</div>

<div role="tabpanel" class="tab-pane" id="agenda_leads">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-reminders-leads dataTable no-footer" id="table_agenda_leads">
                <thead>
                    <tr>
                        <th><?php echo _l('agenda_leads_title'); ?></th>
                        <th><?php echo _l('agenda_leads_description'); ?></th>
                        <th><?php echo _l('agenda_leads_status'); ?></th>
                        <th><?php echo _l('agenda_leads_date'); ?></th>
                        <th><?php echo _l('agenda_leads_staff'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (contactcenter_get_agenda_leads($lead->id) as $agenda_lead) { ?>
                        <tr>
                            <td><?php echo $agenda_lead->title; ?></td>
                            <td><?php echo $agenda_lead->description; ?></td>
                            <td><?php echo get_status_agenda($agenda_lead->status)['nome']; ?></td>
                            <td><?php echo _d($agenda_lead->start); ?></td>
                            <td><?php echo get_staff_full_name($agenda_lead->staff_id); ?></td>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    initDataTableInline("#table_agenda_leads");
</script>