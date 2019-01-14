<?php $view->extend('MauticCoreBundle:Default:content.html.php'); ?>
<?php $view['slots']->set('headerTitle', $view['translator']->trans('plugin.smsgateway.send.index.title')); ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <?php foreach ($view['session']->getFlash('notice') as $flash_message): ?>
                <div class="alert alert-success alert-dismissible text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong><?php echo $flash_message ?></strong>
                </div>
            <?php endforeach ?>

            <?php echo $view['form']->start($form); ?>
            <?php echo $view['form']->row($form['message']); ?>
            <ul id="counter" class="list-unstyled">
                <li><?php echo $view['translator']->trans('plugin.smsgateway.send.index.form.counter.entered'); ?>: <span id="chars-entered">0</span></li>
                <li><?php echo $view['translator']->trans('plugin.smsgateway.send.index.form.counter.left'); ?>: <span id="chars-left">0</span></li>
                <li><?php echo $view['translator']->trans('plugin.smsgateway.send.index.form.counter.messages'); ?>: <span id="messages-count">0</span></li>
            </ul>
            <table class="table table-striped table-responsive">
                <thead>
                    <tr>
                        <td>Checked</td>
                        <td>First and Last name</td>
                        <td>Phone</td>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($form['contacts']->children as $contact): ?>
                    <tr>
                        <td><?php echo $view['form']->widget($contact); ?></td>
                        <?php $label = explode('|', strip_tags($view['form']->label($contact))); ?>
                        <td><?php echo $label[0]; ?></td>
                        <td><?php echo $label[1]; ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php echo $view['form']->widget($form['messages_count']); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><?php echo $view['form']->row($form['send']); ?></td>
                    </tr>
                </tfoot>
            </table>

            <?php echo $view['form']->end($form); ?>
        </div>
    </div>
</div>
<script src="/plugins/MauticSmsGatewayBundle/Assets/js/jquery.smscharcount.js"></script>
<script>
    jQuery(function($){
        $('textarea#send_sms_message').smsCharCount({
            onUpdate: function(data){
                $('#chars-entered').text(data.charCount);
                $('#chars-left').text(data.charRemaining);
                $('#messages-count').text(data.messageCount);
                $('#send_sms_messages_count').val(data.messageCount);
            }
        });

        $(document).on('click', '#send_sms_send', function(e){
            e.preventDefault();

            if (checkMessage() && checkRecipients()) {
                $('form[name="send_sms"]').submit();
            }
        });

        $('#send_sms_provider_chosen .chosen-single span').bind('DOMSubtreeModified', function() {
            if ($(this).text() === 'Engine') {
                $('#engine-callbackurls').removeClass('hidden').addClass('show');
            } else {
                $('#engine-callbackurls').removeClass('show').addClass('hidden');
            }
        });

        function checkMessage() {
            var message = $('textarea#send_sms_message');

            if (message.val() == '' || message.val() == null) {
                alert("<?php echo $view['translator']->trans('plugin.smsgateway.send.index.form.alert.empty_message'); ?>");
                return false;
            }

            return true;
        }

        function checkRecipients() {
            var recepients = $(('input[type="checkbox"]:checked'));
            
            if (recepients.length === 0) {
                alert("<?php echo $view['translator']->trans('plugin.smsgateway.send.index.form.alert.empty_recipients'); ?>");
                return false;
            }

            return true;
        }
    });
</script>
